<?php

namespace Impiger\InnovationVoucherProgram\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\InnovationVoucherProgramInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;

class InnovationVoucherProgramTable extends TableAbstract {
    use WorkflowProperty;
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
    protected $editPermissions = "innovation-voucher-program.edit";
    protected $deletePermissions = "innovation-voucher-program.destroy";
    /* @customized by Sabari Shankar.Parthiban */
    protected $printPreview = 'base.print';

    /**
     * InnovationVoucherProgramTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param InnovationVoucherProgramInterface $innovationVoucherProgramRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, InnovationVoucherProgramInterface $innovationVoucherProgramRepository) {
        $this->repository = $innovationVoucherProgramRepository;
        $this->setOption('id', 'plugins-innovation-voucher-program-table');
        parent::__construct($table, $urlGenerator);
    }

    /**
     * {@inheritDoc}
     */
    public function ajax() {
        $data = $this->table
                ->eloquent($this->query())
                ->editColumn('name', function ($item) {
                    $this->checkDefault($item);
                    $isEdit = (!empty($this->editPermissions));
                    $isPublic = $this->getOption('shortcode');


                    return CrudHelper::getNameFieldLink($item, 'innovation-voucher-program', $isEdit, $isPublic);
                })
                ->editColumn('checkbox', function ($item) {
                    return $this->getCheckbox($item->id);
                })
                ->editColumn('voucher_type', function($item) {
                    return CrudHelper::formatRows($item->voucher_type, 'database', 'attribute_options|id|name', $item, '');
                })
                ->editColumn('created_by', function($item) {
                    return CrudHelper::formatRows($item->created_by, 'database', 'users|id|first_name:last_name', $item, '');
                })
                ->editColumn('wf_status', function($item) {
                    return ucfirst($item->wf_status);
                })
                ->editColumn('is_enabled', function($item) {
				return CrudHelper::formatRows($item->is_enabled, 'radio', '1:Active,0:In-Active', $item, '');
			})
                ->editColumn('created_at', function($item) {
                    return CrudHelper::formatDateTime($item->created_at);
                })
                ->editColumn('created_at', function ($item) {
                    return BaseHelper::formatDate($item->created_at);
                })
                ->addColumn('operations', function ($item) {
            $editPermissions = $this->editPermissions;
            $deletePermissions = $this->deletePermissions;
            $this->checkDefault($item);
            /*  @customized Sabari Shankar.Parthiban start */
            $permissions = $this->checkPermission($item);

            return $this->getOperations($permissions['edit'], $permissions['delete'], $item, "<a  href='innovation-voucher-programs/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\InnovationVoucherProgram\Models\InnovationVoucherProgram', 'innovation-voucher-program.enable_disable',''));
        });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query() {
        $model = $this->repository->getModel();
        $select = [
            'innovation_voucher_programs.id',
            'innovation_voucher_programs.voucher_type',
            'innovation_voucher_programs.application_number',
            'innovation_voucher_programs.project_title',
            'innovation_voucher_programs.wf_status',
            'innovation_voucher_programs.is_enabled',
            'innovation_voucher_programs.created_by',
            'innovation_voucher_programs.created_at'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns() {
        return [
            'application_number' => [
                'name' => 'application_number',
                'title' => 'Application No',
                'width' => '30',
                'class' => 'text-left'
            ],
            'created_at' => [
                'name' => 'created_at',
                'title' => 'Applied Date',
                'width' => '30',
                'class' => 'text-left'
            ],
            'voucher_type' => [
                'name' => 'voucher_type',
                'title' => 'Voucher Type',
                'width' => '50',
                'class' => 'text-left'
            ],
            'created_by' => [
                'name' => 'created_by',
                'title' => 'Applicant Name',
                'width' => '50',
                'class' => 'text-left'
            ],
            'project_title' => [
                'name' => 'project_title',
                'title' => 'Project Title',
                'width' => '100',
                'class' => 'text-left'
            ],
            'wf_status' => [
                'name' => 'wf_status',
                'title' => 'Current Status',
                'width' => '120',
                'class' => 'text-left'
            ],
            
            'is_enabled' => [
            'name' => 'is_enabled',
            'title' => 'Is Enabled',
            'width' => '100',
            'class' => 'text-left'
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons() {
        if (!$this->hasActions) {
            return [];
        }

        $buttons = $this->addCreateButton(route('innovation-voucher-program.create'), 'innovation-voucher-program.create');


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, InnovationVoucherProgram::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array {
        if (Auth::user() && !Auth::user()->hasPermission('innovation-voucher-program.edit')) {
            return [];
        }
        return $this->addDeleteAction(route('innovation-voucher-program.deletes'), 'innovation-voucher-program.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array {
        return CrudHelper::getBulkChanges('innovation-voucher-program', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array {
        $filters = $this->getBulkChanges(true);
        $workflow = [];
        if (is_plugin_active('workflows') && $this->isWorkflowSupport('innovation_voucher_programs')) {
            $workflowStates = \CustomWorkflow::getWorkflowAllStates('innovation_voucher_programs');
            foreach ($workflowStates as $key => $value) {
                $workflow[$value] = ucfirst($value);
            }
        }
        $filters['innovation_voucher_programs.wf_status'] = [
            'title' => 'Status',
            'type' => 'select',
            'choices' => $workflow
        ];
        Arr::forget($filters, ['innovation_voucher_programs.created_by']);
        return $filters;
    }

    /**
     * @return array
     */
    public function getDefaultButtons(): array {
        $defaultBtns = parent::getDefaultButtons();

        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user() && Auth::user()->hasPermission('innovation-voucher-program.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('innovation-voucher-program.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item) {
        if (isset($item->is_default) && $item->is_default) {
            if (Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())) {
                $this->editPermissions = 'innovation-voucher-program.edit';
            } else {
                $this->editPermissions = '';
            }
            $this->deletePermissions = '';
        }
    }

    public function setTableConfig($config): self {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }
    /* @Customized By Sabari Shankar Parthiban Start */

    public function checkPermission($item) {
        $permissions = [
            'edit' => $this->editPermissions,
            'delete' => $this->deletePermissions
        ];
        if (Auth::id() && !in_array(getRoleIdFromSlug(SUPERADMIN_ROLE_SLUG), Auth::user()->role_ids)) {
            if (Auth::id() && $item->created_by != Auth::id()) {
                $permissions['edit'] = '';
                $permissions['delete'] = '';
            }
            if (is_plugin_active('workflows') && $this->isWorkflowSupport('innovation_voucher_programs') && $item->wf_status != \CustomWorkflow::getInitialState('innovation_voucher_programs')) {
                    $permissions['edit'] = '';
                    $permissions['delete'] = '';
            }
        }

        return $permissions;
    }
}
