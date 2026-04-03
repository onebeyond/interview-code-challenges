<?php

namespace App\Domain\Model;

readonly class Fine 
{
    public function __construct(
        public string $id,
        public int $borrowerId,
        public float $amount,
        public string $details = ''
    ) {}
}