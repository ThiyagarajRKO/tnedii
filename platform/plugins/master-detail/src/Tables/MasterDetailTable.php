<?php

namespace Impiger\MasterDetail\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MasterDetail\Repositories\Interfaces\MasterDetailInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\MasterDetail\Models\MasterDetail;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class MasterDetailTable extends TableAbstract
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
    protected $editPermissions = "master-detail.edit";
    protected $deletePermissions = "master-detail.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * MasterDetailTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param MasterDetailInterface $masterDetailRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, MasterDetailInterface $masterDetailRepository)
    {
        $this->repository = $masterDetailRepository;
        $this->setOption('id', 'plugins-master-detail-table');
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
                return CrudHelper::getNameFieldLink($item, 'master-detail', $isEdit, $isPublic);
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
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                if($this->checkDefault($item)){
                  $editPermissions = "";
                  $deletePermissions = "";
                }
                return $this->getOperations($editPermissions, $deletePermissions, $item, "<a data-fancybox data-type='ajax' data-src='master-details/viewdetail/$item->id ' href='javascript:void(0);' class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\MasterDetail\Models\MasterDetail', 'master-detail.enable_disable'));
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
            'attribute_options.id',
			'attribute_options.attribute',
			'attribute_options.name',
			'attribute_options.created_at',
			'attribute_options.updated_at'
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
//            'id' => [
//			'name' => 'id',
//			'title' => 'Id',
//			'width' => '100',
//			'class' => 'text-left'
//			],
			'attribute' => [
			'name' => 'attribute',
			'title' => 'Attribute',
			'width' => '100',
			'class' => 'text-left'
			],
			'name' => [
			'name' => 'name',
			'title' => 'Name',
			'width' => '100',
			'class' => 'text-left'
			],
//			'created_at' => [
//			'name' => 'created_at',
//			'title' => 'Created At',
//			'width' => '100',
//			'class' => 'text-left'
//			],
//			'updated_at' => [
//			'name' => 'updated_at',
//			'title' => 'Updated At',
//			'width' => '100',
//			'class' => 'text-left',
//			'visible' => false
//			]
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

        $buttons = $this->addCreateButton(route('master-detail.create'), 'master-detail.create');


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, MasterDetail::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('master-detail.deletes'), 'master-detail.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        $row = \DB::table('cruds')->where('module_name', 'master-detail')->get()->first();
        $moduleConfig = CF_decode_json($row->module_config);
        $columns = [];
        $bulkChangesType = ['text', 'select', 'text_datetime', 'select-search', 'number', 'date', 'text_date', 'textarea'];
        $gridConfig = [];

        foreach($moduleConfig['grid'] as $grid)
            $gridConfig[$grid['field']] = $grid;

        foreach ($moduleConfig['forms'] as $val) {
            $gConfig = (isset($gridConfig[$val['field']])) ? $gridConfig[$val['field']] : [];
            $isAllowedType = (Arr::get($gConfig, 'view') && in_array($val['type'], $bulkChangesType)) ? true : false;
            if (
                (!$isFilter && $isAllowedType && Arr::get($val, 'bulk_edit')) ||
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

        if (Auth::user() && Auth::user()->hasPermission('master-detail.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('master-detail.print')) {
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
