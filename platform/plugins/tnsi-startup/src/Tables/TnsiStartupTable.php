<?php

namespace Impiger\TnsiStartup\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TnsiStartup\Repositories\Interfaces\TnsiStartupInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\TnsiStartup\Models\TnsiStartup;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
use Impiger\Workflows\Traits\WorkflowProperty;


class TnsiStartupTable extends TableAbstract
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
    protected $filterTemplate = 'plugins/tnsi-startup::filter';
    protected $view = "core/table::table";
    protected $editPermissions = "tnsi-startup.edit";
    protected $deletePermissions = "tnsi-startup.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    protected $isColumnCustomization = true;
    /**
     * TnsiStartupTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param TnsiStartupInterface $tnsiStartupRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, TnsiStartupInterface $tnsiStartupRepository)
    {
        $this->repository = $tnsiStartupRepository;
        $this->setOption('id', 'plugins-tnsi-startup-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'tnsi-startup', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('hub_institution_id', function($item) { 
				return CrudHelper::formatRows($item->hub_institution_id, 'database', 'hub_institutions|id|name', $item, '');
			})
            ->editColumn('spoke_registration_id', function($item) { 
				return CrudHelper::formatRows($item->spoke_registration_id, 'database', 'spoke_registration|id|name', $item, '');
			})
            ->filterColumn('spoke_registration_id', function($query, $keyword) {
                $sql = 'SR.name like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->editColumn('region_id', function($item) { 
				return CrudHelper::formatRows($item->region_id, 'database', 'regions|id|name', $item, '');
			})
            ->editColumn('idea_about', function($item) { 
				return CrudHelper::formatRows($item->idea_about, 'database', 'attribute_options|id|name', $item, '');
			})
            ->editColumn('is_your_idea', function($item) { 
				return CrudHelper::formatRows($item->is_your_idea, 'database', 'attribute_options|id|name', $item, '');
			})
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->filterColumn('created_at', function($query, $keyword) {
                $sql = 'tnsi_startup.created_at like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->editColumn('is_won', function ($item) {
                // return BaseHelper::formatDate($item->created_at);
                // CrudHelper::getRadioOptionValues('datalist', '1:Yes|0:No')
                return CrudHelper::formatRows($item->is_won, 'radio', '0:No,1:Yes', $item, '');
            })
            ->editColumn('pitch_training', function ($item) {
                return CrudHelper::formatRows($item->pitch_training, 'radio', '0:No,1:Yes', $item, '');
            })
            ->editColumn('is_incubated', function ($item) {
                return CrudHelper::formatRows($item->is_incubated, 'radio', '0:No,1:Yes', $item, '');
            })
            ->editColumn('team_members', function ($item) {
                // return CrudHelper::formatRows($item->team_members, 'radio', '0:No,1:Yes', $item, '');
                // return "ubaidur,ramesh";
                return CrudHelper::jsonToStringFormat($item->team_members);
            })
            ->filterColumn('team_members', function($query, $keyword) {
                $sql = 'tnsi_startup.team_members like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('idea_about', function($query, $keyword) {
                $sql = 'tnsi_startup.idea_about like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->filterColumn('wf_status', function($query, $keyword) {
                $sql = 'tnsi_startup.wf_status like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='tnsi-startups/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'tnsi_startup.*','SR.district_id'
            // 'tnsi_startup.*','SR.district_id','D.region_id','E.name'
        ];

        $query = $model->select($select)
                ->leftJoin('spoke_registration AS SR','SR.id','=','tnsi_startup.spoke_registration_id')
                ->leftJoin('district AS D','D.id','=','SR.district_id')
                // ->leftJoin('entrepreneurs AS E','E.spoke_registration_id','=','tnsi_startup.spoke_registration_id')
                ->whereNotNull('tnsi_startup.id');
        // dd(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select)->dd());
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [            
            'region_id' => [
                'name' => 'D.region_id',
                'title' => 'Region',
                'width' => '100',
                'class' => 'text-left'
			],
            'hub_institution_id' => [
                'name' => 'hub_institution_id',
                'title' => 'Hub Institution',
                'width' => '100',
                'class' => 'text-left'
            ],
            'spoke_registration_id' => [
                'name' => 'spoke_registration_id',
                'title' => 'College Name',
                'width' => '100',
                'class' => 'text-left'
			],
			'name' => [
                'name' => 'name',
                'title' => 'Name of your Startup',
                'width' => '100',
                'class' => 'text-left'
			],           
            'team_members' => [
                'name' => 'team_members',
                'title' => 'Team Members',
                'width' => '100',
                'class' => 'text-left'
            ],
            'idea_about' => [
                'name' => 'idea_about',
                'title' => 'Idea About',
                'width' => '100',
                'class' => 'text-left'
            ],
            'is_your_idea' => [
                'name' => 'is_your_idea',
                'title' => 'Is Your Idea?',
                'width' => '100',
                'class' => 'text-left'
            ],                 
            'about_startup' => [
                'name' => 'about_startup',
                'title' => 'About Your Startup',
                'width' => '100',
                'class' => 'text-left'
            ],   
            'problem_of_address' => [
                'name' => 'problem_of_address',
                'title' => 'Problem Address?',
                'width' => '100',
                'class' => 'text-left'
            ],            
            'solution_of_problem' => [
                'name' => 'solution_of_problem',
                'title' => 'Solutions for the problem',
                'width' => '100',
                'class' => 'text-left'
            ],
            'unique_selling_proposition' => [
                'name' => 'unique_selling_proposition',
                'title' => "Unique Selling Proposition (USP)?",
                'width' => '100',
                'class' => 'text-left'
            ],
            'revenue_stream' => [
                'name' => 'revenue_stream',
                'title' => 'Revenue stream',
                'width' => '100',
                'class' => 'text-left'
            ],
            'description' => [
                'name' => 'description',
                'title' => 'Description',
                'width' => '100',
                'class' => 'text-left'
            ],
            'duration' => [
                'name' => 'duration',
                'title' => 'Duration',
                'width' => '100',
                'class' => 'text-left'
            ],
            'is_won' => [
                'name' => 'is_won',
                'title' => 'Won prize?',
                'width' => '100',
                'class' => 'text-left'
            ],
            'pitch_training' => [
                'name' => 'pitch_training',
                'title' => 'Pitch training',
                'width' => '100',
                'class' => 'text-left'
            ],
            'is_incubated' => [
                'name' => 'is_incubated',
                'title' => 'Business Incubator',
                'width' => '100',
                'class' => 'text-left'
            ],
            'demo_link' => [
                'name' => 'demo_link',
                'title' => 'Demo link',
                'width' => '100',
                'class' => 'text-left'
            ],
            'about_tnsi' => [
                'name' => 'about_tnsi',
                'title' => 'About TNSI?',
                'width' => '100',
                'class' => 'text-left'
            ],			
			'wf_status' => [
                'name' => 'wf_status',
                'title' => 'Status',
                'width' => '100',
                'class' => 'text-left'
            ],
			'created_at' => [
                'name' => 'created_at',
                'title' => 'Application Submitted',
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

        $buttons = $this->addCreateButton(route('tnsi-startup.create'), 'tnsi-startup.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, TnsiStartup::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('tnsi-startup.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('tnsi-startup.deletes'), 'tnsi-startup.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('tnsi-startup', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        $filters = $this->getBulkChanges(true);
        $workflow = [];
        if (is_plugin_active('workflows') && $this->isWorkflowSupport('tnsi_startup')) {
            $workflowStates = \CustomWorkflow::getWorkflowAllStates('tnsi_startup');
            foreach ($workflowStates as $key => $value) {
                $workflow[$value] = ucfirst($value);
            }
        }
        $filters['tnsi_startup.wf_status'] = [
            'title' => 'Status',
            'type' => 'select',
            'choices' => $workflow
        ];
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

        if (Auth::user() && Auth::user()->hasPermission('tnsi-startup.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('tnsi-startup.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'tnsi-startup.edit';
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
