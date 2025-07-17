using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess.Interfaces;

public interface IAuthorRepository
{
    public List<Author> GetAuthors();

    public Guid AddAuthor(Author author);
}