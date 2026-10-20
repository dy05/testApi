<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['label'])]
class Category extends Model
{
    use HasFactory;

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
