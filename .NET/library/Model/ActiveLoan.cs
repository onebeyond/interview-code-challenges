namespace OneBeyondApi.Model;

public class ActiveLoan
{
    public Guid BorrowerId { get; set; }
    public string BorrowerName { get; set; } = null!;
    public string BookTitle { get; set; } = null!;
    public Guid BookStockId { get; set; }
    public DateTime LoanEndDate { get; set; }
}
