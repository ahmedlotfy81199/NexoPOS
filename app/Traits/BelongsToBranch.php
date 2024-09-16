<?php

namespace App\Traits;

use App\Models\Branch;
use App\Scopes\BranchScope;
use Illuminate\Support\Facades\Schema;

trait BelongsToBranch
{
    public static $branchIdColumn = 'branch_id';

    public function branch()
    {
        return $this->belongsTo(Branch::class, BelongsToBranch::$branchIdColumn);
    }

    public static function bootBelongsToBranch()
    {
        static::addGlobalScope(new BranchScope());

        static::creating(function ($model) {
            $modelName = class_basename($model);
            $ignoredModels = ['User'];
            if (!$model->getAttribute(BelongsToBranch::$branchIdColumn) && Schema::hasColumn($model->getTable(), 'branch_id') && ! $model->relationLoaded('branch') && ! in_array($modelName, $ignoredModels)) {
                $model->setAttribute(BelongsToBranch::$branchIdColumn, auth()->user()?->branch_id);
            }
        });
    }
}
