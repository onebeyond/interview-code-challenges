using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers;

[ApiController]
[Route("[controller]")]
public class AuthorController(ILogger<AuthorController> logger, IAuthorRepository authorRepository) : ControllerBase
{
    [HttpGet]
    [Route("GetAuthors")]
    public IList<Author> Get()
    {
        return authorRepository.GetAuthors();
    }

    [HttpPost]
    [Route("AddAuthor")]
    public Guid Post(Author author)
    {
        return authorRepository.AddAuthor(author);
    }
}