using Microsoft.AspNetCore.Mvc;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.Controllers;

[ApiController]
[Route("[controller]")]
public class BookController(ILogger<BookController> logger, IBookRepository bookRepository) : ControllerBase
{
    [HttpGet]
    [Route("GetBooks")]
    public IList<Book> Get()
    {
        return bookRepository.GetBooks();
    }

    [HttpPost]
    [Route("AddBook")]
    public Guid Post(Book book)
    {
        return bookRepository.AddBook(book);
    }
}