namespace OneBeyondApi.Model
{
    public class Loan
    {
        public Guid Id { get; set; }
        public BookStock BookStock { get; set; }
        public Borrower Borrower { get; set; }
        /// <summary>
        /// The planned/loaned date
        /// </summary>
        public DateTime LoanStartDate { get; set; }
        /// <summary>
        /// The planned end date for the loan
        /// </summary>
        public DateTime LoanEndDate { get; set; }

    }
}
