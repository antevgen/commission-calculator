<?php

namespace App\Parsers;

use App\Collections\CollectionInterface;

interface ParserInterface
{
    public function parse(string $input): CollectionInterface;
}
