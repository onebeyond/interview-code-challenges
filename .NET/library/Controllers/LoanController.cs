using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess.Interfaces;

namespace OneBeyondApi.Controllers;

[ApiController]
[Route("[controller]")]
public class LoanController(
    ICatalogueRepository catalogueRepository,
    IFineRepository fineRepository) : ControllerBase
{

    [HttpGet("ActiveLoans")]
    public IActionResult GetActiveLoans()
    {
        var activeLoans = catalogueRepository.GetActiveLoans();
        return Ok(activeLoans);
    }

    [HttpPost("ReturnBook/{bookStockId}")]
    public IActionResult ReturnBook(Guid bookStockId)
    {
        var (success, fine) = catalogueRepository.ReturnBook(bookStockId);

        if (!success)
            return NotFound("Book not found or not on loan");

        if (fine != null)
            fineRepository.AddFine(fine);

        return Ok();
    }

    [HttpPost("FulfillReservation/{reservationId}")]
    public IActionResult FulfillReservation(Guid reservationId)
    {
        var (success, message) = catalogueRepository.FulfillReservation(reservationId);

        if (!success)
            return BadRequest(message);

        return Ok();
    }
}