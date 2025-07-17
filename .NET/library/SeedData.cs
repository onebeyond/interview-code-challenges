using OneBeyondApi.DataAccess;
using OneBeyondApi.Model;

namespace OneBeyondApi;

public static class SeedData
{
    public static void SetInitialData(LibraryContext context)
    {
        // Clear existing data
        context.Database.EnsureDeleted();
        context.Database.EnsureCreated();

        // Authors
        var ernestMonkjack = new Author { Id = Guid.NewGuid(), Name = "Ernest Monkjack" };
        var sarahKennedy = new Author { Id = Guid.NewGuid(), Name = "Sarah Kennedy" };
        var margaretJones = new Author { Id = Guid.NewGuid(), Name = "Margaret Jones" };

        // Books
        var clayBook = new Book
        {
            Id = Guid.NewGuid(),
            Name = "The Importance of Clay",
            Format = BookFormat.Paperback,
            AuthorId = ernestMonkjack.Id,
            ISBN = "1305718181",
        };

        var agileBook = new Book
        {
            Id = Guid.NewGuid(),
            Name = "Agile Project Management - A Primer",
            Format = BookFormat.Hardback,
            AuthorId = sarahKennedy.Id,
            ISBN = "1293910102",
        };

        var rustBook = new Book
        {
            Id = Guid.NewGuid(),
            Name = "Rust Development Cookbook",
            Format = BookFormat.Paperback,
            AuthorId = margaretJones.Id,
            ISBN = "3134324111",
        };

        // Borrowers
        var daveSmith = new Borrower
        {
            Id = Guid.NewGuid(),
            Name = "Dave Smith",
            EmailAddress = "dave@smithy.com",
        };

        var lianaJames = new Borrower
        {
            Id = Guid.NewGuid(),
            Name = "Liana James",
            EmailAddress = "liana@gmail.com",
        };

        // Book Stocks
        var clayBookCopy1 = new BookStock
        {
            Id = Guid.NewGuid(),
            BookId = clayBook.Id,
            OnLoanToId = daveSmith.Id,
            LoanEndDate = DateTime.Now.Date,
        };

        var clayBookCopy2 = new BookStock
        {
            Id = Guid.NewGuid(),
            BookId = clayBook.Id,
            OnLoanToId = null,
            LoanEndDate = null,
        };

        var agileBookCopy = new BookStock
        {
            Id = Guid.NewGuid(),
            BookId = agileBook.Id,
            OnLoanToId = lianaJames.Id,
            LoanEndDate = DateTime.Now.Date.AddDays(7),
        };

        var rustBookCopy = new BookStock
        {
            Id = Guid.NewGuid(),
            BookId = rustBook.Id,
            OnLoanToId = null,
            LoanEndDate = null,
        };

        // Reservations
        var rustBookReservation = new Reservation
        {
            Id = Guid.NewGuid(),
            BookId = rustBook.Id,
            BorrowerId = daveSmith.Id,
            ReservationDate = DateTime.Now.AddDays(-1),
            Status = ReservationStatus.Pending,
        };

        var overdueFine = new Fine
        {
            Id = Guid.NewGuid(),
            BorrowerId = daveSmith.Id,
            BookStockId = clayBookCopy1.Id,
            Amount = 5.00m, // £5 fine
            RaisedDate = DateTime.Now.AddDays(-3),
            Paid = false,
        };

        context.Authors.AddRange(ernestMonkjack, sarahKennedy, margaretJones);
        context.Books.AddRange(clayBook, agileBook, rustBook);
        context.Borrowers.AddRange(daveSmith, lianaJames);
        context.Catalogue.AddRange(clayBookCopy1, clayBookCopy2, agileBookCopy, rustBookCopy);
        context.Reservations.Add(rustBookReservation);
        context.Fines.Add(overdueFine);

        context.SaveChanges();
    }
}
