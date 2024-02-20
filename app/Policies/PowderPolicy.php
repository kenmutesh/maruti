<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PowderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->powder->view){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to view powders', 403);
    }

    public function create(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->powder->create){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to create a powder', 403);
    }

    public function update(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->powder->update){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to edit a powder', 403);
    }

    public function delete(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can delete a powder', 403);
    }

    public function restore(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ?  
        Response::allow() : Response::deny('Only admins can restore a supplier', 403);
    }

    public function forceDelete()
    {
        return Response::deny('No force deletion of powders', 403);
    }
}
