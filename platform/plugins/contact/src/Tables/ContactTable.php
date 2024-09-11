<?php

namespace Impiger\Contact\Tables;

use BaseHelper;
use Impiger\Contact\Exports\ContactExport;
use Html;
use Illuminate\Support\Facades\Auth;
use Impiger\Contact\Enums\ContactStatusEnum;
use Impiger\Contact\Repositories\Interfaces\ContactInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class ContactTable extends TableAbstract
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
    protected $exportClass = ContactExport::class;
    /*@Customized by Sabari Shankar Parthiban start*/
    protected $printPreview = 'plugins/crud::print';
    /*@Customized by Sabari Shankar Parthiban end*/
    /**
     * ContactTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param ContactInterface $contactRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, ContactInterface $contactRepository)
    {
        parent::__construct($table, $urlGenerator);

        $this->repository = $contactRepository;

        if (!Auth::user()->hasAnyPermission(['contacts.edit', 'contacts.destroy'])) {
            $this->hasOperations = false;
            $this->hasActions = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                if (!Auth::user()->hasPermission('contacts.edit')) {
                    return $item->name;
                }

                return Html::link(route('contacts.edit', $item->id), $item->name);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->editColumn('status', function ($item) {
                return $item->status->toHtml();
            })
            ->addColumn('operations', function ($item) {
                return $this->getOperations('contacts.edit', 'contacts.destroy', $item);
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
            'contacts.id',
            'contacts.name',
            'contacts.phone',
            'contacts.email',
            'contacts.created_at',
            'contacts.status',
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
            'id'         => [
                'name'  => 'contacts.id',
                'title' => trans('core/base::tables.id'),
                'width' => '20px',
            ],
            'name'       => [
                'name'  => 'contacts.name',
                'title' => trans('core/base::tables.name'),
                'class' => 'text-left',
            ],
            'email'      => [
                'name'  => 'contacts.email',
                'title' => trans('plugins/contact::contact.tables.email'),
                'class' => 'text-left',
            ],
            'phone'      => [
                'name'  => 'contacts.phone',
                'title' => trans('plugins/contact::contact.tables.phone'),
            ],
            'created_at' => [
                'name'  => 'contacts.created_at',
                'title' => trans('core/base::tables.created_at'),
                'width' => '100px',
            ],
            'status'    => [
                'name'  => 'contacts.status',
                'title' => trans('core/base::tables.status'),
                'width' => '100px',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        return $this->addDeleteAction(route('contacts.deletes'), 'contacts.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges(): array
    {
        return [
            /* @Customized By Sabari Shankar Parthiban
            'contacts.name'       => [
                'title'    => trans('core/base::tables.name'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'contacts.email'      => [
                'title'    => trans('core/base::tables.email'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            'contacts.phone'      => [
                'title'    => trans('plugins/contact::contact.sender_phone'),
                'type'     => 'text',
                'validate' => 'required|max:120',
            ],
            
            'contacts.created_at' => [
                'title' => trans('core/base::tables.created_at'),
                'type'  => 'date',
            ],
            @Customized by Sabari Shankar Parthiban end*/
            'contacts.status'    => [
                'title'    => trans('core/base::tables.status'),
                'type'     => 'select',
                'choices'  => ContactStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(ContactStatusEnum::values()),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultButtons(): array
    {
        /*@Customized by Sabari Shankar Parthiban start
        return [
            'export',
            'reload',
        ];*/
        $defaultBtns = parent::getDefaultButtons();
        $defaultBtns = array_merge($defaultBtns,['export','print']);
        return $defaultBtns;
        /*@Customized by Sabari Shankar Parthiban end*/
    }
}
