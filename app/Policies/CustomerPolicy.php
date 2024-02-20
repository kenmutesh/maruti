<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {        
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->customer->view){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to view', 403);
    }

    public function create(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->customer->create){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to create', 403);
    }

    public function update(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->customer->update){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to edit', 403);
    }

    public function delete(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can delete');
    }

    public function restore(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can restore');
    }

    public function forceDelete()
    {
        return Response::deny('No force deletion', 403);
    }
}
