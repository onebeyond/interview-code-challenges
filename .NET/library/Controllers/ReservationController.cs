using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Dto;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers;
[ApiController]
[Route("[controller]")]
public class ReservationController(ICatalogueRepository catalogueRepository, IReservationRepository reservationRepository) : ControllerBase
{
    [HttpPost("Reserve")]
    public IActionResult ReserveBook([FromBody] ReservationRequest request)
    {
        var reservation = new Reservation
        {
            BookId = request.BookId,
            BorrowerId = request.BorrowerId,
            ReservationDate = DateTime.UtcNow,
            Status = ReservationStatus.Pending,
        };

        reservationRepository.AddReservation(reservation);
        return Ok(reservation.Id);
    }

    [HttpGet("Availability/{bookId:guid}/{borrowerId:guid}")]
    public IActionResult GetAvailability(Guid bookId, Guid borrowerId)
    {
        var availability = catalogueRepository.GetBookAvailability(bookId, borrowerId);
        return Ok(availability);
    }

    [HttpGet("BorrowerReservations/{borrowerId:guid}")]
    public IActionResult GetBorrowerReservations(Guid borrowerId)
    {
        var reservations = reservationRepository.GetReservationsByBorrower(borrowerId);
        return Ok(reservations);
    }
}

