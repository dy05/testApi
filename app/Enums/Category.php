<?php

namespace App\Enums;

enum Category: string
{
    case FOOD = 'food';
    case TRAVEl = 'travel';
    case OFFICE = 'office';
    case OTHER = 'other';

    public function all(): array
    {
        return [
            'food',
            'travel',
            'office',
            'other'
        ];
    }
}
