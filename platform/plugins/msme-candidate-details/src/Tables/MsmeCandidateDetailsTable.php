<?php

namespace Impiger\MsmeCandidateDetails\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\MsmeCandidateDetails\Repositories\Interfaces\MsmeCandidateDetailsInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\MsmeCandidateDetails\Models\MsmeCandidateDetails;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class MsmeCandidateDetailsTable extends TableAbstract
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
    protected $editPermissions = "msme-candidate-details.edit";
    protected $deletePermissions = "msme-candidate-details.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * MsmeCandidateDetailsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param MsmeCandidateDetailsInterface $msmeCandidateDetailsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, MsmeCandidateDetailsInterface $msmeCandidateDetailsRepository)
    {
        $this->repository = $msmeCandidateDetailsRepository;
        $this->setOption('id', 'plugins-msme-candidate-details-table');
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

                
                return CrudHelper::getNameFieldLink($item, 'msme-candidate-details', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('scheme', function($item) { 
				return CrudHelper::formatRows($item->scheme, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('care_of', function($item) { 
				return CrudHelper::formatRows($item->care_of, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('gender', function($item) { 
				return CrudHelper::formatRows($item->gender, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('district_id', function($item) { 
				return CrudHelper::formatRows($item->district_id, 'database', 'district|id|name', $item, '');
			})
			->editColumn('dob', function($item) { 
				return CrudHelper::formatDate($item->dob);
			})
			->editColumn('photo', function($item) { 
				return CrudHelper::formatRows($item->photo, 'file', '/storage/', $item, '');
			})
			->editColumn('created_at', function($item) { 
				return CrudHelper::formatDateTime($item->created_at);
			})
			->editColumn('updated_at', function($item) { 
				return CrudHelper::formatDateTime($item->updated_at);
			})
			->editColumn('deleted_at', function($item) { 
				return CrudHelper::formatDateTime($item->deleted_at);
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('enroll_start_date', function ($item) {
                return BaseHelper::formatDate($item->enroll_start_date);
            })
            ->editColumn('enroll_to_date', function ($item) {
                return BaseHelper::formatDate($item->enroll_to_date);
            })
            ->editColumn('T.certificate_generated_at', function ($item) {
                return BaseHelper::formatDate($item->certificate_generated_at);
            })
            ->filterColumn('T.certificate_generated_at', function($query, $keyword) {
                $sql = 'T.certificate_generated_at like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                
                
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='msme-candidate-details/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'msme_candidate_details.*','T.id AS trainee_id','T.entrepreneur_id','T.training_title_id','T.certificate_status','T.certificate_generated_at'
        ];

        $query = $model->select($select)
        ->join('entrepreneurs AS E','E.msme_candidate_detail_id','=','msme_candidate_details.id')
        ->leftjoin('trainees AS T', 'T.entrepreneur_id', '=', 'E.id')
        //->whereNotNull('msme_candidate_details.id')
        ->whereNotNull('E.msme_candidate_detail_id');
	//$query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select);dd($query->dd());
        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'scheme' => [
			'name' => 'scheme',
			'title' => 'Scheme',
			'width' => '100',
			'class' => 'text-left'
			],
			'candidate_msme_ref_id' => [
			'name' => 'candidate_msme_ref_id',
			'title' => 'Candidate Msme Ref Id',
			'width' => '100',
			'class' => 'text-left'
			],
			'candidate_name' => [
			'name' => 'candidate_name',
			'title' => 'Candidate Name',
			'width' => '100',
			'class' => 'text-left'
			],
			
			'gender' => [
			'name' => 'gender',
			'title' => 'Gender',
			'width' => '100',
			'class' => 'text-left'
			],
			'mobile_no' => [
			'name' => 'mobile_no',
			'title' => 'Mobile No',
			'width' => '100',
			'class' => 'text-left'
			],
			'email' => [
			'name' => 'email',
			'title' => 'Email',
			'width' => '100',
			'class' => 'text-left'
			],
			'dob' => [
			'name' => 'dob',
			'title' => 'Dob',
			'width' => '100',
			'class' => 'text-left'
			],
			'qualification' => [
			'name' => 'qualification',
			'title' => 'Qualification',
			'width' => '100',
			'class' => 'text-left'
			],
			'district_id' => [
			'name' => 'district_id',
			'title' => 'District',
			'width' => '100',
			'class' => 'text-left'
			],
			'address' => [
			'name' => 'address',
			'title' => 'Address',
			'width' => '100',
			'class' => 'text-left'
			],
			'enroll_start_date' => [
			'name' => 'enroll_start_date',
			'title' => 'Start Date',
			'width' => '100',
			'class' => 'text-left'
			],
			'enroll_to_date' => [
			'name' => 'enroll_to_date',
			'title' => 'To Date',
			'width' => '100',
			'class' => 'text-left'
			],
			'T.certificate_generated_at' => [
			'name' => 'T.certificate_generated_at',
			'title' => 'Certificate Generated',
			'width' => '100',
			'class' => 'text-left'
			],
			// 'is_enrolled' => [
			// 'name' => 'is_enrolled',
			// 'title' => 'Is Enrolled',
			// 'width' => '100',
			// 'class' => 'text-left'
			// ],
			
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
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

        $buttons = $this->addCreateButton(route('msme-candidate-details.create'), 'msme-candidate-details.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, MsmeCandidateDetails::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('msme-candidate-details.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('msme-candidate-details.deletes'), 'msme-candidate-details.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('msme-candidate-details', $isFilter );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
		$filters = $this->getBulkChanges(true);
		
		$filters['msme_candidate_details.district_id'] = $filters['msme_candidate_details.district'];
        $filters['msme_candidate_details.enroll_start_date'] = [
            'title' => 'Start Date',
            'type' => 'date'
        ];
        $filters['msme_candidate_details.enroll_to_date'] = [
            'title' => 'To Date',
            'type' => 'date'
        ];
        $filters['T.certificate_generated_at'] = [
            'title' => 'Certificate Generated At',
            'type' => 'date'
        ];
		\Arr::forget($filters,['msme_candidate_details.updated_at','msme_candidate_details.deleted_at']);//dd($filters);
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

        if (Auth::user() && Auth::user()->hasPermission('msme-candidate-details.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('msme-candidate-details.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'msme-candidate-details.edit';
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

    public function applyFilterCondition($query, string $key, string $operator, ?string $value)
    {        
        switch ($key) {
            case 'T.certificate_generated_at':
                if ($value == "") {
                    break;
                }

                return $query->where(DB::raw("DATE(T.certificate_generated_at)"), $operator, $value);
            case 'msme_candidate_details.district':
                if ($value == "") {
                    break;
                }

                return $query->where(DB::raw("msme_candidate_details.district_id"), $operator, $value);
            
        }
        return CrudHelper::applyFilterCondition($this->repository, $query,  $key,  $operator, $value);
    }

}
