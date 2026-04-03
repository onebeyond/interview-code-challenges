# Library Coding Challenges - PHP API

This project contains a basic API for a fictional library. The library has a catalogue which is made up of a stock of books. This is a PHP API project using in-memory storage for simplicity. If you want to add additional test data, look at the file `data/data.php`.

Each book stock record in the catalogue has an associated Book record, and if currently "On Loan" it has a loan end date and an associated "Borrower". It is possible via the API to retrieve all book stock records in the Catalogue, or to search by Author Name and/or Book Title. Both searches use a "contains" search on the relevant Author/Book attributes.

- **Books** have a name, an associated Author record, a Book Format, and an ISBN number.
- **Authors** have a name.
- **Borrowers** have a name and an email address.

There are basic APIs for Books, Authors, and Borrowers that list all entities and allow addition of new records. It is not currently possible via the API to modify the Book Stock in the Catalogue.

---

## PHP Engineer Challenge (to be implemented in PHP)

Extend the existing API to add the following functionality:

1. **On Loan Endpoint Enhancements:**
   - **List Active Loans:**  
     Add an "On Loan" endpoint that returns the details of all borrowers with active loans, including the titles of the books they have on loan.
   - **Return Books:**  
     Extend the "On Loan" endpoint to allow books on loan to be returned. If a book is returned after its loan end date then a fine should be raised against the borrower. *Hint:* Define the data model for fines and the relationship with borrowers in `models/Fine.php`.

2. **Reservation System:**
   - **Reserve a Book:**  
     Add functionality to allow a borrower to reserve a particular title that is currently on loan (consider multiple borrowers reserving the same book).
   - **Query Reservation Status:**  
     Provide an API endpoint so that a borrower can query when a reserved book will be available.

---

## Project Structure

