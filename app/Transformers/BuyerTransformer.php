<?php

namespace App\Transformers;

use App\Models\Buyer;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
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
    public function transform(Buyer $buyer)
    {
        return [
            'identifier' => (int)$buyer->id,
            'name' => (string)$buyer->name,
            'email' => (string)$buyer->email,
            'isVerified' => (boolean)$buyer->isVerified(),
            'creationDate' => $buyer->created_at,
            'lastChange' => $buyer->updated_at,
            'deletedDate' => isset($buyer->deleted_at) ? (string)$buyer->deleted_at : null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('buyers.show', $buyer->id),
                ],
                [
                    'rel' => 'buyer.categories',
                    'href' => route('buyers.categories.index', $buyer->id),
                ],
                [
                    'rel' => 'buyer.products',
                    'href' => route('buyers.products.index', $buyer->id),
                ],
                [
                    'rel' => 'buyer.sellers',
                    'href' => route('buyers.sellers.index', $buyer->id),
                ],
                [
                    'rel' => 'buyer.transactions',
                    'href' => route('buyers.transactions.index', $buyer->id),
                ],
                [
                    'rel' => 'user',
                    'href' => route('users.show', $buyer->id),
                ],
            ],
        ];
    }

    public static function attributeMapper(string $key)
    {
        $attributes = [
            'identifier' => "id",
            'name' => "name",
            'email' => "email",
            'isVerified' => "verified",
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
             "name" => 'name',
             "email" => 'email',
             "verified" => 'isVerified',
             "created_at" => 'creationDate',
             "updated_at" => 'lastChange',
             "deleted_at" => 'deletedDate',
        ];

        return $attributes[$key] ?? null;
    }
}
