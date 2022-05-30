<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;

class CategoryTransactionsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Category $category)
    {
        $transactions = $category->products()
            ->whereHas('transactions') // bring only those products which have transaction associated with them
            ->with('transactions')
            ->get()
            ->pluck('transactions')
            ->flatten();
        return $this->showAll($transactions);
    }
}
