<?php

namespace Impiger\KnowledgePartner\Tables;

use BaseHelper;
use Impiger\KnowledgePartner\Exports\KnowledgePartnerExport;
use Html;
use Illuminate\Support\Facades\Auth;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

use App\Utils\CrudHelper;

class KnowledgePartnerTable extends TableAbstract
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
    protected $exportClass = KnowledgePartnerExport::class;
    protected $printPreview = 'plugins/crud::print';
    /**
     * KnowledgePartnerTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param KnowledgePartnerInterface $knowledgePartnerRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, KnowledgePartnerInterface $knowledgePartnerRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $knowledgePartnerRepository;


    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('state_id', function($item) { 
				return CrudHelper::formatRows($item->state_id, 'database', 'states|state_id|state_name', $item, '');
			})
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
           /*->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })*/
            ->addColumn('operations', function ($item) {
                return $this->getOperations('', 'knowledge-partner.destroy', $item, "<a  href='knowledge-partners/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'knowledge_partners.id',
            'knowledge_partners.name_of_the_institution',
            'knowledge_partners.state_id',
            'knowledge_partners.district',
            'knowledge_partners.district_id',
            'knowledge_partners.pin_code',
            'knowledge_partners.gst_no',
            'knowledge_partners.pan',
            'knowledge_partners.tin',
            'knowledge_partners.contact_person',
            'knowledge_partners.mobile_number',
            'knowledge_partners.email_address',
            'knowledge_partners.website',
            'knowledge_partners.created_at',
            //'knowledge_partners.status',
        ];

        $query = $model->select($select)->whereNull('knowledge_partners.deleted_at');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'id'         => [
                'name'  => 'knowledge_partners.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name_of_the_institution'       => [
                'name'  => 'knowledge_partners.name_of_the_institution',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.name_of_the_institution'),
                'class' => 'text-left',
            ],
            'state_id'      => [
                'name'  => 'knowledge_partners.state',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.state'),
            ],
            'district'      => [
                'name'  => 'knowledge_partners.district',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.district'),
                'class' => 'text-left',
            ],
            'pin_code'      => [
                'name'  => 'knowledge_partners.pin_code',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.pin_code'),
            ],
            'gst_no'      => [
                'name'  => 'knowledge_partners.gst_no',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.gst_no'),
            ],
            'pan'      => [
                'name'  => 'knowledge_partners.pan',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.pan'),
            ],
            'tin'      => [
                'name'  => 'knowledge_partners.tin',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.tin'),
            ],
            'contact_person'      => [
                'name'  => 'knowledge_partners.contact_person',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.contact_person'),
            ],
            'mobile_number'      => [
                'name'  => 'knowledge_partners.mobile_number',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.mobile_number'),
            ],
            'email_address'      => [
                'name'  => 'knowledge_partners.email_address',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.email_address'),
            ],
            'website'      => [
                'name'  => 'knowledge_partners.website',
                'title' => trans('plugins/knowledge-partner::knowledge-partner.tables.website'),
            ],
            'created_at' => [
                'name'  => 'knowledge_partners.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            //'status'    => [
            //    'name'  => 'knowledge_partners.status',
            //    'title' => trans('core/base::tables.status'),
            //    'width' => '100px',
            //],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('knowledge-partner.deletes'), 'knowledge-partner.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /*'knowledge_partners.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'knowledge_partners.email'      => [
                'title'    => trans('core/base::tables.email'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'knowledge_partners.phone'      => [
                'title'    => trans('plugins/knowledge-partner::knowledge-partner.sender_phone'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            
            'knowledge_partners.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
            'knowledge_partners.status'    => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => KnowledgePartnerStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(KnowledgePartnerStatusEnum::values()),
            ],*/
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
        $defaultBtns = parent::getDefaultButtons();
        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user() && Auth::user()->hasPermission('knowledge-partner.export')) {
            //$defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('knowledge-partner.print')) {
            //$defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }
}
