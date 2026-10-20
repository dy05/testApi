<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface IBaseRepository
{
    public function all(): Collection;
}
