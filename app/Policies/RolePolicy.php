<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
       return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can view', 403);
    }

    public function view(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can view');
    }

    public function create(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can create');
    }

    public function update(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can update taxes');
    }

    public function delete(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can delete');
    }

    public function restore()
    {
        return Response::deny('You cannot restore');
    }

    public function forceDelete(User $user): Response
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() :Response::deny('You cannot force delete'); //no deletion is possible
    }
}
