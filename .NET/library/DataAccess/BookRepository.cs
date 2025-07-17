using Microsoft.EntityFrameworkCore;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class BookRepository(LibraryContext context) : IBookRepository
{
    public List<Book> GetBooks()
    {
        return context.Books
            .Include(book => book.Author)
            .ToList();
    }

    public Guid AddBook(Book book)
    {
        context.Books.Add(book);
        context.SaveChanges();
        return book.Id;
    }
}