<?php

namespace Impiger\Entrepreneur\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\Entrepreneur\Models\Entrepreneur;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class EntrepreneurTable extends TableAbstract
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
    protected $view = "plugins/crud::import.list";
    protected $editPermissions = "entrepreneur.edit";
    protected $deletePermissions = "entrepreneur.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * EntrepreneurTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param EntrepreneurInterface $entrepreneurRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, EntrepreneurInterface $entrepreneurRepository)
    {
        $this->repository = $entrepreneurRepository;
        $this->setOption('id', 'plugins-entrepreneur-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'entrepreneur', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('candidate_type_id', function($item) { 
				return CrudHelper::formatRows($item->candidate_type_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('prefix_id', function($item) { 
				return CrudHelper::formatRows($item->prefix_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('care_of', function($item) { 
				return CrudHelper::formatRows($item->care_of, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('community', function($item) { 
				return CrudHelper::formatRows($item->community, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('gender_id', function($item) { 
				return CrudHelper::formatRows($item->gender_id, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('district_id', function($item) { 
				return CrudHelper::formatRows($item->district_id, 'database', 'district|id|name', $item, '');
			})
            ->editColumn('dob', function($item) { 
				return CrudHelper::formatDate($item->dob);
			})
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='entrepreneurs/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'entrepreneurs.*'
        ];

        $query = $model->select($select)->whereNotNull('entrepreneurs.id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'candidate_type_id' => [
			'name' => 'candidate_type_id',
			'title' => 'Candidate Type',
			'width' => '100',
			'class' => 'text-left'
			],
			'prefix_id' => [
			'name' => 'prefix_id',
			'title' => 'Prefix',
			'width' => '100',
			'class' => 'text-left'
			],
            'name' => [
            'name' => 'name',
            'title' => 'Name',
            'width' => '100',
            'class' => 'text-left'
            ],			
			'gender_id' => [
            'name' => 'gender_id',
            'title' => 'Gender',
            'width' => '100',
            'class' => 'text-left'
            ],
			'dob' => [
			'name' => 'dob',
			'title' => 'Dob',
			'width' => '100',
			'class' => 'text-left'
			],
           
			'mobile' => [
			'name' => 'mobile',
			'title' => 'Mobile',
			'width' => '100',
			'class' => 'text-left'
			],
			'email' => [
			'name' => 'email',
			'title' => 'Email',
			'width' => '100',
			'class' => 'text-left'
			],
            'community' => [
            'name' => 'community',
            'title' => 'Community',
            'width' => '100',
            'class' => 'text-left'
            ],
            'care_of' => [
			'name' => 'care_of',
			'title' => 'Care Of',
			'width' => '100',
			'class' => 'text-left'
			],
			'father_name' => [
			'name' => 'father_name',
			'title' => 'Father Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'district_id' => [
			'name' => 'district_id',
			'title' => 'District',
			'width' => '100',
			'class' => 'text-left'
			],
			'pincode' => [
			'name' => 'pincode',
			'title' => 'Pincode',
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

        $buttons = $this->addCreateButton(route('entrepreneur.create'), 'entrepreneur.create');
        $buttons['bulk-upload'] = ['link' => '#','text' => view('plugins/crud::import.import')->render()];
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, Entrepreneur::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('entrepreneur.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('entrepreneur.deletes'), 'entrepreneur.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('entrepreneur', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('entrepreneur.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('entrepreneur.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'entrepreneur.edit';
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
