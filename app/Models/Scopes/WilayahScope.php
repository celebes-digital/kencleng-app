<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class WilayahScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $admin = Auth::user()?->admin;

        if ($admin?->level === 'direktur' || $admin?->level === 'admin_wilayah') {
            $builder->where($model->getTable() . '.wilayah_id', $admin?->wilayah_id);
        }
    }
}
