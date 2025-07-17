using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class FineRepository(LibraryContext context) : IFineRepository
{
    public void AddFine(Fine fine)
    {
        context.Fines.Add(fine);
        context.SaveChanges();
    }
}
