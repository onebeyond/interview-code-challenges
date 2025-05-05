<?php

// In-memory storage of data

// Sample authors
$authors = [
    new Author(1, 'Jane Austen'),
    new Author(2, 'Mark Twain')
];

// Sample books
$books = [
    new Book(1, 'Pride and Prejudice', 1, 'Hardcover', '1111111111111'),
    new Book(2, 'Adventures of Huckleberry Finn', 2, 'Paperback', '2222222222222')
];

// Sample borrowers
$borrowers = [
    new Borrower(1, 'Alice', 'alice@example.com'),
    new Borrower(2, 'Bob', 'bob@example.com')
];

// Sample book stocks (assume book 1 is on loan)
$bookStocks = [
    new BookStock(1, 1, true, '2025-04-10', 1),
    new BookStock(2, 2, false)
];

// Fines and reservations (empty arrays to start)
$fines = [];
$reservations = [];

// 

function maxId($a) {
    return array_reduce(fn($c, $o) => $c > $o->id ? $c : $o->id,$a);
}

function book($bookId) {
    return array_first(array_filter(fn($o) => $bookId==$o->bookId,$books));
}

function bookList() {
    return array_map(function($o) {
        $book = book($o->bookId);
        return ['title' => $book->title, 'isOnLoan'=>$o->isOnLoan,'loanEndDate'=>$o->loanEndDate,'borrowerId'=>$o->borrowerId];
    },$bookStocks);
}

function fineAmount($bookStock) {
    // TODO calculate the fine amount
}

function fineDetails($bookStock) {
    // TODO fine details
}

function returnBook($bookId) {
    $bookStock = array_first(array_filter(fn($o) => $bookId ==$o->bookId, $bookStocks));
    $bookStocks = array_filter(fn($o) => $o->$bookId != $bookId,$bookStocks);

    if (strtotime($bookStock->loadEndDate) < strtotime(date('now'))) {
        return new Fine($id,$bookStock->borrowerId,fineAmount($bookStock), fineDetails($bookStock));
    } else {
        return [];
    }
}

function reservation($bookId, $borrowerId) {
    $bookReservations = array_filter(
        fn($o) => $o->bookId == $bookId
        ,$reservations
    );

    if (count($bookReservations)) {
        $id = maxId($reservations) + 1;
        $reservation = new Reservation($id, $bookId, $borrowerId, date('now'));
        $reservations[]=$reservation;
        return [];
    }

    return NULL;
}

function reservationStatus($bookId) {
    $available = true;
    $reservedAt = NULL;

    $bookReservations = array_filter(
        fn($o) => $o->$bookId == $bookId
        ,$reservations
    );

    if (count($bookReservations)) {
        $available = false;
        $reservedAt = array_reduce(
            fn($v, $o) => (strtotime($o->reservedAt) > strtotime($v)) ? $o->reservedAt : $v 
            ,$bookReservations);
    }

    return [
        'available' => $available,
        'reservedAt' => $reservedAt
    ];
}
