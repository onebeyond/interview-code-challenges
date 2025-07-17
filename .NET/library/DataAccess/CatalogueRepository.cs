using Microsoft.EntityFrameworkCore;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class CatalogueRepository(LibraryContext context, IReservationRepository reservationRepository) : ICatalogueRepository
{
    private const decimal FinePerDay = 1.00m;

    public List<BookStock> GetCatalogue()
    {
        return context.Catalogue
            .Include(x => x.Book)
            .ThenInclude(x => x.Author)
            .Include(x => x.OnLoanTo)
            .ToList();
    }

    public List<BookStock> SearchCatalogue(CatalogueSearch search)
    {
        var list = context.Catalogue
            .Include(x => x.Book)
            .ThenInclude(x => x.Author)
            .Include(x => x.OnLoanTo)
            .AsQueryable();

            if (!string.IsNullOrEmpty(search.Author))
            {
                list = list.Where(x => x.Book.Author.Name.Contains(search.Author));
            }
            if (!string.IsNullOrEmpty(search.BookName))
            {
                list = list.Where(x => x.Book.Name.Contains(search.BookName));
            }

        return list.ToList();
    }

    public List<ActiveLoan> GetActiveLoans()
    {
        return context.Catalogue
            .Where(bs => bs.OnLoanToId != null)
            .Include(bs => bs.Book)
            .Include(bs => bs.OnLoanTo)
            .Select(bs => new ActiveLoan
            {
                BorrowerId = bs.OnLoanToId!.Value,
                BorrowerName = bs.OnLoanTo!.Name,
                BookTitle = bs.Book.Name,
                BookStockId = bs.Id,
                LoanEndDate = bs.LoanEndDate!.Value,
            })
            .ToList();
    }

    public (bool success, Fine? fine) ReturnBook(Guid bookStockId)
    {
        var bookStock = context.Catalogue
            .Include(bs => bs.Book)
            .FirstOrDefault(bs => bs.Id == bookStockId && bs.OnLoanToId != null);

        if (bookStock == null)
            return (false, null);

        // Check for overdue
        Fine? fine = null;
        if (bookStock.LoanEndDate < DateTime.UtcNow)
        {
            fine = new Fine
            {
                Id = Guid.NewGuid(),
                BorrowerId = bookStock.OnLoanToId!.Value,
                BookStockId = bookStockId,
                Amount = CalculateFine(bookStock.LoanEndDate.Value),
                RaisedDate = DateTime.UtcNow,
                Paid = false,
            };
        }

        // Free up the book
        bookStock.OnLoanToId = null;
        bookStock.LoanEndDate = null;

        // Update reservations for this book title
        var nextReservation = context.Reservations
            .Where(r => r.BookId == bookStock.BookId &&
                r.Status == ReservationStatus.Pending)
            .OrderBy(r => r.ReservationDate)
            .FirstOrDefault();

        if (nextReservation != null)
        {
            nextReservation.BookStockId = bookStockId;
            nextReservation.Status = ReservationStatus.Available;
            nextReservation.NotifyBefore = DateTime.UtcNow.AddDays(2);
            reservationRepository.UpdateReservation(nextReservation);
        }

        context.SaveChanges();
        return (true, fine);
    }

    public (bool success, string message) FulfillReservation(Guid reservationId)
    {
        //TODO Use result pattern or similar to return success/failure with message

        var reservation = reservationRepository.GetReservationById(reservationId);
        if (reservation is not { Status: ReservationStatus.Available })
            return (false, "Valid reservation not found");

        if (reservation.NotifyBefore < DateTime.UtcNow)
            return (false, "Reservation expired");

        if (reservation.BookStockId == null)
            return (false, "No book assigned to reservation");

        var bookStock = context.Catalogue
            .FirstOrDefault(bs => bs.Id == reservation.BookStockId &&
                bs.OnLoanToId == null);

        if (bookStock == null)
            return (false, "Book no longer available");

        // Loan out the book
        bookStock.OnLoanToId = reservation.BorrowerId;
        bookStock.LoanEndDate = DateTime.UtcNow.AddDays(14);
        reservation.Status = ReservationStatus.Completed;
        reservationRepository.UpdateReservation(reservation);

        context.SaveChanges();
        return (true, "Reservation fulfilled");
    }

    public DateTime? GetBookAvailability(Guid bookId, Guid borrowerId)
    {
        // Check if borrower has active reservation
        var borrowerReservation = context.Reservations
            .FirstOrDefault(r =>
                r.BookId == bookId &&
                r.BorrowerId == borrowerId &&
                r.Status == ReservationStatus.Available);

        if (borrowerReservation != null)
            return DateTime.UtcNow;

        // Check available copies
        var availableCopy = context.Catalogue
            .FirstOrDefault(bs =>
                bs.BookId == bookId &&
                bs.OnLoanToId == null);

        if (availableCopy != null)
            return DateTime.UtcNow;

        // Check earliest return date
        return context.Catalogue
            .Where(bs =>
                bs.BookId == bookId &&
                bs.LoanEndDate != null)
            .OrderBy(bs => bs.LoanEndDate)
            .Select(bs => bs.LoanEndDate)
            .FirstOrDefault();
    }

    private static decimal CalculateFine(DateTime dueDate)
    {
        var daysOverdue = (DateTime.UtcNow - dueDate).Days;
        return daysOverdue * FinePerDay;
    }
}