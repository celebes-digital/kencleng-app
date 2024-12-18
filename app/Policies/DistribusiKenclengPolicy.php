<?php

namespace App\Policies;

use App\Models\DistribusiKencleng;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DistribusiKenclengPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin || 
            $user->profile?->group === 'distributor';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DistribusiKencleng $distribusiKencleng): bool
    {
        return $user->is_admin ||
            ($user->profile?->group === 'distributor'
            && $user->profile?->distributor_id === $distribusiKencleng->distributor_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DistribusiKencleng $distribusiKencleng): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DistribusiKencleng $distribusiKencleng): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DistribusiKencleng $distribusiKencleng): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DistribusiKencleng $distribusiKencleng): bool
    {
        return $user->is_admin;
    }
}
