<?php

class Book {
    public $id;
    public $title;
    public $authorId;
    public $format;
    public $isbn;

    public function __construct($id, $title, $authorId, $format, $isbn) {
        $this->id = $id;
        $this->title = $title;
        $this->authorId = $authorId;
        $this->format = $format;
        $this->isbn = $isbn;
    }
}
