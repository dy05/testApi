<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected ExpenseRepository $expenseRepository
    ) {
    }

    public function summary(int $id, Request $request)
    {
        return response()->json([
            'data' => $this->expenseRepository->summary($id)
        ]);
    }
}
