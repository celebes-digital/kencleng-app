<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CabangScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $admin = Auth::user()?->admin;

        if ($admin?->level === 'admin' || $admin?->level === 'manajer') {
            $builder->where('cabang_id', $admin?->cabang_id);
        }
    }
}
