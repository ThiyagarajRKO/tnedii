<?php

namespace Impiger\Usergroups\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupsInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Usergroups\Models\Usergroups;
use Html;
use App\Utils\CrudHelper;

class UsergroupsTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    protected $printPreview = 'base.print';
    /**
     * UsergroupsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param UsergroupsInterface $usergroupsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UsergroupsInterface $usergroupsRepository)
    {
        $this->repository = $usergroupsRepository;
        $this->setOption('id', 'plugins-usergroups-table');
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
                if (!Auth::user()->hasPermission('usergroups.edit')) {
                    return $item->name;
                }
                return Html::link(route('usergroups.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return CrudHelper::formatDateTime($item->created_at);
            });


        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('usergroups.edit', 'usergroups.destroy', $item,"<a data-fancybox data-type='ajax' data-src='usergroups/viewdetail/$item->id ' href='javascript:void(0);' class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
            })
            ->escapeColumns([])
            ->make(true);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'usergroups.id',
            'usergroups.name',
            'usergroups.created_at',
            'usergroups.description',
        ];

        $query = $model->select($select);
        $user = Auth::user();
        if($user && !$user->is_admin && !$user->isSuperUser()){
              $query =$query->where(function($query) use ($user){
                  foreach($user->role_ids as $roleId){
                    $query = $query->orWhereJsonContains('usergroups.roles', "$roleId");
                  }
              });
        }


        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id' => [
                'name'  => 'usergroups.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
                'visible' => false
            ],
            'name' => [
                'name'  => 'usergroups.name',
                'title' => 'User Group Name',
                'class' => 'text-left',
                 'width' => '100px',
            ],
            'created_at' => [
                'name'  => 'usergroups.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],

        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        $buttons = $this->addCreateButton(route('usergroups.create'), 'usergroups.create');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Usergroups::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('usergroups.deletes'), 'usergroups.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'usergroups.name' => [
                'title'    => 'User Group Name',
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'usergroups.created_at' => [
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

        if (Auth::user() && Auth::user()->hasPermission('usergroups.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('usergroups.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }
}
