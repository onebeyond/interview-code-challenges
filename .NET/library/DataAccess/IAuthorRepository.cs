using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess
{
    public interface IAuthorRepository
    {
        public List<Author> GetAuthors();

        public Guid AddAuthor(Author author);
    }
}
