<?php

namespace Impiger\InnovationVoucherProgram\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpKnowledgePartnerInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\InnovationVoucherProgram\Models\IvpKnowledgePartner;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class IvpKnowledgePartnerTable extends TableAbstract
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
    protected $editPermissions = "ivp-knowledge-partner.edit";
    protected $deletePermissions = "ivp-knowledge-partner.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * IvpKnowledgePartnerTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param IvpKnowledgePartnerInterface $ivpKnowledgePartnerRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, IvpKnowledgePartnerInterface $ivpKnowledgePartnerRepository)
    {
        $this->repository = $ivpKnowledgePartnerRepository;
        $this->setOption('id', 'plugins-ivp-knowledge-partner-table');
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
                return CrudHelper::getNameFieldLink($item, 'ivp-knowledge-partner', $isEdit, $isPublic);
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
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='ivp-knowledge-partners/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'ivp_knowledge_partner_details.*'
        ];

        $query = $model->select($select)->whereNotNull('ivp_knowledge_partner_details.id');

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
			'organization_type' => [
			'name' => 'organization_type',
			'title' => 'Organization Type',
			'width' => '100',
			'class' => 'text-left'
			],
			'organization_name' => [
			'name' => 'organization_name',
			'title' => 'Organization Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'contact_person' => [
			'name' => 'contact_person',
			'title' => 'Contact Person',
			'width' => '100',
			'class' => 'text-left'
			],
			'designation' => [
			'name' => 'designation',
			'title' => 'Designation',
			'width' => '100',
			'class' => 'text-left'
			],
			'mobile_number' => [
			'name' => 'mobile_number',
			'title' => 'Mobile Number',
			'width' => '100',
			'class' => 'text-left'
			],
			'email_id' => [
			'name' => 'email_id',
			'title' => 'Email Id',
			'width' => '100',
			'class' => 'text-left'
			],
			'responsibilities' => [
			'name' => 'responsibilities',
			'title' => 'Responsibilities',
			'width' => '100',
			'class' => 'text-left'
			],
			'attachment' => [
			'name' => 'attachment',
			'title' => 'Attachment',
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

        $buttons = $this->addCreateButton(route('ivp-knowledge-partner.create'), 'ivp-knowledge-partner.create');
        
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, IvpKnowledgePartner::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('ivp-knowledge-partner.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('ivp-knowledge-partner.deletes'), 'ivp-knowledge-partner.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('ivp-knowledge-partner', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('ivp-knowledge-partner.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('ivp-knowledge-partner.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'ivp-knowledge-partner.edit';
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
