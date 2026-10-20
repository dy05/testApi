<?php

namespace App\Models;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'amount', 'category', 'user_id', 'date'])]
class Expense extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'category' => Category::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
