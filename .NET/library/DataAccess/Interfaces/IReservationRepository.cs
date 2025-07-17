using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess.Interfaces;

public interface IReservationRepository
{
    void AddReservation(Reservation reservation);
    Reservation? GetReservationById(Guid reservationId);
    List<Reservation> GetReservationsByBorrower(Guid borrowerId);
    void UpdateReservation(Reservation reservation);
}
