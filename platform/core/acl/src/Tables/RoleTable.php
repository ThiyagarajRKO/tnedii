<?php

namespace Impiger\ACL\Tables;

use BaseHelper;
use Html;
use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\ACL\Repositories\Interfaces\UserInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
/* @Customized By Sabari Shankar Parthiban */
use App\Utils\CrudHelper;

class RoleTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;
     /* @Customized By Sabari Shankar Parthiban start */
    protected $printPreview = 'base.print';
    protected $editPermission = 'roles.edit';
    protected $deletePermission = 'roles.destroy';
     /* @Customized By Sabari Shankar Parthiban end */
    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * RoleTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param RoleInterface $roleRepository
     * @param UserInterface $userRepository
     */
    public function __construct(
        DataTables $table,
        UrlGenerator $urlGenerator,
        RoleInterface $roleRepository,
        UserInterface $userRepository
    ) {
        parent::__construct($table, $urlGenerator);

        $this->repository = $roleRepository;
        $this->userRepository = $userRepository;

        if (!Auth::user()->hasAnyPermission(['roles.edit', 'roles.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }


    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {

                if (!Auth::user()->hasPermission('roles.edit')) {
                    return $item->name;
                }
                /* @Customized By Sabari Shankar Parthiban start */
                $permissions = $this->checkPermission($item);
                     if($permissions['edit']){
                         return Html::link(route('roles.edit', $item->id), $item->name);
                     }
                     return $item->name;
                /* @Customized By Sabari Shankar Parthiban end */


            })
            /* @Customized By Sabari Shankar Parthiban start */
                ->editColumn('is_system', function($item) {

                    return CrudHelper::formatRows($item->is_system, 'radio', '1:Yes,0:No,:No', $item, '');
                })
                /* @Customized By Sabari Shankar Parthiban end */
                ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('created_by', function ($item) {
                return $item->author->name;
            })
            ->addColumn('operations', function ($item) {

                /*  @customized Sabari Shankar.Parthiban start */
                    $permissions = $this->checkPermission($item);
                     $extraPermission = "<a data-fancybox data-type='ajax' data-src='roles/viewdetail/$item->id ' href='javascript:void(0);' class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>";
                     if($permissions['delete']){
                         $extraPermission.=CrudHelper::getRowActivationActionBtn($item,'Impiger\ACL\Models\Role', 'roles.enable_disable','is_system:1','role');
                     }
                return $this->getOperations($permissions['edit'], $permissions['delete'], $item,$extraPermission);
                /*  @customized Sabari Shankar.Parthiban end */
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
            'roles.id',
            'roles.name',
            'roles.description',
            'roles.created_at',
            'roles.created_by',
            /*@ Customized By Sabari Shankar Parthiban start */
            'roles.is_system',
            'roles.is_enabled',
            /*@ Customized By Sabari Shankar Parthiban end */
        ];
        $query = $model
            ->with('author')
            ->select($select);
        /*@ Customized By Sabari Shankar Parthiban start */
            $query = $query->whereNull('roles.deleted_at');
        /*@ Customized By Sabari Shankar Parthiban end */
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            // 'id'          => [
            //     'name'  => 'roles.id',
            //     'title' => trans('core/base::tables.id'),
            //     'width' => '20px',
            // ],
            'name'        => [
                'name'  => 'roles.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            /* @Customized by Sabari Shankar Parthiban start
            'is_system'    => [
                'name'  => 'roles.is_system',
                'title' => trans('core/acl::permissions.system'),
                'class' => 'text-left',
                'width' => '30px',
            ],
            /* @Customized by Sabari Shankar Parthiban end */
            'description' => [
                'name'  => 'roles.description',
                'title' => trans('core/base::tables.description'),
                'class' => 'text-left',
            ],
            'created_at'  => [
                'name'  => 'roles.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
                'class' => 'text-left',
            ],
            'created_by'  => [
                'name'  => 'roles.created_by',
                'title' => trans('core/acl::permissions.created_by'),
                'width' => '100px',
                'class' => 'text-left',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        return $this->addCreateButton(route('roles.create'), 'roles.create');
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('roles.deletes'), 'roles.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*
             * @customized by Sabari Shankar Parthiban
            'roles.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],

             */
        ];
    }
    /* @Customized By Sabari Shankar Parthiban Start */

    public function checkPermission($item) {
        $permissions=[
            'edit' => 'roles.edit',
            'delete' => 'roles.destroy',
        ];
        // if ($item->is_system) {
        //     $this->deletePermission = '';
        // }

        if (Auth::user() &&  in_array($item->id, Auth::user()->role_ids)) {
            $permissions['edit'] = '';
            $permissions['delete'] = '';
        }
        // if ($item->is_system && (!Auth::user()->is_admin && !Auth::user()->isSuperUser()) && $item->created_by !=Auth::id()) {
        //     $this->editPermission = '';
        // }
        return $permissions;
    }
     /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'roles.name' => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:60|min:3',
            ],
           /* 'roles.is_system' => [
                'title' => trans('core/acl::permissions.system'),
                'type'     => 'select',
                'choices'  => [1=>'Yes',0=>'No']
            ],*/
            'roles.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
        ];
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

        if (Auth::user() && Auth::user()->hasPermission('roles.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('roles.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }
    /* @Customized by Sabari Shankar Parthiban end */
}
