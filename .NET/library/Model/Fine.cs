namespace OneBeyondApi.Model
{
    public class Fine
    {
        public Guid Id { get; set; }
        public Borrower Borrower { get; set; }
        /// <summary>
        /// The fine for the late return
        /// </summary>
        public int Amount { get; set; }
        /// <summary>
        /// If a fine paid than this is the pay date
        /// </summary>
        public DateTime? PaidDate { get; set; }
    }
}
