using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess
{
    public interface ILoanService
    {
        List<OnLoanModel> GetAllLoaned();
        Guid ReturnBook(Guid bookStockId);
        Guid ReserveBook(Guid bookStockId, Guid borrowerId);
        DateTime GetFirstAvailableDate(Guid? bookId, string bookTitle);
    }
}
