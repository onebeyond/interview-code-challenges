using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers;

[ApiController]
[Route("[controller]")]
public class BorrowerController(ILogger<BorrowerController> logger, IBorrowerRepository borrowerRepository) : ControllerBase
{
    [HttpGet]
    [Route("GetBorrowers")]
    public IList<Borrower> Get()
    {
        return borrowerRepository.GetBorrowers();
    }

    [HttpPost]
    [Route("AddBorrower")]
    public Guid Post(Borrower borrower)
    {
        return borrowerRepository.AddBorrower(borrower);
    }
}