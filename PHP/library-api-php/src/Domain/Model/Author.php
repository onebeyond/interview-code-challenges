<?php

namespace App\Domain\Model;

readonly class Author 
{
    public function __construct(
        public int $id,
        public string $name
    ) {}
}