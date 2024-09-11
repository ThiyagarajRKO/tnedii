<?php

namespace Impiger\BackendMenu\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\BackendMenu\Repositories\Interfaces\BackendMenuInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\BackendMenu\Models\BackendMenu;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class BackendMenuTable extends TableAbstract
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
    protected $editPermissions = "backend-menu.edit";
    protected $deletePermissions = "backend-menu.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * BackendMenuTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param BackendMenuInterface $backendMenuRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, BackendMenuInterface $backendMenuRepository)
    {
        $this->repository = $backendMenuRepository;
        $this->setOption('id', 'plugins-backend-menu-table');
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

                return CrudHelper::getNameFieldLink($item, 'backend-menu', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function($item) {
				return CrudHelper::formatDateTime($item->created_at);
			})
			->editColumn('updated_at', function($item) {
				return CrudHelper::formatDateTime($item->updated_at);
			})
			->editColumn('deleted_at', function($item) {
				return CrudHelper::formatDateTime($item->deleted_at);
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


                return $this->getOperations($editPermissions, $deletePermissions, $item, "<a  href='backend-menus/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'backend_menus.id',
			'backend_menus.menu_id',
			'backend_menus.parent_id',
			'backend_menus.name',
			'backend_menus.url',
			'backend_menus.icon',
			'backend_menus.priority',
			'backend_menus.permissions',
			'backend_menus.target',
			'backend_menus.active',
			'backend_menus.created_at',
			'backend_menus.updated_at',
			'backend_menus.deleted_at'
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
			'class' => 'text-left'
			],
			'menu_id' => [
			'name' => 'menu_id',
			'title' => 'Menu Id',
			'width' => '100',
			'class' => 'text-left'
			],
			'parent_id' => [
			'name' => 'parent_id',
			'title' => 'Parent Id',
			'width' => '100',
			'class' => 'text-left'
			],
			'name' => [
			'name' => 'name',
			'title' => 'Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'url' => [
			'name' => 'url',
			'title' => 'Url',
			'width' => '100',
			'class' => 'text-left'
			],
			'icon' => [
			'name' => 'icon',
			'title' => 'Icon',
			'width' => '100',
			'class' => 'text-left'
			],
			'priority' => [
			'name' => 'priority',
			'title' => 'Priority',
			'width' => '100',
			'class' => 'text-left'
			],
			'permissions' => [
			'name' => 'permissions',
			'title' => 'Permissions',
			'width' => '100',
			'class' => 'text-left'
			],
			'target' => [
			'name' => 'target',
			'title' => 'Target',
			'width' => '100',
			'class' => 'text-left'
			],
			'active' => [
			'name' => 'active',
			'title' => 'Active',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
			'width' => '100',
			'class' => 'text-left'
			],
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
			'width' => '100',
			'class' => 'text-left'
			],
			'deleted_at' => [
			'name' => 'deleted_at',
			'title' => 'Deleted At',
			'width' => '100',
			'class' => 'text-left'
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

        $buttons = $this->addCreateButton(route('backend-menu.create'), 'backend-menu.create');


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, BackendMenu::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('backend-menu.deletes'), 'backend-menu.destroy', parent::bulkActions());
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

        if (Auth::user() && Auth::user()->hasPermission('backend-menu.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('backend-menu.print')) {
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
