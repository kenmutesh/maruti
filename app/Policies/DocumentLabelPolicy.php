<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DocumentLabelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
       return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can view all document labels', 403);
    }

    public function view(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can view a document label');
    }

    public function create()
    {
        return Response::deny('You cannot create a document label'); // need to ask us the developer if any labels are to be added
    }

    public function update(User $user)
    {
        return $user->role->name == 'ADMIN' ? 
                Response::allow() : Response::deny('Only admins can update any document labels');
    }

    public function delete()
    {
        return Response::deny('You cannot delete a document label'); // only on the developer end 
    }

    public function restore()
    {
        return Response::deny('You cannot restore a deleted document label'); // since no deletion, no restoration as well;
    }

    public function forceDelete(): Response
    {
        return Response::deny('You cannot force delete a document label'); //no deletion is possible
    }
}
