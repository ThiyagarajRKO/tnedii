<?php

namespace Impiger\User\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\User\Repositories\Interfaces\UserInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\User\Models\User;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class UserTable extends TableAbstract
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
    protected $editPermissions = "user.edit";
    protected $deletePermissions = "user.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * UserTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param UserInterface $userRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UserInterface $userRepository)
    {
        $this->repository = $userRepository;
        $this->setOption('id', 'plugins-user-table');
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

                return CrudHelper::getNameFieldLink($item, 'user', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('photo', function($item) {
				return CrudHelper::formatRows($item->photo, 'image', '/storage/', $item, '');
			})

            ->filterColumn('phone_code', function($query, $keyword) {
                $sql = 'countries.phone_code  like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })

            ->filterColumn('present_phone', function($query, $keyword) {
                $sql = 'user_address.present_phone  like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
			->editColumn('role', function($item) {
				return CrudHelper::formatRows($item->role, 'database', 'roles|id|name', $item, '');
			})
            ->filterColumn('role', function($query, $keyword) {
                $sql = 'roles.name  like ?';
				$query->whereRaw($sql, ["%{$keyword}%"]);
            })
			->editColumn('is_enabled', function($item) {
				return CrudHelper::formatRows($item->is_enabled, 'radio', '1:Active,0:In-Active', $item, '');
			})
			->editColumn('created_at', function($item) {
				return CrudHelper::formatDateTime($item->created_at);
			})
			->editColumn('updated_at', function($item) {
				return CrudHelper::formatDateTime($item->updated_at);
			})

            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);


                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='users/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>".CrudHelper::getRowActivationActionBtn($item,'Impiger\User\Models\User', 'user.enable_disable','').apply_filters(ADD_CUSTOM_ACTION,'',$this->repository->getModel(),$item));
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
            'impiger_users.*',DB::raw('GROUP_CONCAT(role_users.role_id) AS role'),DB::raw('GROUP_CONCAT(role_users.role_id) AS role_id'),'countries.phone_code','user_address.present_phone'
        ];

        $query = $model->select($select)->leftJoin('role_users','impiger_users.user_id','=','role_users.user_id')->leftJoin('roles','roles.id','=','role_users.role_id')->leftJoin('user_address','user_address.imp_user_id','=','impiger_users.id')->leftJoin('countries','countries.id','=','user_address.present_phonecode')->whereNotNull('impiger_users.user_id')->groupBy('role_users.user_id');

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
			'first_name' => [
			'name' => 'first_name',
			'title' => 'First Name',
			'width' => '100',
			'class' => 'text-left'
			],
			'last_name' => [
			'name' => 'last_name',
			'title' => 'Last Name',
			'width' => '100',
			'class' => 'text-left'
			],

			'email' => [
			'name' => 'email',
			'title' => 'Email',
			'width' => '100',
			'class' => 'text-left'
			],
			'designation' => [
			'name' => 'designation',
			'title' => 'Designation',
			'width' => '100',
			'class' => 'text-left'
			],
			'phone_number' => [
			'name' => 'phone_number',
			'title' => 'Contact Number',
			'width' => '100',
			'class' => 'text-left'
			],
			'role' => [
			'name' => 'role',
			'title' => 'Role',
			'width' => '100',
			'class' => 'text-left'
			],
			'is_enabled' => [
			'name' => 'is_enabled',
			'title' => 'Is Enabled',
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
			'updated_at' => [
			'name' => 'updated_at',
			'title' => 'Updated At',
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

        $buttons = $this->addCreateButton(route('user.create'), 'user.create');


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, User::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('user.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('user.deletes'), 'user.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('user', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        $filters = $this->getBulkChanges(true);
        Arr::forget($filters,['impiger_users.entity_type','impiger_users.entity_id']);
        return $filters;
    }

    /**
     * @param Builder $query
     * @param string $key
     * @param string $operator
     * @param string $value
     * @return Builder
     */
    public function applyFilterCondition($query, string $key, string $operator, ?string $value)
    {
		switch ($key) {
                    case 'impiger_users.role':
                        if ($value == "") {
                            break;
                        }

                        return $query->where("roles.id",$operator, $value);
            }
        return CrudHelper::applyFilterCondition($this->repository, $query,  $key,  $operator, $value);
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

       /* if (Auth::user() && Auth::user()->hasPermission('user.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('user.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }*/

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'user.edit';
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
