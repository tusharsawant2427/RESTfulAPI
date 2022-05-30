<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;

class BuyerSellersController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Buyer $buyer)
    {
        $sellers = $buyer->transactions()
            ->with('product.seller') // nested relationship
            ->get()
            ->pluck('product.seller')
            ->unique();
        return $this->showAll($sellers);
    }
}
