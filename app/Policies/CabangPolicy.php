<?php

namespace App\Policies;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CabangPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cabang $cabang): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin' || $user->admin?->cabang_id === $cabang->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cabang $cabang): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cabang $cabang): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cabang $cabang): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cabang $cabang): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }
}
