using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess.Interfaces;

public interface IBorrowerRepository
{
    public List<Borrower> GetBorrowers();

    public Guid AddBorrower(Borrower borrower);
}