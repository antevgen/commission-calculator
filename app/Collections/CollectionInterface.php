<?php

declare(strict_types=1);

namespace App\Collections;

use App\Models\ModelInterface;

interface CollectionInterface
{
    public function add(ModelInterface $item): void;

    public function pluck($property): array;
}
