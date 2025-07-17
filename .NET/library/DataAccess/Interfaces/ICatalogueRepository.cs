using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess.Interfaces;

public interface ICatalogueRepository
{
    List<BookStock> GetCatalogue();
    List<BookStock> SearchCatalogue(CatalogueSearch search);
    List<ActiveLoan> GetActiveLoans();
    (bool success, Fine? fine) ReturnBook(Guid bookStockId);
    (bool success, string message) FulfillReservation(Guid reservationId);
    DateTime? GetBookAvailability(Guid bookId, Guid borrowerId);
}