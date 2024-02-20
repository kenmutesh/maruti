<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {        
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->supplier->view){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to view suppliers', 403);
    }

    public function create(User $user)
    {
        $userRole = $user->role;
        
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->supplier->create){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to create a supplier', 403);
    }

    public function update(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->supplier->update){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to edit a supplier', 403);
    }

    public function delete(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can delete a supplier');
    }

    public function restore(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can restore a supplier');
    }

    public function forceDelete()
    {
        return Response::deny('No force deletion of suppliers', 403);
    }
}
