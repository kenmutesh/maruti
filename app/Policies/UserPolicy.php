<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
       return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can view all users', 403);
    }

    public function view(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can view a user');
    }

    public function create(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can view create user');
    }

    public function update(User $authenticatedUser, User $user)
    {
    }

    public function delete(User $authenticatedUser, User $user)
    {
        return $authenticatedUser->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can view delete user');
    }

    public function restore(User $authenticatedUser, User $user)
    {
        return $authenticatedUser->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can restore a user');
    }

    public function forceDelete(User $authenticatedUser, User $user): Response
    {
        return Response::deny('You cannot force delete a user'); //no force deletion is possible
    }
}
