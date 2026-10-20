<?php

namespace App\Models;

use App\Enums\Category as CategoryEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'amount', 'category', 'user_id', 'date', 'category_id'])]
class Expense extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
//            'category' => CategoryEnum::class,
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
