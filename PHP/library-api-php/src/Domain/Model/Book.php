<?php

namespace App\Domain\Model;

readonly class Book
{
    public function __construct(
        public int $id,
        public string $title,
        public int $authorId,
        public string $format,
        public string $isbn
    ) {
    }
}
