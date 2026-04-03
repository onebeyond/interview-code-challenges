<?php

namespace App\Domain\Model;

use DateTimeImmutable;

readonly class BookStock
{
    public function __construct(
        public int $id,
        public int $bookId,
        public bool $isOnLoan = false,
        public ?DateTimeImmutable $loanEndDate = null,
        public ?int $borrowerId = null
    ) {
    }

    /**
     * Domain logic belongs in the model. Returning a book generates a new immutable state.
     */
    public function returnBook(): self
    {
        return new self(
            id: $this->id,
            bookId: $this->bookId,
            isOnLoan: false,
            loanEndDate: null,
            borrowerId: null
        );
    }
}
