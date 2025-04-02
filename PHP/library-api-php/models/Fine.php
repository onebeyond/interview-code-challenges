<?php

class Fine {
    public $id;
    public $borrowerId;
    public $amount;
    public $details;

    public function __construct($id, $borrowerId, $amount, $details = '') {
        $this->id = $id;
        $this->borrowerId = $borrowerId;
        $this->amount = $amount;
        $this->details = $details;
    }
}
