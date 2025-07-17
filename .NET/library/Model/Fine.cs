namespace OneBeyondApi.Model;

public class Fine
{
    public Guid Id { get; set; }
    public Guid BorrowerId { get; set; }
    public Guid BookStockId { get; set; }
    public decimal Amount { get; set; }
    public DateTime RaisedDate { get; set; }
    public bool Paid { get; set; }
}
