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

class UserRegisteredTable extends TableAbstract {

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
    protected $editPermissions = false;
    protected $deletePermissions = false;
    /* @customized by Sabari Shankar.Parthiban */
    protected $printPreview = 'base.print';
    protected $isFrontend = false;

    /**
     * UserTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param UserInterface $userRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, UserInterface $userRepository) {
        $this->repository = $userRepository;
        $this->setOption('id', 'plugins-user-table');
        $pathInfo = \Request::getPathInfo();
        $this->isFrontend = (str_contains($pathInfo, 'admin')) ? false : true;
            parent::__construct($table, $urlGenerator);
    }

    /**
     * {@inheritDoc}
     */
    public function ajax() {
        $data = $this->table
                ->eloquent($this->query())
                ->editColumn('name', function ($item) {
                    $isEdit = (!empty($this->editPermissions) && !$this->checkDefault($item));
                    $isPublic = $this->getOption('shortcode');

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
                ->filterColumn('district', function($query, $keyword) {
                    $sql = 'DIS.name  like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('county', function($query, $keyword) {
                    $sql = 'C.name  like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->filterColumn('specialization', function($query, $keyword) {
                    $sql = 'AO.name like ?';
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->editColumn('nationality', function($item) {
                    return CrudHelper::formatRows($item->nationality, 'database', 'countries|id|nationality', $item, '');
                })
                ->editColumn('dob', function($item) {
                    return CrudHelper::formatDate($item->dob);
                })
                ->editColumn('if_refugee', function($item) {
                    return CrudHelper::formatRows($item->if_refugee, 'radio', '1:Yes,0:No,:No', $item, '');
                })
                ->editColumn('blood_group', function($item) {
                    return CrudHelper::formatRows($item->blood_group, 'database', 'attribute_options|id|name', $item, '');
                })
                ->editColumn('is_enabled', function($item) {
                    return CrudHelper::formatRows($item->is_enabled, 'radio', '1:Active,0:In-Active', $item, '');
                })
                ->editColumn('marital_status', function($item) {
                    return CrudHelper::formatRows($item->marital_status, 'database', 'attribute_options|id|name', $item, '');
                })
                ->editColumn('specialization', function($item) {
                    return CrudHelper::formatRows($item->specialization, 'database', 'attribute_options|id|name', $item, '');
                })
                ->editColumn('registered_user', function($item) {
                    return CrudHelper::formatRows($item->registered_user, 'radio', '1:Yes,0:No', $item, '');
                })
                ->editColumn('religion', function($item) {
                    return CrudHelper::formatRows($item->religion, 'database', 'attribute_options|id|name', $item, '');
                })
                ->editColumn('accredited_status', function($item) {
                    return CrudHelper::formatRows($item->accredited_status, 'database', 'attribute_options|id|name', $item, '');
                })
                ->editColumn('wf_status', function($item) {
                    return ($item->wf_status) ? CrudHelper::formatRows($item->wf_status, 'workflow', '', $item, '') : "";
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
            $editPermissions = $this->editPermissions;
            $deletePermissions = $this->deletePermissions;
            if ($this->checkDefault($item)) {
                $editPermissions = "";
                $deletePermissions = "";
            }


            return $this->getOperations($editPermissions, $deletePermissions, $item, "<a  href='users/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
        });

        return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query() {
        $model = $this->repository->getModel();
        $select = [
            'impiger_users.*', 'countries.phone_code', 'user_address.present_phone', 'I.name as institute', 'I.id as institute_id',
            DB::raw('GROUP_CONCAT(EI.specialization) AS specialization'), 'DIS.name as district', 'C.name as county'
        ];

        $query = $model->select($select)
                        ->leftJoin('user_address', 'user_address.imp_user_id', '=', 'impiger_users.id')
                        ->leftJoin('countries', 'countries.id', '=', 'user_address.present_phonecode')
                        ->leftJoin('roles', 'roles.id', '=', 'impiger_users.registered_role')
                        ->leftJoin('deployed_users AS DU', 'DU.imp_user_id', '=', 'impiger_users.id')
                        ->leftJoin('institutions AS I', 'I.id', '=', 'DU.reference_id')
                        ->leftJoin('education_info AS EI', 'EI.imp_user_id', '=', 'impiger_users.id')
                        ->leftJoin('district AS DIS', 'DIS.id', '=', 'user_address.present_district')
                        ->leftJoin('county AS C', 'C.id', '=', 'user_address.present_county')
                        ->leftJoin('attribute_options AS AO', 'AO.id', '=', 'EI.specialization')
                        ->whereNotNull('impiger_users.id')
                        ->where('impiger_users.registered_user', 1)
                        ->groupBy('impiger_users.id');
//                ->where('impiger_users.wf_status',STAFF_INITIAL_STATE)
        ;
        if($this->isFrontend){
            $query = $query->where('impiger_users.wf_status',STAFF_APPROVE_STATE);
        }

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns() {
        return [
            'photo' => [
                'name' => 'photo',
                'title' => 'Photo',
                'width' => '100',
                'class' => 'text-left',
                'exportable' => 0,
                'visible' => false
            ],
            'registration_number' => [
                'name' => 'registration_number',
                'title' => 'Registration Number',
                'width' => '70',
                'class' => 'text-left',
                'visible' => ($this->isFrontend) ? true : false
            ],
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
            'username' => [
                'name' => 'username',
                'title' => 'Username',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'email' => [
                'name' => 'email',
                'title' => 'Email',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'phone_code' => [
                'name' => 'phone_code',
                'title' => 'Code',
                'width' => '20',
                'class' => 'text-left',
                'visible' => false
            ],
            'present_phone' => [
                'name' => 'present_phone',
                'title' => 'Contact Number',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'specialization' => [
                'name' => 'specialization',
                'title' => 'Area Of Specialization',
                'width' => '100',
                'class' => 'text-left'
            ],
            'district' => [
                'name' => 'district',
                'title' => 'District',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'county' => [
                'name' => 'county',
                'title' => 'County',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'nationality' => [
                'name' => 'nationality',
                'title' => 'Nationality',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'identity_number' => [
                'name' => 'identity_number',
                'title' => 'NIN',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'dob' => [
                'name' => 'dob',
                'title' => 'Dob',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'institute' => [
                'name' => 'I.name',
                'title' => 'Institution',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'if_refugee' => [
                'name' => 'if_refugee',
                'title' => 'If Refugee',
                'width' => '20',
                'class' => 'text-left',
                'visible' => false
            ],
            'blood_group' => [
                'name' => 'blood_group',
                'title' => 'Blood Group',
                'width' => '30',
                'class' => 'text-left',
                'visible' => false
            ],
            'gender' => [
                'name' => 'gender',
                'title' => 'Gender',
                'width' => '30',
                'class' => 'text-left',
                'visible' => false
            ],
            'card_number' => [
                'name' => 'card_number',
                'title' => 'Card Number',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'passport_number' => [
                'name' => 'passport_number',
                'title' => 'Passport Number',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'marital_status' => [
                'name' => 'marital_status',
                'title' => 'Marital Status',
                'width' => '50',
                'class' => 'text-left',
                'visible' => false
            ],
            'no_of_child' => [
                'name' => 'no_of_child',
                'title' => 'No Of Child',
                'width' => '20',
                'class' => 'text-left',
                'visible' => false
            ],
            'religion' => [
                'name' => 'religion',
                'title' => 'Religion',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
            'accredited_status' => [
                'name' => 'accredited_status',
                'title' => 'Status',
                'width' => '70',
                'class' => 'text-left',
                'visible' => ($this->isFrontend) ? true : false
            ],
            'wf_status' => [
                'name' => 'wf_status',
                'title' => ' Wf Status',
                'width' => '70',
                'class' => 'text-left',
                'class' => 'text-left workflow-dropdown',
                'visible' => ($this->isFrontend) ? false : true

            ],
            'created_at' => [
                'name' => 'created_at',
                'title' => 'Created At',
                'width' => '100',
                'class' => 'text-left',
                'visible' => false
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons() {
        if (!$this->hasActions) {
            return [];
        }

        $buttons = [];


        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, User::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array {
        if (Auth::user() && !Auth::user()->hasPermission('user.edit')) {
            return [];
        }
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array {
        return [];
    }

    /**
     * @return array
     */
    public function getFilters(): array {
        $workflow = [];
        if (is_plugin_active('workflows') && \Workflow::get($this->repository->getModel())) {
                $workflowStates = \CustomWorkflow::getWorkflowAllStates('user');
                foreach($workflowStates as $key => $value){
                    $workflow[$value] = ucfirst($value);
                }
        }
        $filters = CrudHelper::getBulkChanges('user', true);
        $institute = [
            'title' => 'Institute',
            'type' => 'select',
            'choices' => CrudHelper::getSelectBoxChoices([
                "field" => 'institute_id',
                "option" => ["opt_type" => "external",
                    "lookup_table" => "institutions",
                    "lookup_key" => "id",
                    "lookup_value" => "name",
                    "where_cndn" => "is_enabled=1 AND deleted_at IS NULL"]
            ])
        ];
        $status = [
            'title' => 'Status',
            'type' => 'select',
            'choices' => $workflow
        ];
        $filters = CrudHelper::arrayInsertAfterKey('impiger_users.username',$filters,'I.id',$institute);
        $filters = CrudHelper::arrayInsertAfterKey('I.id',$filters,'impiger_users.wf_status',$status);
        Arr::forget($filters, ['impiger_users.payroll','impiger_users.entity_id','impiger_users.entity_type', 'impiger_users.is_login_needed', 'impiger_users.is_enabled', 'impiger_users.designation', 'impiger_users.staff_category']);
        return $filters;
    }

    /**
     * {@inheritDoc}
     */
    public function applyFilterCondition($query, string $key, string $operator, ?string $value) {

        switch ($key) {
            case 'I.id':
                if (!$value) {
                    break;
                }
                return $query->whereRaw($key . '=' . $value);
        }
        return parent::applyFilterCondition($query, $key, $operator, $value);
    }

    /**
     * @return array
     */
    public function getDefaultButtons(): array {
        $defaultBtns = parent::getDefaultButtons();

        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user() && Auth::user()->hasPermission('user.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('user.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item) {
        if (isset($item->is_default) && $item->is_default) {
            return true;
        }
        return false;
    }

    public function setTableConfig($config): self {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }

}
