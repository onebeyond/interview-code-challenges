using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess.Interfaces;

public interface IBookRepository
{
    public List<Book> GetBooks();

    public Guid AddBook(Book book);
}