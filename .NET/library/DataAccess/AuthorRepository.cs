using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class AuthorRepository(LibraryContext context) : IAuthorRepository
{
    public List<Author> GetAuthors()
    {
        return context.Authors.ToList();
    }

    public Guid AddAuthor(Author author)
    {
        context.Authors.Add(author);
        context.SaveChanges();
        return author.Id;
    }
}