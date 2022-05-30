<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;

class ProductTransactionsController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('client.credentials')->only(['index']);
    }

    public function index(Product $product)
    {
        $transactions = $product->transactions;
        return $this->showAll($transactions);
    }
}
