<?php

/**
 * NexoPOS Controller
 *
 * @since  1.0
 **/

namespace App\Http\Controllers\Dashboard;

use App\Crud\RolesCrud;
use App\Crud\BranchCrud;
use App\Http\Controllers\DashboardController;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Branch;
use App\Services\BranchesService;
use App\Services\DateService;
use App\Services\BranchsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class BranchesController extends DashboardController
{
    public function __construct(
        protected DateService $dateService
    ) {
        // ...
    }

    public function listBranches()
    {
        return BranchCrud::table();
    }

    public function createBranch()
    {
        ns()->restrict(['create.users']);

        return BranchCrud::form();
    }

    public function editBranch(Branch $branch)
    {
        ns()->restrict(['update.branchs']);

        if ($branch->id === Auth::id()) {
            return redirect(ns()->route('ns.dashboard.branchs.profile'));
        }

        return BranchCrud::form($branch);
    }

    public function getBranchs(Branch $branch)
    {
        ns()->restrict(['read.branchs']);

        return Branch::get(['branchname', 'id', 'email']);
    }
}
