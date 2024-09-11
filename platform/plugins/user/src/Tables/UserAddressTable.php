<?php

namespace Impiger\User\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\User\Repositories\Interfaces\UserAddressInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\User\Models\UserAddress;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class UserAddressTable extends TableAbstract
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
    protected $editPermissions = "user-address.edit";
    protected $deletePermissions = "user-address.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * UserAddressTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param UserAddressInterface $userAddressRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UserAddressInterface $userAddressRepository)
    {
        $this->repository = $userAddressRepository;
        $this->setOption('id', 'plugins-user-address-table');
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

                return CrudHelper::getNameFieldLink($item, 'user-address', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('present_country', function($item) {
				return CrudHelper::formatRows($item->present_country, 'database', 'countries|id|country_name', $item, '');
			})
			->editColumn('present_district', function($item) {
				return CrudHelper::formatRows($item->present_district, 'database', 'district|id|name', $item, '');
			})
			->editColumn('present_county', function($item) {
				return CrudHelper::formatRows($item->present_county, 'database', 'county|id|name', $item, '');
			})
			->editColumn('present_phonecode', function($item) {
				return CrudHelper::formatRows($item->present_phonecode, 'database', 'countries|id|phone_code', $item, '');
			})
			->editColumn('permanent_country', function($item) {
				return CrudHelper::formatRows($item->permanent_country, 'database', 'countries|id|country_name', $item, '');
			})
			->editColumn('permanent_district', function($item) {
				return CrudHelper::formatRows($item->permanent_district, 'database', 'district|id|name', $item, '');
			})
			->editColumn('permanent_county', function($item) {
				return CrudHelper::formatRows($item->permanent_county, 'database', 'county|id|name', $item, '');
			})
			->editColumn('created_at', function($item) {
				return CrudHelper::formatDateTime($item->created_at);
			})
			->editColumn('permanent_phonecode', function($item) {
				return CrudHelper::formatRows($item->permanent_phonecode, 'database', 'countries|id|phone_code', $item, '');
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


                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='user-addresses/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
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
            'user_address.id',
			'user_address.present_phone',
			'user_address.present_add_1',
			'user_address.present_add_2',
			'user_address.present_country',
			'user_address.present_district',
			'user_address.present_county',
			'user_address.present_phonecode',
			'user_address.present_zipcode',
			'user_address.same_as_present',
			'user_address.permanent_add_1',
			'user_address.permanent_add_2',
			'user_address.permanent_country',
			'user_address.permanent_district',
			'user_address.permanent_county',
			'user_address.permanent_zipcode',
			'user_address.created_at',
			'user_address.permanent_phonecode',
			'user_address.updated_at',
			'user_address.permanent_phone',
			'user_address.deleted_at'
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
            'present_phone' => [
			'name' => 'present_phone',
			'title' => 'Present Phone',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_add_1' => [
			'name' => 'present_add_1',
			'title' => 'Present Add 1',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_add_2' => [
			'name' => 'present_add_2',
			'title' => 'Present Add 2',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_country' => [
			'name' => 'present_country',
			'title' => 'Present Country',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_district' => [
			'name' => 'present_district',
			'title' => 'Present District',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_county' => [
			'name' => 'present_county',
			'title' => 'Present County',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_phonecode' => [
			'name' => 'present_phonecode',
			'title' => 'Present Phonecode',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'present_zipcode' => [
			'name' => 'present_zipcode',
			'title' => 'Present Zipcode',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'same_as_present' => [
			'name' => 'same_as_present',
			'title' => 'Same As Present',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_add_1' => [
			'name' => 'permanent_add_1',
			'title' => 'Permanent Add 1',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_add_2' => [
			'name' => 'permanent_add_2',
			'title' => 'Permanent Add 2',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_country' => [
			'name' => 'permanent_country',
			'title' => 'Permanent Country',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_district' => [
			'name' => 'permanent_district',
			'title' => 'Permanent District',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_county' => [
			'name' => 'permanent_county',
			'title' => 'Permanent County',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_zipcode' => [
			'name' => 'permanent_zipcode',
			'title' => 'Permanent Zipcode',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'created_at' => [
			'name' => 'created_at',
			'title' => 'Created At',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_phonecode' => [
			'name' => 'permanent_phonecode',
			'title' => 'Permanent Phonecode',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'permanent_phone' => [
			'name' => 'permanent_phone',
			'title' => 'Permanent Phone',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
			],
			'deleted_at' => [
			'name' => 'deleted_at',
			'title' => 'Deleted At',
			'width' => '100',
			'class' => 'text-left',
			'visible' => false
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

        $buttons = $this->addCreateButton(route('user-address.create'), 'user-address.create');


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, UserAddress::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('user-address.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('user-address.deletes'), 'user-address.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('user-address', $isFilter, );
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

        if (Auth::user() && Auth::user()->hasPermission('user-address.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('user-address.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'user-address.edit';
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
