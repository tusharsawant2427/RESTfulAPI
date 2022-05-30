<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellerPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if($user->isAdmin()) {
            return true;
        }
    }

    public function view(User $user, Seller $seller)
    {
        return $user->id === $seller->id;
    }

    public function updateProduct(User $user, Seller $seller)
    {
        return $user->id === $seller->id;
    }

    public function deleteProduct(User $user, Seller $seller)
    {
        return $user->id === $seller->id;
    }

    public function sale(User $user, User $seller) // Remember here the seller has to be instance of User as at the first time it won't be seller it will be user only.
    {
        return $user->id === $seller->id;
    } //
}
