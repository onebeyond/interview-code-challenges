namespace OneBeyondApi.Model;

public class Reservation
{
    public Guid Id { get; set; }
    public Guid BookId { get; set; }
    public Guid? BookStockId { get; set; }
    public BookStock? BookStock { get; set; }
    public Guid? BorrowerId { get; set; }
    public Borrower? Borrower { get; set; }
    public DateTime ReservationDate { get; set; }
    public DateTime? NotifyBefore { get; set; }
    public ReservationStatus Status { get; set; }
}
