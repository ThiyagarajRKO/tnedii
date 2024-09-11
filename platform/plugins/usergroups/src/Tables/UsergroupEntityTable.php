<?php

namespace Impiger\Usergroups\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupEntityInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Usergroups\Models\UsergroupEntity;
use Html;
use App\Utils\CrudHelper;

class UsergroupEntityTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = false;

    protected $printPreview = 'base.print';
    /**
     * UsergroupsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param UsergroupsInterface $usergroupsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UsergroupEntityInterface $usergroupsRepository)
    {
        $this->repository = $usergroupsRepository;
        $this->setOption('id', 'plugins-usergroups-table');
        parent::__construct($table, $urlGenerator);

        if (!Auth::user()->hasAnyPermission(['usergroupsentity.edit', 'usergroupsentity.destroy'])) {
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
                if (!Auth::user()->hasPermission('usergroupsentity.edit')) {
                    return $item->name;
                }
                return Html::link(route('usergroupsentity.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return CrudHelper::formatDateTime($item->created_at);
            });


        return apply_filters(BASE_FILTER_GET_LIST_DATA, $data, $this->repository->getModel())
            ->addColumn('operations', function ($item) {
                return $this->getOperations('', '', $item);
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
            'usergroup_entity.id',
            'usergroups.name as usergroups',
            'cruds.module_title as entity',
            'usergroup_entity.created_at',
        ];

        $query = $model->leftJoin('usergroups', 'usergroups.id', '=', 'usergroup_entity.usergroup_id')
                ->leftJoin('cruds', 'cruds.id', '=', 'usergroup_entity.crud_id')
                ->select($select);

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
            ],
            'entity' => [
                'name'  => 'cruds.module_title',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'usergroups' => [
                'name'  => 'usergroups.name',
                'title' => trans('core/base::tables.description'),
                'class' => 'text-left',
            ],
            'created_at' => [
                'name'  => 'usergroup_entity.created_at',
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
        $buttons = $this->addCreateButton(route('usergroupsentity.create'), 'usergroupsentity.map');

        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Usergroups::class);
    }

    /**
     * {@inheritDoc}
     */
//    public function bulkActions(): array
//    {
//        return $this->addDeleteAction(route('usergroupsentity.deletes'), 'usergroupsentity.destroy', parent::bulkActions());
//    }
//
//    /**
//     * {@inheritDoc}
//     */
//    public function getBulkChanges(): array
//    {
//        return [
//            'usergroups.name' => [
//                'title'    => trans('core/base::tables.name'),
//                'type'     => 'text',
//                'validate' => 'required|max:120',
//            ],
////            'usergroups.status' => [
////                'title'    => trans('core/base::tables.status'),
////                'type'     => 'select',
////                'choices'  => BaseStatusEnum::labels(),
////                'validate' => 'required|in:' . implode(',', BaseStatusEnum::values()),
////            ],
//            'usergroups.created_at' => [
//                'title' => trans('core/base::tables.created_at'),
//                'type'  => 'date',
//            ],
//        ];
//    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges();
    }
}
