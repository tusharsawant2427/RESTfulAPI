<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'identifier' => (int)$product->id,
            'title' => (string)$product->name,
            'details' => (string)$product->description,
            'stock' => (int)$product->quantity,
            'status' =>(string)$product->status,
            'picture'=> url("img/{$product->image}"),
            'seller' => (int)$product->seller_id,
            'creationDate' => $product->created_at,
            'lastChange' => $product->updated_at,
            'deletedDate' => isset($product->deleted_at) ? (string)$product->deleted_at : null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('products.show', $product->id),
                ],
                [
                    'rel' => 'product.buyers',
                    'href' => route('products.buyers.index', $product->id),
                ],
                [
                    'rel' => 'seller',
                    'href' => route('sellers.show', $product->seller_id),
                ],
                [
                    'rel' => 'product.categories',
                    'href' => route('products.categories.index', $product->id),
                ],
                [
                    'rel' => 'product.transactions',
                    'href' => route('products.transactions.index', $product->id),
                ],
            ]
        ];
    }

    public static function attributeMapper(string $key)
    {
        $attributes = [
            'identifier' => "id",
            'title' => "name",
            'details' => "description",
            'stock' => "quantity",
            'status' => "status",
            'picture'=> "image",
            'seller' => "seller_id",
            'creationDate' => "created_at",
            'lastChange' => "updated_at",
            'deletedDate' => "deleted_at",
        ];

        return $attributes[$key] ?? null;
    }

    public static function getTransformedAttribute(string $key)
    {
        $attributes = [
            "id" => 'identifier',
            "name" => 'title',
            "description" => 'details',
            "quantity" => 'stock',
            "status" => 'status',
            "image" => 'picture',
            "seller_id" => 'seller',
            "created_at" => 'creationDate',
            "updated_at" => 'lastChange',
            "deleted_at" => 'deletedDate',
        ];
        return $attributes[$key] ?? null;
    }
}
