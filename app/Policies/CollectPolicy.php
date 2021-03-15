<?php

namespace App\Policies;

use App\Models\Collect;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view(?User $user, Collect $collect)
    {
        return request('password') === $collect->password || optional($user)->id === $collect->id;
    }
}
