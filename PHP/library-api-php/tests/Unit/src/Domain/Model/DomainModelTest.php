<?php

namespace App\Tests\Unit;

use App\Domain\Model\Author;
use App\Domain\Model\Book;
use App\Domain\Model\Borrower;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DomainModelTest extends TestCase
{
    #[Test]
    public function it_can_instantiate_an_author_model(): void
    {
        $author = new Author(id: 1, name: 'Stephen King');

        $this->assertEquals(1, $author->id);
        $this->assertEquals('Stephen King', $author->name);
    }

    #[Test]
    public function it_can_instantiate_a_book_model(): void
    {
        $book = new Book(
            id: 10,
            title: 'The Shining',
            authorId: 1,
            format: 'Hardcover',
            isbn: '978-0385121675'
        );

        $this->assertEquals(10, $book->id);
        $this->assertEquals('The Shining', $book->title);
        $this->assertEquals('978-0385121675', $book->isbn);
    }

    #[Test]
    public function it_can_instantiate_a_borrower_model(): void
    {
        $borrower = new Borrower(id: 42, name: 'Andrei', email: 'andrei@example.com');

        $this->assertEquals(42, $borrower->id);
        $this->assertEquals('Andrei', $borrower->name);
        $this->assertEquals('andrei@example.com', $borrower->email);
    }
}
