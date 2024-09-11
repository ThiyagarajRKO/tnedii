<?php

namespace Impiger\InnovationVoucherProgram\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpCompanyDetailsInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\InnovationVoucherProgram\Models\IvpCompanyDetails;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class IvpCompanyDetailsTable extends TableAbstract
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
    protected $editPermissions = "ivp-company-details.edit";
    protected $deletePermissions = "ivp-company-details.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * IvpCompanyDetailsTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param IvpCompanyDetailsInterface $ivpCompanyDetailsRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, IvpCompanyDetailsInterface $ivpCompanyDetailsRepository)
    {
        $this->repository = $ivpCompanyDetailsRepository;
        $this->setOption('id', 'plugins-ivp-company-details-table');
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
                return CrudHelper::getNameFieldLink($item, 'ivp-company-details', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('registration_date', function($item) { 
				return CrudHelper::formatDate($item->registration_date);
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
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='ivp-company-details/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'ivp_company_details.id',
			'ivp_company_details.innovation_voucher_program_id',
			'ivp_company_details.company_name',
			'ivp_company_details.designation',
			'ivp_company_details.company_address',
			'ivp_company_details.company_classification',
			'ivp_company_details.website',
			'ivp_company_details.certificate',
			'ivp_company_details.registration_number',
			'ivp_company_details.registration_date',
			'ivp_company_details.annual_turnover',
			'ivp_company_details.no_of_employees',
			'ivp_company_details.created_at',
			'ivp_company_details.updated_at',
			'ivp_company_details.deleted_at'
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
            'innovation_voucher_program_id' => [
			'name' => 'innovation_voucher_program_id',
			'title' => 'Innovation Voucher Program Id',
			'width' => '100',
			'class' => 'text-left'
			],
			'company_name' => [
			'name' => 'company_name',
			'title' => 'Company Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'designation' => [
			'name' => 'designation',
			'title' => 'Designation',
			'width' => '100',
			'class' => 'text-left'
			],
			'company_address' => [
			'name' => 'company_address',
			'title' => 'Company Address',
			'width' => '100',
			'class' => 'text-left'
			],
			'company_classification' => [
			'name' => 'company_classification',
			'title' => 'Company Classification',
			'width' => '100',
			'class' => 'text-left'
			],
			'website' => [
			'name' => 'website',
			'title' => 'Website',
			'width' => '100',
			'class' => 'text-left'
			],
			'certificate' => [
			'name' => 'certificate',
			'title' => 'Certificate',
			'width' => '100',
			'class' => 'text-left'
			],
			'registration_number' => [
			'name' => 'registration_number',
			'title' => 'Registration Number',
			'width' => '100',
			'class' => 'text-left'
			],
			'registration_date' => [
			'name' => 'registration_date',
			'title' => 'Registration Date',
			'width' => '100',
			'class' => 'text-left'
			],
			'annual_turnover' => [
			'name' => 'annual_turnover',
			'title' => 'Annual Turnover',
			'width' => '100',
			'class' => 'text-left'
			],
			'no_of_employees' => [
			'name' => 'no_of_employees',
			'title' => 'No Of Employees',
			'width' => '100',
			'class' => 'text-left'
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
			'width' => '100',
			'class' => 'text-left'
			],
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
			'width' => '100',
			'class' => 'text-left'
			],
			'deleted_at' => [
			'name' => 'deleted_at',
			'title' => 'Deleted At',
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

        $buttons = $this->addCreateButton(route('ivp-company-details.create'), 'ivp-company-details.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, IvpCompanyDetails::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('ivp-company-details.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('ivp-company-details.deletes'), 'ivp-company-details.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('ivp-company-details', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('ivp-company-details.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('ivp-company-details.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'ivp-company-details.edit';
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
