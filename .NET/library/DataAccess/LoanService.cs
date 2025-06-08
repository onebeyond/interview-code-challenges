using Microsoft.EntityFrameworkCore;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess
{
    public class LoanService : ILoanService
    {
        int _finePerDay = 0;
        int _defaultLoanDays = 0;

        /// <summary>
        /// Constructor
        /// </summary>
        /// <param name="finePerDay">Default value => 50, but can change in DI</param>
        /// <param name="defaultLoanDays">Default value => 5, but can change in DI</param>
        public LoanService(int finePerDay = 50, int defaultLoanDays = 5)
        {
            _finePerDay = finePerDay;
            _defaultLoanDays = defaultLoanDays;
        }

        /// <summary>
        /// Get all currently loaned book grouped by the Borrowers
        /// </summary>
        /// <returns>
        /// A Grouped list of Books by the borrowers (the full details of the borrowers)
        /// and the books they loaned with the stock id (to able to return it) and the title of the book
        /// </returns>
        public List<OnLoanModel> GetAllLoaned()
        {
            using var context = new LibraryContext();

            var list = context.Catalogue
                .Include(x => x.Book)
                .Include(x => x.OnLoanTo)
                .Where(x => x.OnLoanTo != null)
                .GroupBy(x => x.OnLoanTo)
                .Select(x => new OnLoanModel()
                {
                    Borrower = x.Key,
                    Books = x.Select(b => new LoanedBook
                    {
                        BookStockId = b.Id,
                        Title = b.Book.Name
                    }).ToList()
                }).ToList();

            return list;
        }

        /// <summary>
        /// Returns a borrowed book. Set the bookstock loandata to null
        /// </summary>
        /// <param name="bookStockId">The returned bookstock id</param>
        /// <returns>
        /// An empty guid if the return not succeded and the returned
        /// bookstock id if the returns succseeded
        /// </returns>
        public Guid ReturnBook(Guid bookStockId)
        {
            Guid retVal = Guid.Empty;
            using var context = new LibraryContext();

            var book = context.Catalogue.Include(x => x.OnLoanTo).FirstOrDefault(x => x.Id == bookStockId);

            if (book != null && book.OnLoanTo != null && book.LoanEndDate != null)
            {
                //If the book returned late => add fine
                var lateDays = (DateTime.Today - book.LoanEndDate.Value).Days;
                if (lateDays > 0)
                {
                    context.Fines.Add(GetFine(book.OnLoanTo, lateDays));
                }

                book.LoanEndDate = null;
                book.OnLoanTo = null;
                if (context.SaveChanges() != 0)
                {
                    retVal = book.Id;
                }
            }

            return retVal;
        }

        /// <summary>
        /// Reserve a book or loan it if available
        /// </summary>
        /// <param name="bookStockId"></param>
        /// <returns></returns>
        public Guid ReserveBook(Guid bookStockId, Guid borrowerId)
        {
            Guid retVal = Guid.Empty;
            using var context = new LibraryContext();

            var bookStock = context.Catalogue
                .Include(x => x.Book).Include(x => x.OnLoanTo)
                .FirstOrDefault(x => x.Id == bookStockId);

            var borrower = context.Borrowers.Find(borrowerId);

            if (bookStock != null && borrower != null)
            {
                //If the book is on Loan then reserve the first available date or else borrow now 
                if (bookStock.LoanEndDate.HasValue)
                {
                    var lastReserve = context.Loans.Where(x => x.BookStock.Id == bookStockId)
                        .OrderByDescending(x => x.LoanEndDate).FirstOrDefault();

                    context.Loans.Add(AddLoanReserve(lastReserve, borrower, bookStock));
                }
                else
                {
                    bookStock.LoanEndDate = DateTime.Now.AddDays(_defaultLoanDays);
                    bookStock.OnLoanTo = borrower;
                }

                context.SaveChanges();
                retVal = bookStockId;
            }

            return retVal;
        }

        /// <summary>
        /// Get the first loan date for a book
        /// </summary>
        /// <param name="bookId">The id of the needed book</param>
        /// <param name="bookTitle">The title of the needed book</param>
        /// <returns>Returns the first available date when a stock from a book is loanable, 
        /// if there is no stock for this book than returns DateTime.MaxValue
        /// </returns>
        public DateTime GetFirstAvailableDate(Guid? bookId, string bookTitle)
        {
            DateTime firstAvailableDate = DateTime.MaxValue;
            using var context = new LibraryContext();
            //Get all stock from a book
            var bookStocks = context.Catalogue
                .Include(x => x.Book)
                .Where(x => bookId != null ? x.Book.Id == bookId : x.Book.Name == bookTitle)
                .ToList();

            //If one of the bookstock is available than the book is loanable today
            if (bookStocks.Any(x => x.LoanEndDate == null))
            {
                firstAvailableDate = DateTime.Now;
            }
            else
            {
                //Get the first available date from the reservations grouped by bookstocks
                var bookStockIds = bookStocks.Select(x => x.Id).ToList();
                var firstAvailableDatesFromReservations = context.Loans.Include(x => x.BookStock)
                    .Where(x => bookStockIds.Contains(x.BookStock.Id))
                    .GroupBy(x => x.BookStock).Select(x => x.OrderByDescending(d => d.LoanEndDate).First()).ToList();

                //If there is any reservation
                if (firstAvailableDatesFromReservations.Count != 0)
                {
                    //iterate through the booksStocks to find the first available date
                    foreach (var bookStock in bookStocks)
                    {
                        //if there is reservations for the bookStock than get the earliest availability
                        //otherwise get the current loan end date
                        var bookStockFirstAvailableDate = firstAvailableDatesFromReservations
                            .FirstOrDefault(x => x.BookStock.Id == bookStock.Id)?.LoanEndDate ?? bookStock.LoanEndDate.Value;

                        //if the earliest available reservation is earlier than the current eariliest than choose that value for the earliest date
                        firstAvailableDate = firstAvailableDate < bookStockFirstAvailableDate ? firstAvailableDate : bookStockFirstAvailableDate;
                    }
                }
                else 
                {
                    //The first available date from the current loans, it always has value
                    firstAvailableDate = bookStocks.OrderBy(x => x.LoanEndDate).First().LoanEndDate.Value; 
                }
            }

            return firstAvailableDate;
        }

        private Loan AddLoanReserve(Loan lastReserve, Borrower borrower, BookStock bookStock)
        {
            DateTime loanStart = lastReserve != null ? 
                lastReserve.LoanEndDate : bookStock.LoanEndDate.Value;

            Loan loan = new()
            {
                Id = bookStock.Id,
                Borrower = borrower,
                BookStock = bookStock,
                LoanStartDate = loanStart,
                LoanEndDate = loanStart.AddDays(_defaultLoanDays)

            };

            return loan;
        }

        private Fine GetFine(Borrower borrower, int lateDays)
        {
            Fine fine = new()
            {
                Borrower = borrower,
                PaidDate = DateTime.Now,
                Amount = _finePerDay * lateDays
            };

            return fine;
        }
    }
}
