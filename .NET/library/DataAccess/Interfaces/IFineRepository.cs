using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess.Interfaces;

public interface IFineRepository
{
    void AddFine(Fine fine);
}
