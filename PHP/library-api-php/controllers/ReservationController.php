<?php

require_once __DIR__ . '/../data/data.php';
require_once __DIR__ . '/../models/Reservation.php';

class ReservationController {
    // POST /reservations
    public function reserve() {
        $reservation = reservation($_POST['bookId'], $_POST['borrowerId']);
        if ($reservation === NULL) {
            header('Content-Type: application/json');
            header('Status: 404');
        } else {
            header('Content-Type: application/json');
            header('Status: 200');
            echo json_encode($reservation);    
        }
    }
    
    // GET /reservations
    public function status() {
        header('Content-Type: application/json');
        header('Status: 200');
        echo json_encode(reservationStatus($_GET['bookId']));
    }
}
