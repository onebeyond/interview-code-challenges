<?php

namespace App\Application\Controller;

use App\Application\Routing\Route;
use App\Domain\Repository\BookRepositoryInterface;

readonly class BookController
{
    public function __construct(
        private BookRepositoryInterface $bookRepo
    ) {
    }

    #[Route('/books', 'GET')]
    public function index(): void
    {
        $books = $this->bookRepo->findAll();

        // We ensure a clean JSON response of the entire catalog
        echo json_encode($books);
    }
}
