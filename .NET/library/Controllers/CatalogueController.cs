using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers;

[ApiController]
[Route("[controller]")]
public class CatalogueController(ILogger<CatalogueController> logger, ICatalogueRepository catalogueRepository) : ControllerBase
{
    [HttpGet]
    [Route("GetCatalogue")]
    public IList<BookStock> Get()
    {
        return catalogueRepository.GetCatalogue();
    }

    [HttpPost]
    [Route("SearchCatalogue")]
    public IList<BookStock> Post(CatalogueSearch search)
    {
        return catalogueRepository.SearchCatalogue(search);
    }
}