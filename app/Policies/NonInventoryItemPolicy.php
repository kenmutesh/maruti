<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class NonInventoryItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->noninventoryitem->view){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to view inventory items', 403);
    }

    public function create(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->noninventoryitem->create){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to create a inventory items', 403);
    }

    public function update(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->noninventoryitem->update){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to edit a non inventory item', 403);
    }

    public function delete(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can delete a non inventory item', 403);
    }

    public function restore(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can restore a non inventory item', 403);
    }

    public function forceDelete()
    {
        return Response::deny('No force deletion of non inventory items', 403);
    }
}
