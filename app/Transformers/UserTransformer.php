<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
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
    public function transform(User $user)
    {
        return [
            'identifier' => (int)$user->id,
            'name' => (string)$user->name,
            'email' => (string)$user->email,
            'isVerified' => (boolean)$user->isVerified(),
            'isAdmin' => (boolean)$user->isAdmin(),
            'creationDate' => $user->created_at,
            'lastChange' => $user->updated_at,
            'deletedDate' => isset($user->deleted_at) ? (string)$user->deleted_at : null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id),
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
            'isAdmin' => "admin",
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
            "admin" => 'isAdmin',
            "created_at" => 'creationDate',
            "updated_at" => 'lastChange',
            "deleted_at" => 'deletedDate',
        ];

        return $attributes[$key] ?? null;
    }
}
