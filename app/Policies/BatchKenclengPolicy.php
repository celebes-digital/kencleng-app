<?php

namespace App\Policies;

use App\Models\BatchKencleng;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BatchKenclengPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BatchKencleng $batchKencleng): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin && ($user->admin?->level === 'admin' || $user->admin?->level === 'principal');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BatchKencleng $batchKencleng): bool
    {
        return $user->is_admin && ($user->admin?->level ==='admin' || $user->admin?->level === 'principal');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BatchKencleng $batchKencleng): bool
    {
        return $user->is_admin && ($user->admin?->level ==='admin' || $user->admin?->level === 'principal');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BatchKencleng $batchKencleng): bool
    {
        return $user->is_admin && ($user->admin?->level ==='admin' || $user->admin?->level === 'principal');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BatchKencleng $batchKencleng): bool
    {
        return $user->is_admin && ($user->admin?->level ==='admin' || $user->admin?->level === 'principal');
    }
}
