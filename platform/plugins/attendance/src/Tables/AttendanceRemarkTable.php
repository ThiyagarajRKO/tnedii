<?php

namespace Impiger\Attendance\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Attendance\Repositories\Interfaces\AttendanceRemarkInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Attendance\Models\AttendanceRemark;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class AttendanceRemarkTable extends TableAbstract
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
    protected $editPermissions = "attendance-remark.edit";
    protected $deletePermissions = "attendance-remark.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * AttendanceRemarkTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param AttendanceRemarkInterface $attendanceRemarkRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, AttendanceRemarkInterface $attendanceRemarkRepository)
    {
        $this->repository = $attendanceRemarkRepository;
        $this->setOption('id', 'plugins-attendance-remark-table');
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
                $this->checkDefault($item);
                $isEdit =  (!empty($this->editPermissions));
                $isPublic =  $this->getOption('shortcode');

                #{hideOnEditLink}
                return CrudHelper::getNameFieldLink($item, 'attendance-remark', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('training_title_id', function($item) { 
				return CrudHelper::formatRows($item->training_title_id, 'database', 'training_title|id|name', $item, '');
			})
			->editColumn('entrepreneur_id', function($item) { 
				return CrudHelper::formatRows($item->entrepreneur_id, 'database', 'entrepreneurs|id|name', $item, '');
			})
			->editColumn('created_by', function($item) { 
				return CrudHelper::formatRows($item->created_by, 'database', 'users|id|username', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='attendance-remarks/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'attendance_remarks.id',
			'attendance_remarks.training_title_id',
			'attendance_remarks.entrepreneur_id',
			'attendance_remarks.remark',
			'attendance_remarks.created_by'
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
            'training_title_id' => [
			'name' => 'training_title_id',
			'title' => 'Training Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'entrepreneur_id' => [
			'name' => 'entrepreneur_id',
			'title' => 'Entrepreneur Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'remark' => [
			'name' => 'remark',
			'title' => 'Remark',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_by' => [
			'name' => 'created_by',
			'title' => 'Created By',
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

        $buttons = $this->addCreateButton(route('attendance-remark.create'), 'attendance-remark.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, AttendanceRemark::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('attendance-remark.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('attendance-remark.deletes'), 'attendance-remark.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('attendance-remark', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('attendance-remark.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('attendance-remark.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'attendance-remark.edit';
            }else{
                $this->editPermissions = '';
            }
            $this->deletePermissions = '';
        }
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
