using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers
{
    [ApiController]
    [Route("[controller]")]
    public class LoanController : ControllerBase
    {
        private readonly ILogger<AuthorController> _logger;
        private readonly ILoanService _loanService;
        
        public LoanController(ILogger<AuthorController> logger, ILoanService loanService)
        {
            _logger = logger;
            _loanService = loanService;
        }

        [HttpGet]
        [Route("OnLoan")]
        public IList<OnLoanModel> Get()
        {
            return _loanService.GetAllLoaned();
        }

        [HttpPost]
        [Route("ReturnLoan{bookStockGuid:required}")]
        public Guid Post(Guid bookStockGuid)
        {
            return _loanService.ReturnBook(bookStockGuid);
        }

        [HttpPost]
        [Route("ReserveBook")]
        public Guid ReserveBook(Guid bookStockId, Guid borrowerId)
        {
            return _loanService.ReserveBook(bookStockId, borrowerId);
        }

        [HttpGet]
        [Route("GetFirstAvailability")]
        public DateTime GetFirstAvailbility(Guid? bookId, string? bookTitle)
        {
            return _loanService.GetFirstAvailableDate(bookId, bookTitle);
        }

    }
}
