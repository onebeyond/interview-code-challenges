using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers
{
    [ApiController]
    [Route("[controller]")]
    public class LoanController : ControllerBase
    {
        private readonly ILogger<LoanController> _logger;
        private readonly ICatalogueRepository _catalogueRepository;
        private readonly IBorrowerRepository _borrowerRepository;

        public LoanController(ILogger<LoanController> logger, ICatalogueRepository catalogueRepository, IBorrowerRepository borrowerRepository)
        {
            _logger = logger;
            _catalogueRepository = catalogueRepository;
            _borrowerRepository = borrowerRepository;
        }

        [HttpGet]
        [Route("GetOnLoan")]
        public IActionResult GetOnLoan()
        {
            var onLoan = _catalogueRepository
                .GetCatalogue()
                .Where(stock => stock.OnLoanTo != null)
                .Select(stock => new
                {
                    Borrower = stock.OnLoanTo.Name,
                    Email = stock.OnLoanTo.EmailAddress,
                    BookTitle = stock.Book.Name,
                    DueDate = stock.LoanEndDate
                })
                .ToList();

            return Ok(onLoan);
        }
    }
}
