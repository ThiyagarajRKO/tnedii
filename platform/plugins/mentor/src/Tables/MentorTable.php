<?php

namespace Impiger\Mentor\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Mentor\Repositories\Interfaces\MentorInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Mentor\Models\Mentor;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class MentorTable extends TableAbstract
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
    protected $editPermissions = "mentor.edit";
    protected $deletePermissions = "mentor.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * MentorTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param MentorInterface $mentorRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, MentorInterface $mentorRepository)
    {
        $this->repository = $mentorRepository;
        $this->setOption('id', 'plugins-mentor-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'mentor', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('entrepreneur_id', function($item) { 
				return CrudHelper::formatRows($item->entrepreneur_id, 'database', 'entrepreneurs|id|name', $item, '');
			})
			->editColumn('vendor_id', function($item) { 
				return CrudHelper::formatRows($item->vendor_id, 'database', 'vendors|id|name', $item, '');
			})
			->editColumn('district_id', function($item) { 
				return CrudHelper::formatRows($item->district_id, 'database', 'district|id|name', $item, '');
			})
			->editColumn('industry_id', function($item) { 
				return CrudHelper::formatRows($item->industry_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('specialization_id', function($item) { 
				return CrudHelper::formatRows($item->specialization_id, 'database', 'specializations|id|name', $item, '');
			})
			->editColumn('experience_id', function($item) { 
				return CrudHelper::formatRows($item->experience_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('last_use_id', function($item) { 
				return CrudHelper::formatRows($item->last_use_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('proficiency_level_id', function($item) { 
				return CrudHelper::formatRows($item->proficiency_level_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('qualification_id', function($item) { 
				return CrudHelper::formatRows($item->qualification_id, 'database', 'qualifications|id|name', $item, '');
			})
			->editColumn('status_id', function($item) { 
				return CrudHelper::formatRows($item->status_id, 'radio', '1:Active,0:Inactive', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='mentors/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'mentors.*'
        ];

        $query = $model->select($select)->whereNotNull('mentors.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'entrepreneur_id' => [
			'name' => 'entrepreneur_id',
			'title' => 'Entrepreneur',
			'width' => '100',
			'class' => 'text-left'
			],
			'email' => [
			'name' => 'email',
			'title' => 'Email',
			'width' => '100',
			'class' => 'text-left'
			],
			'vendor_id' => [
			'name' => 'vendor_id',
			'title' => 'Vendor',
			'width' => '100',
			'class' => 'text-left'
			],
			'district_id' => [
			'name' => 'district_id',
			'title' => 'District',
			'width' => '100',
			'class' => 'text-left'
			],
			'industry_id' => [
			'name' => 'industry_id',
			'title' => 'Industry',
			'width' => '100',
			'class' => 'text-left'
			],
			'specialization_id' => [
			'name' => 'specialization_id',
			'title' => 'Specialization',
			'width' => '100',
			'class' => 'text-left'
			],
			'experience_id' => [
			'name' => 'experience_id',
			'title' => 'Experience',
			'width' => '100',
			'class' => 'text-left'
			],
			'last_use_id' => [
			'name' => 'last_use_id',
			'title' => 'Last Use',
			'width' => '100',
			'class' => 'text-left'
			],
			'proficiency_level_id' => [
			'name' => 'proficiency_level_id',
			'title' => 'Proficiency Level',
			'width' => '100',
			'class' => 'text-left'
			],
			'qualification_id' => [
			'name' => 'qualification_id',
			'title' => 'Qualification',
			'width' => '100',
			'class' => 'text-left'
			],
			'achivements' => [
			'name' => 'achivements',
			'title' => 'Achivements',
			'width' => '100',
			'class' => 'text-left'
			],
			'resume' => [
			'name' => 'resume',
			'title' => 'Resume',
			'width' => '100',
			'class' => 'text-left'
			],
			'status_id' => [
			'name' => 'status_id',
			'title' => 'Status',
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

        $buttons = $this->addCreateButton(route('mentor.create'), 'mentor.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Mentor::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('mentor.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('mentor.deletes'), 'mentor.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('mentor', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('mentor.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('mentor.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'mentor.edit';
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
