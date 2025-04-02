<?php

class Reservation {
    public $id;
    public $bookId;
    public $borrowerId;
    public $reservedAt;

    public function __construct($id, $bookId, $borrowerId, $reservedAt) {
        $this->id = $id;
        $this->bookId = $bookId;
        $this->borrowerId = $borrowerId;
        $this->reservedAt = $reservedAt;
    }
}
