using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class BorrowerRepository(LibraryContext context) : IBorrowerRepository
{
    public List<Borrower> GetBorrowers()
    {
        return context.Borrowers.ToList();
    }

    public Guid AddBorrower(Borrower borrower)
    {
        context.Borrowers.Add(borrower);
        context.SaveChanges();
        return borrower.Id;
    }
}