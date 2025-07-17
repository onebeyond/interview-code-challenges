using Microsoft.EntityFrameworkCore;
using OneBeyondApi.Model;

namespace OneBeyondApi.DataAccess;

public class LibraryContext(DbContextOptions<LibraryContext> options) : DbContext(options)
{

    public DbSet<Author> Authors { get; set; }
    public DbSet<Book> Books { get; set; }
    public DbSet<BookStock> Catalogue { get; set; }
    public DbSet<Borrower> Borrowers { get; set; }
    public DbSet<Fine> Fines { get; set; }
    public DbSet<Reservation> Reservations { get; set; }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // Configure relationships
        modelBuilder.Entity<Book>()
            .HasOne(b => b.Author)
            .WithMany()
            .HasForeignKey(b => b.AuthorId);

        modelBuilder.Entity<BookStock>()
            .HasOne(bs => bs.Book)
            .WithMany()
            .HasForeignKey(bs => bs.BookId);

        modelBuilder.Entity<BookStock>()
            .HasOne(bs => bs.OnLoanTo)
            .WithMany()
            .HasForeignKey(bs => bs.OnLoanToId)
            .IsRequired(false);

        modelBuilder.Entity<Reservation>()
            .HasOne(r => r.BookStock)
            .WithMany()
            .HasForeignKey(r => r.BookStockId)
            .IsRequired(false);
    }
}