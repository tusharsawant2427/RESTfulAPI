<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Seller;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductsController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-product');

        $this->middleware('can:view,seller')->only('index');
        $this->middleware('can:sale,seller')->only('store');
        $this->middleware('can:update-product,seller')->only('update'); //see the name properly
        $this->middleware('can:delete-product,seller')->only('destroy');

    }

    public function index(Seller $seller)
    {
        if(request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-product')) {
            $products = $seller->products;
            return $this->showAll($products);
        }
        throw new AuthorizationException("Invalid Scopes");
    }

    public function store(Request $request, Seller $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image'=> 'required|image'
        ];
        $this->validate($request, $rules);
        $data = $request->all();
        $data['status'] = Product::UNAVAILABLE_PRODUCT;
        $data['image'] = $request->image->store(''); // store() requires path and filesystem name, but our default is products so no need to pass in the second argument as well we need not create any subfolder so our path is also blank but that is compulsory argument so passing blank
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);
        return $this->showOne($product);
    }

    public function update(Request $request, Seller $seller, Product $product)
    {
        /*
         * Points to remember
         * We need to validate that the product's seller id and seller who is requesting the update are same
         * We cannot mark the product as AVAILABLE if there is no category associated with it
         */
        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT,
            'image' => 'image'
        ];
        $this->validate($request, $rules);

        $this->verifySeller($seller, $product);

        $product->fill(
            $request->only([
                'name',
                'description',
                'quantity'
            ])
        );

        if($request->has('status')) {
            $product->status = $request->status;

            if($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse("A product must have atleast one category associated to be active!", 409);
            }
        }

        if($request->hasFile('image')) {
            Storage::delete($product->image);
            $product->image = $request->image->store('');
        }

        if($product->isClean()) {
            return $this->errorResponse('You have not updated any value', 422);
        }

        $product->save();
        return $this->showOne($product);
    }

    public function destroy(Seller $seller, Product $product)
    {
        $this->verifySeller($seller, $product);
        Storage::delete($product->image);
        $product->delete();
        return $this->showOne($product);
    }

    private function verifySeller(Seller $seller, Product $product)
    {
        if($seller->id != $product->seller_id) {
            throw new HttpException(422, "You are trying to update someone else's product!");
        }
    }
}
