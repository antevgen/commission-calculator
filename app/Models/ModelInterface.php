<?php

namespace App\Models;

interface ModelInterface
{
    public static function fromArray(array $data): ?self;
}
