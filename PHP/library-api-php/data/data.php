<?php

use App\Domain\Model\Author;
use App\Domain\Model\Book;
use App\Domain\Model\Borrower;
use App\Domain\Model\BookStock;
use App\Domain\Model\Reservation;
use App\Domain\Model\Fine;

// Sample authors
$authors = [
    new Author(id: 1, name: 'Jane Austen'),
    new Author(id: 2, name: 'Mark Twain')
];

// Sample books
$books = [
    new Book(id: 1, title: 'Pride and Prejudice', authorId: 1, format: 'Hardcover', isbn: '1111111111111'),
    new Book(id: 2, title: 'Adventures of Huckleberry Finn', authorId: 2, format: 'Paperback', isbn: '2222222222222')
];

// Sample borrowers
$borrowers = [
    new Borrower(id: 1, name: 'Alice', email: 'alice@example.com'),
    new Borrower(id: 2, name: 'Bob', email: 'bob@example.com')
];

// Sample book stocks (assume book 1 is on loan)
// Sample book stocks with EDGE CASES
$bookStocks = [
    new BookStock(id: 1, bookId: 1, isOnLoan: true, loanEndDate: new DateTimeImmutable('2025-04-10'), borrowerId: 1),
    new BookStock(id: 2, bookId: 2, isOnLoan: false, loanEndDate: null, borrowerId: null),
    // EDGE CASE: Massively Overdue (10 days late)
    new BookStock(id: 3, bookId: 1, isOnLoan: true, loanEndDate: new DateTimeImmutable('-10 days'), borrowerId: 2),
    // EDGE CASE: Highly Contested Book
    new BookStock(id: 4, bookId: 2, isOnLoan: true, loanEndDate: new DateTimeImmutable('+2 days'), borrowerId: 1)
];

// Fines (keep empty to start)
$fines = [];

// EDGE CASE: 2 people already in the queue for BookStock #4
$reservations = [
    new Reservation(id: 'res_edge_1', bookStockId: 4, borrowerId: 2, reservedAt: new DateTimeImmutable('-1 day')),
    new Reservation(id: 'res_edge_2', bookStockId: 4, borrowerId: 3, reservedAt: new DateTimeImmutable('-1 hour'))
];