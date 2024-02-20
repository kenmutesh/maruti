<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaxPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
       return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can view all taxes', 403);
    }

    public function view(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can view a tax');
    }

    public function create(User $user)
    {
        return Response::deny('You cannot create a tax'); // need to ask us the developer if any labels are to be added
    }

    public function update(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can update taxes');
    }

    public function delete()
    {
        return Response::deny('You cannot delete a tax'); // only on the developer end 
    }

    public function restore()
    {
        return Response::deny('You cannot restore a deleted tax'); // since no deletion, no restoration as well;
    }

    public function forceDelete(): Response
    {
        return Response::deny('You cannot force delete a tax'); //no deletion is possible
    }
}
