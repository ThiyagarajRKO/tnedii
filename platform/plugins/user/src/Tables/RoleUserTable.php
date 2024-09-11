<?php

namespace Impiger\User\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\User\Repositories\Interfaces\RoleUserInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\User\Models\RoleUser;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class RoleUserTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * @var string
     */
    protected $view = "core/table::table";
    protected $editPermissions = "role-user.edit";
    protected $deletePermissions = "role-user.destroy";
    protected $printPreview = 'base.print';
    /**
     * RoleUserTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param RoleUserInterface $roleUserRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, RoleUserInterface $roleUserRepository)
    {
        $this->repository = $roleUserRepository;
        $this->setOption('id', 'plugins-role-user-table');
        parent::__construct($table, $urlGenerator);


    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                $isEdit =  (!empty($this->editPermissions) && !$this->checkDefault($item));
                $isPublic =  $this->getOption('shortcode');
                return CrudHelper::getNameFieldLink($item, 'role-user', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })

            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                if($this->checkDefault($item)){
                  $editPermissions = "";
                  $deletePermissions = "";
                }
                return $this->getOperations($editPermissions, $deletePermissions, $item, "<a  href='role-users/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary'><i class='fa fa-eye'></i></a>");
            });

            return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'role_users.id',
			'role_users.user_id',
			'role_users.role_id',
			'role_users.created_at',
			'role_users.updated_at'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
			'name' => 'id',
			'title' => 'Id',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'user_id' => [
			'name' => 'user_id',
			'title' => 'User Id',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'role_id' => [
			'name' => 'role_id',
			'title' => 'Role Id',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        if (!$this->hasActions) {
            return [];
        }

        $buttons = $this->addCreateButton(route('role-user.create'), 'role-user.create');


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, RoleUser::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('role-user.deletes'), 'role-user.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        $row = \DB::table('cruds')->where('module_name', 'role-user')->get()->first();
        $moduleConfig = CF_decode_json($row->module_config);
        $columns = [];
        $bulkChangesType = ['text', 'select', 'text_datetime', 'select-search', 'number', 'date'];
        $gridConfig = [];

        foreach($moduleConfig['grid'] as $grid)
            $gridConfig[$grid['field']] = $grid;

        foreach ($moduleConfig['forms'] as $val) {
            $gConfig = (isset($gridConfig[$val['field']])) ? $gridConfig[$val['field']] : [];
            $isAllowedType = (Arr::get($gConfig, 'view') && in_array($val['type'], $bulkChangesType)) ? true : false;
            if (
                (!$isFilter && $isAllowedType) ||
                ($isFilter && $val['search'] && $isAllowedType)
             ) {
                $config = [
                    'title' => $val['label'],
                    'type' => $val['type'],
                    'validate' => $val['required']
                ];

                if ($val['type'] == 'select') {
                    $config['choices'] = CrudHelper::getSelectBoxChoices($val);
                } else if ($val['type'] == 'text_datetime') {
                    $config['type'] = 'date';
                }

                $columns[$val['alias'] . "." . $val['field']] = $config;
            }
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges(true);
    }

    /**
     * @return array
     */
    public function getDefaultButtons(): array
    {
        $defaultBtns = parent::getDefaultButtons();

        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user() && Auth::user()->hasPermission('role-user.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('role-user.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if(isset($item->is_default) && $item->is_default){
            return true;
        }
        return false;
    }

    public function setTableConfig($config): self
    {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }

}
