namespace OneBeyondApi.Model
{
    public class BookStock
    {
        public Guid Id { get; set; }
        public Book Book { get; set; }
        public DateTime? LoanEndDate { get; set; }
        public Borrower? OnLoanTo { get; set; }
    }
}
