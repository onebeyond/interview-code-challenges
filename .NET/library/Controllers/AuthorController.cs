using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess;
using OneBeyondApi.Model;
using System.Collections;

namespace OneBeyondApi.Controllers
{
    [ApiController]
    [Route("[controller]")]
    public class AuthorController : ControllerBase
    {
        private readonly ILogger<AuthorController> _logger;
        private readonly IAuthorRepository _authorRepository;

        public AuthorController(ILogger<AuthorController> logger, IAuthorRepository authorRepository)
        {
            _logger = logger;
            _authorRepository = authorRepository;   
        }

        [HttpGet]
        [Route("GetAuthors")]
        public IList<Author> Get()
        {
            return _authorRepository.GetAuthors();
        }

        [HttpPost]
        [Route("AddAuthor")]
        public Guid Post(Author author)
        {
            return _authorRepository.AddAuthor(author);
        }
    }
}