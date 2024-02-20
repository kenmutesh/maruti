<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class InventoryItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->inventoryitem->view){
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
            if($privileges->inventoryitem->create){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to create a inventory items', 403);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user)
    {
        $userRole = $user->role;
        if($userRole->name == 'ADMIN'){
            return Response::allow();
        }else{
            $privileges = json_decode($userRole->privileges);
            if($privileges->inventoryitem->update){
                return Response::allow();
            }
        }
        return Response::deny('Not allowed to edit a inventory item', 403);
    }

    public function delete(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ? 
        Response::allow() : Response::deny('Only admins can delete an inventory item', 403);
    }

    public function restore(User $user)
    {
        $userRole = $user->role;
        return $userRole->name == 'ADMIN' ?
        Response::allow() : Response::deny('Only admins can restore an inventory item', 403);
    }

    public function forceDelete()
    {
        return Response::deny('No force deletion of inventory items', 403);
    }
}
