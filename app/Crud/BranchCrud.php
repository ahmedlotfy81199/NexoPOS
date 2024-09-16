<?php

namespace App\Crud;

use App\Classes\CrudForm;
use App\Classes\CrudTable;
use App\Classes\FormInput;
use App\Models\Branch;
use App\Services\CrudService;
use Illuminate\Http\Request;

class BranchCrud extends CrudService
{
    /**
     * Define the autoload status
     */
    const AUTOLOAD = true;

    /**
     * Define the identifier
     */
    const IDENTIFIER = 'ns.branches';

    /**
     * Define the base table
     */
    protected $table = 'nexopos_branches';

    /**
     * Base route name
     */
    protected $mainRoute = 'ns.branches.index';

    /**
     * Define namespace
     */
    protected $namespace = 'ns.branches';

    /**
     * Model Used
     */
    protected $model = Branch::class;

    /**
     * Determine if the options column should display
     * before the crud columns
     */
    protected $prependOptions = true;

    /**
     * Fields which will be filled during post/put
     */
    public $fillable = ['name'];

    protected $permissions = [
        'create' => 'create.users',
        'read' => 'read.users',
        'update' => 'update.users',
        'delete' => 'delete.users',
    ];

    /**
     * Define Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return the label used for the crud instance
     *
     * @return array
     **/
    public function getLabels()
    {
        return CrudTable::labels(
            list_title: __('Branches List'),
            list_description: __('Display all branches.'),
            no_entry: __('No branches have been registered'),
            create_new: __('Add a new branch'),
            create_title: __('Create a new branch'),
            create_description: __('Register a new branch and save it.'),
            edit_title: __('Edit branch'),
            edit_description: __('Modify Branch.'),
            back_to_list: __('Return to Branches')
        );
    }

    /**
     * Fields
     *
     * @param  object/null
     * @return array of fields
     */
    public function getForm(?Branch $entry = null)
    {
        return CrudForm::form(
            main: FormInput::text(
                label: __('Branch Name'),
                name: 'name',
                validation: 'required',
                value: $entry->name ?? '',
                description: __('Provide a unique name for the branch.')
            ),
            tabs: CrudForm::tabs()
        );
    }

    /**
     * Filter POST input fields
     *
     * @param  array of fields
     * @return array of fields
     */
    public function filterPostInputs($inputs)
    {
        return collect($inputs)->filter(fn($input) => !empty($input))->toArray();
    }

    /**
     * Hooks for additional query modifications if needed
     */
    public function hook($query): void
    {
        $query->orderBy('updated_at', 'desc');
    }

    /**
     * get Links
     *
     * @return array of links
     */
    public function getLinks(): array
    {
        return [
            'list' => ns()->url('dashboard/' . 'branches'),
            'create' => ns()->url('dashboard/' . 'branches/create'),
            'edit' => ns()->url('dashboard/' . 'branches/edit/'),
            'post' => ns()->url('api/crud/' . 'ns.branches'),
            'put' => ns()->url('api/crud/' . 'ns.branches/{id}' . ''),
        ];
    }

    /**
     * get
     *
     * @param  string
     * @return mixed
     */
    public function get($param)
    {
        switch ($param) {
            case 'model':
                return $this->model;
                break;
        }
    }

    /**
     * Define Columns
     */
    public function getColumns(): array
    {
        return CrudTable::columns(
            CrudTable::column(
                label: __('ID'),
                identifier: 'id',
            ),
            CrudTable::column(
                label: __('Branch Name'),
                identifier: 'name'
            ),
            CrudTable::column(
                label: __('Created At'),
                identifier: 'created_at'
            )
        );
    }
}
