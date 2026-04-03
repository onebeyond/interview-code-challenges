<?php

namespace App\Domain\Model;

readonly class Borrower
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email
    ) {
    }
}
