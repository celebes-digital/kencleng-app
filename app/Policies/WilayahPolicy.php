<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Auth\Access\Response;

class WilayahPolicy
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
    public function view(User $user, Wilayah $wilayah): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
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
    public function update(User $user, Wilayah $wilayah): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wilayah $wilayah): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Wilayah $wilayah): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Wilayah $wilayah): bool
    {
        return $user->is_admin && ($user->admin?->level === 'principal' || $user->admin?->level === 'superadmin');
    }
}
