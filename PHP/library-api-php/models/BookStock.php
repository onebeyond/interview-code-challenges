<?php

class BookStock {
    public $id;
    public $bookId;
    public $isOnLoan;
    public $loanEndDate;  // Date string in 'Y-m-d' format or null
    public $borrowerId;   // null if not on loan

    public function __construct($id, $bookId, $isOnLoan = false, $loanEndDate = null, $borrowerId = null) {
        $this->id = $id;
        $this->bookId = $bookId;
        $this->isOnLoan = $isOnLoan;
        $this->loanEndDate = $loanEndDate;
        $this->borrowerId = $borrowerId;
    }
}
