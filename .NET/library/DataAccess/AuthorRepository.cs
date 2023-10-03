using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess
{
    public class AuthorRepository : IAuthorRepository
    {
        public AuthorRepository()
        {
        }
        public List<Author> GetAuthors()
        {
            using (var context = new LibraryContext())
            {
                var list = context.Authors
                    .ToList();
                return list;
            }
        }

        public Guid AddAuthor(Author author)
        {
            using (var context = new LibraryContext())
            {
                context.Authors.Add(author);
                context.SaveChanges();
                return author.Id;
            }
        }
    }
}
