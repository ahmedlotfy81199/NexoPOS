<?php

namespace App\Scopes;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class BranchScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        if (auth()->hasUser() && Schema::hasColumn($model->getTable(), 'branch_id') && isset(auth()->user()?->branch_id)) {
            if ($branch_id = auth()->user()?->branch_id) {
                return $builder->where($model->getTable() . '.branch_id', '=', $branch_id);
            }
        }
    }
}
