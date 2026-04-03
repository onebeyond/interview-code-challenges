<?php

namespace App\Application\Routing;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Route
{
    public function __construct(
        public string $path,
        public string $method = 'GET'
    ) {
    }
}
