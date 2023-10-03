namespace OneBeyondApi.Model
{
    public class Book
    {
        public Guid Id { get; set; }
        public string Name { get; set; }
        public Author Author { get; set; }
        public BookFormat Format { get; set; }
        public string ISBN { get; set; }
    }
}
