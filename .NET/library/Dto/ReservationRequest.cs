namespace OneBeyondApi.Dto;

public class ReservationRequest
{
    public Guid BookId { get; set; }
    public Guid BorrowerId { get; set; }
}