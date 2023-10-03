using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess;
using OneBeyondApi.Model;
using System.Collections;

namespace OneBeyondApi.Controllers
{
    [ApiController]
    [Route("[controller]")]
    public class BorrowerController : ControllerBase
    {
        private readonly ILogger<BorrowerController> _logger;
        private readonly IBorrowerRepository _borrowerRepository;

        public BorrowerController(ILogger<BorrowerController> logger, IBorrowerRepository borrowerRepository)
        {
            _logger = logger;
            _borrowerRepository = borrowerRepository;   
        }

        [HttpGet]
        [Route("GetBorrowers")]
        public IList<Borrower> Get()
        {
            return _borrowerRepository.GetBorrowers();
        }

        [HttpPost]
        [Route("AddBorrower")]
        public Guid Post(Borrower borrower)
        {
            return _borrowerRepository.AddBorrower(borrower);
        }
    }
}