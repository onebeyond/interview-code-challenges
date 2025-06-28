namespace OneBeyondApi.Model
{
    public class OnLoanModel
    {
        public Borrower Borrower { get; set; }
        public List<LoanedBook> Books { get; set; } = [];
    }
}
