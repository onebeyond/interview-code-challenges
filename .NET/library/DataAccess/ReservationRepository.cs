using Microsoft.EntityFrameworkCore;
using OneBeyondApi.DataAccess.Interfaces;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class ReservationRepository(LibraryContext context) : IReservationRepository
{
    public void AddReservation(Reservation reservation)
    {
        context.Reservations.Add(reservation);
        context.SaveChanges();
    }

    public Reservation? GetReservationById(Guid reservationId)
    {
        return context.Reservations
            .Include(r => r.BookStock)
            .FirstOrDefault(r => r.Id == reservationId);
    }

    public List<Reservation> GetReservationsByBorrower(Guid borrowerId)
    {
        return context.Reservations
            .Where(r => r.BorrowerId == borrowerId)
            .ToList();
    }

    public void UpdateReservation(Reservation reservation)
    {
        context.Entry(reservation).State = EntityState.Modified;
        context.SaveChanges();
    }
}