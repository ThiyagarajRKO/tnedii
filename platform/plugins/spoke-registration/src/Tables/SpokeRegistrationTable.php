<?php

namespace Impiger\SpokeRegistration\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeRegistrationInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\SpokeRegistration\Models\SpokeRegistration;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;


class SpokeRegistrationTable extends TableAbstract
{
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
    protected $editPermissions = "spoke-registration.edit";
    protected $deletePermissions = "spoke-registration.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * SpokeRegistrationTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param SpokeRegistrationInterface $spokeRegistrationRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, SpokeRegistrationInterface $spokeRegistrationRepository)
    {
        $this->repository = $spokeRegistrationRepository;
        $this->setOption('id', 'plugins-spoke-registration-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'spoke-registration', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('stream_of_institution', function($item) { 
				return CrudHelper::formatRows($item->stream_of_institution, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('category', function($item) { 
				return CrudHelper::formatRows($item->category, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('affiliation', function($item) { 
				return CrudHelper::formatRows($item->affiliation, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('hub_institution_id', function($item) { 
				return CrudHelper::formatRows($item->hub_institution_id, 'database', 'hub_institutions|id|name', $item, '');
			})
			->editColumn('locality_type', function($item) { 
				return CrudHelper::formatRows($item->locality_type, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('institute_state', function($item) { 
				return CrudHelper::formatRows($item->institute_state, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('program_level', function($item) { 
				return CrudHelper::formatRows($item->program_level, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('has_incubator', function($item) { 
				return CrudHelper::formatRows($item->has_incubator, 'radio', '1:Yes,0:No,:No', $item, '');
			})
			->editColumn('district_id', function($item) { 
				return CrudHelper::formatRows($item->district_id, 'database', 'district|id|name', $item, '');
			})
			->editColumn('internet', function($item) { 
				return CrudHelper::formatRows($item->internet, 'radio', '1:Yes,0:No,:No', $item, '');
			})
			->editColumn('telephone', function($item) { 
				return CrudHelper::formatRows($item->telephone, 'radio', '1:Yes,0:No,:No', $item, '');
			})
			->editColumn('wf_status', function($item) { 
				return \Str::ucfirst($item->wf_status);
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='spoke-registrations/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'spoke_registration.*'
        ];

        $query = $model->select($select)->whereNotNull('spoke_registration.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'name' => [
			'name' => 'name',
			'title' => 'Name Of Institution',
			'width' => '100',
			'class' => 'text-left'
			],
			'stream_of_institution' => [
			'name' => 'stream_of_institution',
			'title' => 'Stream Of Institution',
			'width' => '100',
			'class' => 'text-left'
			],
			
			'hub_institution_id' => [
			'name' => 'hub_institution_id',
			'title' => 'Hub',
			'width' => '100',
			'class' => 'text-left'
			],
			'wf_status' => [
			'name' => 'wf_status',
			'title' => 'Status',
			'width' => '100',
			'class' => 'text-left'
			],
			
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

        $buttons = $this->addCreateButton(route('spoke-registration.create'), 'spoke-registration.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, SpokeRegistration::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('spoke-registration.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('spoke-registration.deletes'), 'spoke-registration.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('spoke-registration', $isFilter );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
         $filters = $this->getBulkChanges(true);
        $workflow = [];
        if (is_plugin_active('workflows') && $this->isWorkflowSupport('spoke_registration')) {
            $workflowStates = \CustomWorkflow::getWorkflowAllStates('spoke_registration');
            foreach ($workflowStates as $key => $value) {
                $workflow[$value] = ucfirst($value);
            }
        }
        $filters['spoke_registration.hub_institution_id'] = [
            'title' => 'Hub Institutions',
            'type' => 'select',
            'choices' => CrudHelper::getSelectBoxChoices(["option" => ['lookup_table' => 'hub_institutions', 'lookup_value' => 'name', 'lookup_key' => 'id']], 'external')
        ];
        $filters['spoke_registration.wf_status'] = [
            'title' => 'Status',
            'type' => 'select',
            'choices' => $workflow
        ];
        Arr::forget($filters, ['spoke_registration.year_of_establishment','spoke_registration.pin_code','spoke_registration.phone_no','spoke_registration.email',
                               'spoke_registration.internet','spoke_registration.telephone' ]);
        return $filters;
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

        if (Auth::user() && Auth::user()->hasPermission('spoke-registration.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('spoke-registration.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'spoke-registration.edit';
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
