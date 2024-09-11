<?php

namespace Impiger\Crud\Http\Controllers;

use Assets;
use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Crud\Http\Requests\CrudRequest;
use Impiger\Crud\Repositories\Interfaces\CrudInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Crud\Tables\CrudTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Crud\Forms\CrudForm;
use Impiger\Base\Forms\FormBuilder;
use App\Models\Crud;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\StreamOutput;
use Arr;
use DB;
use Validator;
use Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Theme;

class CrudController extends BaseController
{
    /**
     * @var CrudInterface
     */
    protected $crudRepository;

    protected $data = array();

    /**
     * @param CrudInterface $crudRepository
     */
    public function __construct(CrudInterface $crudRepository)
    {

        $this->crudRepository = $crudRepository;

        $driver             = config('database.default');
        $database           = config('database.connections');

        $this->db           = $database[$driver]['database'];
        $this->dbuser       = $database[$driver]['username'];
        $this->dbpass       = $database[$driver]['password'];
        $this->dbhost       = $database[$driver]['host'];

        //$this->model = new Module();


        $this->data = array_merge(array(
            'pageTitle' =>  'Module',
            'pageNote'  =>  'Manage All Module',

        ), $this->data);

        // Load Sximo Config
        $sximo     = config('sximo');
        $this->config = $sximo;
        $this->data['sximoconfig'] = $sximo;

        Assets::addStylesDirectly([
            'vendor/core/plugins/crud/css/bootstrap-material-datetimepicker.css',
            'vendor/core/plugins/crud/css/perfect-scrollbar.min.css',
            'vendor/core/plugins/crud/css/c3.min.css',
            'vendor/core/plugins/crud/css/colors.css',
            'vendor/core/plugins/crud/css/jquery.toast.css',
        ])
            ->addScriptsDirectly([
                'vendor/core/plugins/crud/js/moment.js',
                //                'vendor/core/plugins/crud/js/sximo.min.js',
                //                'vendor/core/plugins/crud/js/jquery.js',
                'vendor/core/plugins/crud/js/jquery_cookie.js',
                'vendor/core/plugins/crud/js/jquery_ui.js',
                'vendor/core/plugins/crud/js/jquery_select.js',
                'vendor/core/plugins/crud/js/jquery_form.js',
                'vendor/core/plugins/crud/js/parsley.js',
                'vendor/core/plugins/crud/js/datepicker.js',
                'vendor/core/plugins/crud/js/sweetalert.js',
                'vendor/core/plugins/crud/js/jquery_clone.js',
                'vendor/core/plugins/crud/js/jquery_combo.js',
                'vendor/core/plugins/crud/js/jquery_scroll.js',
                'vendor/core/plugins/crud/js/datatables.js',
                'vendor/core/plugins/crud/js/popper.min.js',
                'vendor/core/plugins/crud/js/bootstrap.min.js',
                'vendor/core/plugins/crud/js/perfect-scrollbar.jquery.min.js',
                'vendor/core/plugins/crud/js/sidebarmenu.js',
                'vendor/core/plugins/crud/js/sticky-kit.min.js',
                'vendor/core/plugins/crud/js/jquery.sparkline.min.js',
                'vendor/core/plugins/crud/js/custom.min.js',
                'vendor/core/plugins/crud/js/sximo5.js',
                'vendor/core/plugins/crud/js/jQuery.style.switcher.js',
                'vendor/core/plugins/crud/js/laravel_sql_parser.js',
            ]);
    }

    /**
     * @param CrudTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(CrudTable $table, CrudRequest $request)
    {
        if (!is_null($request->input('t'))) {
            $rowData = DB::table('cruds')->where('module_type', '=', 'core')
                ->orderby('module_title', 'asc')->get();
            $type = 'core';
        } else {
            $rowData = DB::table('cruds')->where('module_type', '!=', 'core')
                ->orderby('module_title', 'asc')->get();
            $type = 'addon';
        }

        $this->data['type']    = $type;
        $this->data['rowData'] = $rowData;
        return view('plugins/crud::module.index', $this->data);
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        $this->data = array(
            'pageTitle'    => 'Create New Module',
            'pageNote'    => 'Create Quick Module ',
        );

        $parentModules = DB::table('cruds')->where('parent_id', 0)->get()->pluck('module_name', 'id');
        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['cruds'] = crudOption();
        $this->data['parentModules'] = $parentModules;
        return view('plugins/crud::module.create', $this->data);
    }

    /**
     * @param CrudRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(CrudRequest $request, BaseHttpResponse $response)
    {
        // Module Exist Check
        $moduleName = strtolower(trim($request->input('module_name')));
        $row = Crud::isModuleExist(['module_name' => $moduleName]);
        if ($row) {
            return $response
                ->setError(true)
                ->setMessage('Module already exists');
        }

        $table = $request->input('module_db');
        $row = Crud::isModuleExist(['module_db' => $table]);
        if ($row) {
            return $response
                ->setError(true)
                ->setMessage('Table already exists in another module');
        }
        $primary = self::findPrimarykey($request->input('module_db'));

        $select = $request->input('sql_select');
        $where     = $request->input('sql_where');
        $group     = $request->input('sql_group');

        if ($request->input('creation') == 'manual') {

            try {
                $rules = array(
                    'sql_select'            => 'required',
                    'sql_where'          => 'required'
                );
                $validator = Validator::make($request->all(), $rules);

                if (!$validator->passes()) {
                    return $response
                        ->setError(true)
                        ->setMessage('Required Param Missing.');
                }

                DB::select($select . ' ' . $where . ' ' . $group);
            } catch (Exception $e) {
                // Do something when query fails.
                $error = 'Error : ' . $select . ' ' . $where . ' ' . $group;
                return $response
                    ->setError(true)
                    ->setMessage($error);
            }
            $columns = array();
            $results =  Crud::getColoumnInfo($select . ' ' . $where . ' ' . $group);
            $primary_exits = '';
            foreach ($results as $r) {
                $Key = (isset($r['flags'][1]) && $r['flags'][1] == 'primary_key'  ? 'PRI' : '');
                if ($Key != '') $primary_exits = $r['name'];
                $columns[] = (object) array('Field' => $r['name'], 'Table' => $r['table'], 'Type' => $r['native_type'], 'Key' => $Key);
            }
            $primary  = ($primary_exits != '' ? $primary_exits : '');
        } else {
            $columns = DB::select("SHOW COLUMNS FROM " . $request->input('module_db'));
            $select =  " SELECT {$table}.* FROM {$table} ";
            $where = " WHERE " . $table . "." . $primary . " IS NOT NULL";
            if ($primary != '') {
                $where     = " WHERE " . $table . "." . $primary . " IS NOT NULL";
            } else {
                $where  = '';
            }
        }

        $i = 0;
        $rowGrid = array();
        $rowForm = array();
        foreach ($columns as $column) {
            if (!isset($column->Table)) $column->Table = $table;
            if ($column->Field == $primary) {
                $column->Type = 'hidden';
            }

            if ($column->Table == $table) {
                $form_creator = self::configForm($column->Field, $column->Table, $column->Type, $i, [], $column);
                $relation = self::buildRelation($table, $column->Field);
                foreach ($relation as $row) {
                    $array = array('external', $row['table'], $row['column']);
                    $form_creator = self::configForm($column->Field, $table, 'select', $i, $array, $column);
                }
                $rowForm[] = $form_creator;
            }

            $rowGrid[] = self::configGrid($column->Field, $column->Table, $column->Type, $i);
            $i++;
        }

        $json_data['sql_select']     = $select;
        $json_data['sql_where']     = $where;
        $json_data['sql_group']        = $group;
        $json_data['table_db']        = $table;
        $json_data['primary_key']    = $primary;
        $json_data['grid']    = $rowGrid;
        $json_data['forms']    = $rowForm;

        $module_type = $primary == '' ? 'report' : $request->input('module_template');
        $defModuleAction  = json_encode(['create' => 1, 'edit' => 1, 'destroy' => 1, 'export' => 1, 'print' => 1, 'inline_edit' => 0, 'enable_disable' => 0, 'hide_operations' => 0]);
        $data = array(
            'module_name'    => $moduleName,
            'module_alias'    => $request->input('module_alias'),
            'module_title'    => $request->input('module_title'),
            'module_note'    => $request->input('module_note'),
            'module_db'        => $request->input('module_db'),
            'module_db_key' => $primary,
            'module_type'     => $module_type,
            'module_created'     => date("Y-m-d H:i:s"),
            'module_config' => CF_encode_json($json_data),
            'module_queries' => $request->input('module_queries'),
            'parent_id' => (($request->input('parent_id') && $request->input('is_submodule')) ? $request->input('parent_id') : 0),
            'is_entity' => (($request->input('is_entity')) ? $request->input('is_entity') : 0),
            'is_bulkupload' => (($request->input('is_bulkupload')) ? $request->input('is_bulkupload') : 0),
            'is_multi_lingual' => (($request->input('is_multi_lingual')) ? $request->input('is_multi_lingual') : 0),
            'is_customized' => (($request->input('is_customized')) ? $request->input('is_customized') : 0),
            'module_actions' => $defModuleAction,
            'module_action_meta' => (($request->input('module_action_meta')) ? json_encode($request->input('module_action_meta')) : ""),
            'dependent_module' => (($request->input('dependent_module')) ? json_encode($request->input('dependent_module')) : 0),
            'module_before_insert' => (($request->input('module_before_insert')) ? $request->input('module_before_insert') : 0),
            'insert_user_before' => (($request->input('insert_user_before')) ? $request->input('insert_user_before') : 0),
            'is_shortcode_form' => (($request->input('is_shortcode_form')) ? $request->input('is_shortcode_form') : 0),
            'is_shortcode_table' => (($request->input('is_shortcode_table')) ? $request->input('is_shortcode_table') : 0),
            'shortcode_options' => (($request->input('shortcode_options')) ? json_encode($request->input('shortcode_options')) : ""),
        );

        DB::beginTransaction();
        DB::table('cruds')->insert($data);
        $row = DB::table('cruds')->where('module_name', $request->input('module_name'))->get()->first();

        if (!$row) {
            return $response
                ->setError(true)
                ->setMessage('Can not find module');
        }

        try {
            $output = $this->callPluginCreateCommand($row, $request->input('module_name'));
            if ($output['error']) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            return $response
                ->setError(Arr::get($output, 'error'))
                ->setMessage(Arr::get($output, 'msg'));
        } catch (Exception $ex) {
        }
    }

    public function rebuild(Request $request, $id = 0, BaseHttpResponse $response)
    {
        $row = DB::table('cruds')->where('id', $id)->get()->first();
        if (!$row) {
            return $response
                ->setError(true)
                ->setMessage('Can not find module');
        }
        if ($row->is_customized) {
            return $response
                ->setError(true)
                ->setMessage('Can not rebuild this module,because it is customized module');
        }

        $this->data['row'] = $row;
        $class = $row->module_name;
        $output = $this->callPluginCreateCommand($row, $class, true);
        return $response
            ->setError(Arr::get($output, 'error'))
            ->setMessage(Arr::get($output, 'msg'));
    }

    public function callPluginCreateCommand($row, $moduleName, $isEdit = false)
    {
        $output = [];
        $cmdParams = ['name' => $moduleName];
        $cmdSign = 'cms:plugin:module:create';
        $createText = ' creating';
        $actionText = ' created';

        if ($row->parent_id) {
            $parentRow = DB::table('cruds')->select('module_name')->where('id', $row->parent_id)->get()->first();
            if (!$parentRow) {
                $output['error'] = true;
                $output['msg'] = 'Can not find parent module';
                return $output;
            }
            $cmdSign = 'cms:plugin:module:make:crud';
            $cmdParams['plugin'] = $parentRow->module_name;
        }

        if ($isEdit) {
            $cmdParams['is_edit'] = 1;
            $createText = ' re-creating';
            $actionText = ' update';
        }

        $stream = fopen("php://output", "w");
        $commandOutput = Artisan::call($cmdSign, $cmdParams, new StreamOutput($stream));
        $callResponse = ob_get_clean();
        $type = ($commandOutput) ? 'danger' : "info";
        $msg = ($commandOutput) ? 'Problem in' . $createText . ' module.' : "Module " . $actionText . " Successfully";
        $auditMsg = ($commandOutput) ? $callResponse : $msg;

        if (defined('USER_ACTION_CRUD_MANAGEMENT')) {
            do_action(USER_ACTION_CRUD_MANAGEMENT, CRUD_MODULE_SCREEN_NAME, $auditMsg, $row->id, $row->module_name, $type);
        }

        $output['error'] = $commandOutput;
        $output['msg'] = $msg;
        return $output;
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $crud = $this->crudRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $crud));

        page_title()->setTitle(trans('plugins/crud::crud.edit') . ' "' . $crud->name . '"');

        return $formBuilder->create(CrudForm::class, ['model' => $crud])->renderForm();
    }

    /**
     * @param int $id
     * @param CrudRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, CrudRequest $request, BaseHttpResponse $response)
    {
        $crud = $this->crudRepository->findOrFail($id);

        $crud->fill($request->input());

        $this->crudRepository->createOrUpdate($crud);

        event(new UpdatedContentEvent(CRUD_MODULE_SCREEN_NAME, $request, $crud));

        return $response
            ->setPreviousUrl(route('crud.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $crud = $this->crudRepository->findOrFail($id);

            $this->crudRepository->delete($crud);

            event(new DeletedContentEvent(CRUD_MODULE_SCREEN_NAME, $request, $crud));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $crud = $this->crudRepository->findOrFail($id);
            $this->crudRepository->delete($crud);
            event(new DeletedContentEvent(CRUD_MODULE_SCREEN_NAME, $request, $crud));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }


    public function findPrimarykey($table)
    {
        //  show columns from members where extra like '%auto_increment%'"
        $query = "SHOW columns FROM `{$table}` WHERE extra LIKE '%auto_increment%'";
        $primaryKey = '';
        foreach (DB::select($query) as $key) {
            $primaryKey = $key->Field;
            // print_r($key);
        }
        return $primaryKey;
    }

    public function configGrid($field, $alias, $type, $sort)
    {
        $grid = array(
            "field"     => $field,
            "alias"     => $alias,
            "label"     => ucwords(str_replace('_', ' ', $field)),
            "language"    => array(),
            "search"     => '1',
            "download"     => '1',
            "align"     => 'left',
            "view"         => '1',
            "edit"         => '0',
            "visibility"      => '1',
            "sortable"     => '1',
            "frozen"     => '0',
            'hidden'    => '0',
            "sortlist"     => $sort,
            "width"     => '100',
            "conn"          => array('valid' => '0', 'db' => '', 'key' => '', 'display' => ''),
            "format_as"     => '',
            "format_value"  => '',

        );
        return $grid;
    }

    public function configForm($field, $alias, $type, $sort, $opt = array(), $columnConfig = NULL)
    {

        $opt_type = '';
        $lookup_table = '';
        $lookup_key = '';
        if (count($opt) >= 1) {
            $opt_type = $opt[0];
            $lookup_table = $opt[1];
            $lookup_key = $opt[2];
        }

        $dbType = (isset($columnConfig->dbType)) ? $columnConfig->dbType : $type;
        $forms = array(
            "field"     => $field,
            "alias"     => $alias,
            "label"     => ucwords(str_replace('_', ' ', $field)),
            "language"    => array(),
            'required'        => '',
            'bulk_edit'        => '',
            'view'            => '1',
            'type'            => self::configFieldType($type),
            'dbType'          => $dbType,
            'isNullable'      => (isset($columnConfig->Null)) ? $columnConfig->Null : '',
            'defaultVal'      => (isset($columnConfig->Default)) ? $columnConfig->Default : '',
            'casting'      => (isset($columnConfig->casting)) ? $columnConfig->casting : '',
            'add'            => '1',
            'edit'            => '1',
            'search'        => '1',

            'size'            => 'span12',
            "sortlist"     => $sort,
            'form_group'    => '',
            'option'        => array(
                "opt_type"                 => $opt_type,
                "specific_entity_type" => "",
                "custom_function" => "",
                "dependant_table" => "",
                "lookup_query"             => '',
                "lookup_table"             =>     $lookup_table,
                "lookup_key"             =>  $lookup_key,
                "lookup_value"            => $lookup_key,
                "where_cndn"    =>    '',
                'is_dependency'            => '',
                'select_multiple'            => '0',
                'image_multiple'            => '0',
                'lookup_dependency_key'    => '',
                'path_to_upload'        => '',
                'upload_type'        => '',
                'tooltip'        => '',
                'attribute'        => '',
                'extend_class'        => ''
            )
        );
        return $forms;
    }

    public function configFieldType($type)
    {
        $type = strtolower($type);
        switch ($type) {
            default:
                $type = 'text';
                break;
            case 'timestamp';
            case 'datetime';
                $type = 'text_datetime';
                break;
            case 'date';
                $type = 'text_date';
                break;
            case 'string';
            case 'var_string';
                $type = 'text';
                break;
            case 'int';
            case 'long';
                $type = 'text';
                break;
            case 'text';
            case 'blob';
                $type = 'textarea';
                break;
            case 'select';
                $type = 'select';
                break;
            case 'hidden';
                $type = 'hidden';
                break;
        }
        return $type;
    }

    public function buildRelation($table, $field)
    {

        $pdo = DB::getPdo();
        $sql = "
        SELECT
            referenced_table_name AS 'table',
            referenced_column_name AS 'column'
        FROM
            information_schema.key_column_usage
        WHERE
            referenced_table_name IS NOT NULL
            AND table_schema = '" . $this->db . "'  AND table_name = '{$table}' AND column_name = '{$field}' ";
        $Q = $pdo->query($sql);
        $rows = array();
        while ($row =  $Q->fetch()) {
            $rows[] = $row;
        }
        return $rows;
    }

    function getConn(Request $request, $id)
    {
        $row = DB::table('cruds')->where('id', $id)->get()->first();

        if (!$row) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }

        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);

        $module_id = $id;
        $field_id = $request->input('field');
        $alias = $request->input('alias');
        $f = array();
        foreach ($config['grid'] as $form) {
            if ($form['field'] == $field_id) {
                $f = array(
                    'db' => (isset($form['conn']['db']) ? $form['conn']['db'] : ''),
                    'key' => (isset($form['conn']['key']) ? $form['conn']['key'] : ''),
                    'display' => (isset($form['conn']['display']) ? $form['conn']['display'] : ''),
                );
            }
        }

        $this->data['id'] = $id;
        $this->data['f'] = $f;
        $this->data['module'] = 'module';
        $this->data['module_name'] = $row->module_name;
        $this->data['field_id'] = $field_id;
        $this->data['alias'] = $alias;
        return view('plugins/crud::module.connection', $this->data);
    }

    public function postConn(Request $request)
    {
        $id = $request->input('id');
        $field_id = $request->input('field_id');
        $alias = $request->input('alias');
        $row = DB::table('cruds')->where('id', $id)->get()->first();
        if (!$row) {
            return redirect($this->module)->with('message', 'Can not find module')->with('status', 'error');
        }

        $this->data['row'] = $row;
        $fr = array();
        $config = CF_decode_json($row->module_config);
        foreach ($config['grid'] as $form) {
            if ($form['field'] == $field_id && $form['alias'] == $alias) {
                if ($request->input('db') != '') {
                    $value = implode("|", $request->input('display'));
                    $form['conn'] = array(
                        'valid' => '1',
                        'db' => $request->input('db'),
                        'key' => $request->input('key'),
                        'display' => implode("|", array_filter($request->input('display'))),
                    );
                } else {
                    $form['conn'] = array(
                        'valid' => '0',
                        'db' => '',
                        'key' => '',
                        'display' => '',
                    );
                }
                $fr[] = $form;
            } else {
                $fr[] = $form;
            }
        }
        unset($config["grid"]);
        $new_config = array_merge($config, array("grid" => $fr));


        $affected = DB::table('cruds')->where('id', '=', $id)->update(array('module_config' => CF_encode_json($new_config)));
        return redirect('admin/cruds/table/' . $row->module_name)
            ->with('message', 'Module Forms Has Changed Successful.')->with('status', 'success');
    }

    public function createRouters()
    {
        $rows = DB::table('cruds')->where('module_type', '!=', 'core')->get();
        $val  =    "<?php
        ";
        $val_api  = "<?php
        ";
        foreach ($rows as $row) {
            $class = $row->module_name;
            $controller = ucwords($row->module_name) . 'Controller';

            $mType = ($row->module_type == 'addon' ? 'native' :  $row->module_type);
            include(base_path() . '/platform/plugins/crud/resources/views/module/template/' . $mType . '/config/route.php');
            include(base_path() . '/platform/plugins/crud/resources/views/module/template/' . $mType . '/config/route_api.php');
        }
        $val .=     "?>";
        $val_api .=     "?>";
        $filename = base_path() . '/routes/module.php';
        $fp = fopen($filename, "w+");
        fwrite($fp, $val);
        fclose($fp);

        file_put_contents(base_path() . "/routes/services.php", $val_api);

        return true;
    }

    public function getConfig($id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;

        // Get config Info
        $fp = base_path() . '/platform/plugins/crud/resources/views/module/template/' . $this->getTemplateName($row->module_type) . '/config/info.json';
        $fp = json_decode(file_get_contents($fp));
        $this->data['config'] = $fp;
        $this->data['cruds'] = crudOption();

        $this->data['module'] = 'module';
        $this->data['module_lang'] = json_decode($row->module_lang, true);
        $this->data['module_actions'] = json_decode($row->module_actions, true);
        $this->data['module_action_meta'] = $row->module_action_meta;
        $this->data['dependent_module'] = $row->dependent_module;
        $this->data['shortcode_options'] = $row->shortcode_options;
        $this->data['module_name'] = $row->module_name;
        $config = CF_decode_json($row->module_config, true);
        $this->data['tables']     = $config['grid'];
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        $this->data['setting'] = array(
            'gridtype'        => (isset($config['setting']) ? $config['setting']['gridtype'] : 'native'),
            'orderby'        => (isset($config['setting']) ? $config['setting']['orderby'] : $row->module_db_key),
            'ordertype'        => (isset($config['setting']) ? $config['setting']['ordertype'] : 'asc'),
            'perpage'        => (isset($config['setting']) ? $config['setting']['perpage'] : '10'),
            'frozen'        => (isset($config['setting']['frozen'])  ? $config['setting']['frozen'] : 'false'),
            'form-method'        => (isset($config['setting']['form-method'])  ? $config['setting']['form-method'] : 'native'),
            'view-method'        => (isset($config['setting']['view-method'])  ? $config['setting']['view-method'] : 'native'),
            'inline'        => (isset($config['setting']['inline'])  ? $config['setting']['inline'] : 'false'),
            'view_details_type' => (isset($config['setting']['view_details_type'])  ? $config['setting']['view_details_type'] : 0),
            'hide_menu' => (isset($config['setting']['hide_menu'])  ? $config['setting']['hide_menu'] : false),
            'special_notes' => (isset($config['setting']['special_notes'])  ? $config['setting']['special_notes'] : ""),
            'is_captcha' => (isset($config['setting']['is_captcha'])  ? $config['setting']['is_captcha'] : 0),
            'action_box' => (isset($config['setting']['action_box'])  ? $config['setting']['action_box'] : 'right'),
            'menu_priority' => (isset($config['setting']['menu_priority'])  ? $config['setting']['menu_priority'] : NULL),
            'menu_icon' => (isset($config['setting']['menu_icon'])  ? $config['setting']['menu_icon'] : NULL),
            'custom_js' => (isset($config['setting']['custom_js'])  ? $config['setting']['custom_js'] : 0),
            'is_gallery_image' => (isset($config['setting']['is_gallery_image'])  ? $config['setting']['is_gallery_image'] : 0),
            'is_custom_action' => (isset($config['setting']['is_custom_action'])  ? $config['setting']['is_custom_action'] : 0),
            'is_subscription_related_module' => (isset($config['setting']['is_subscription_related_module'])  ? $config['setting']['is_subscription_related_module'] : 0),
            'hide_module_actions' => (isset($config['setting']['hide_module_actions'])  ? $config['setting']['hide_module_actions'] : ""),
            'revision_history' => (isset($config['setting']['revision_history'])  ? $config['setting']['revision_history'] : ""),
            'domain_mapping' => (isset($config['setting']['domain_mapping'])  ? $config['setting']['domain_mapping'] : 0),
        );
        /* @customized by Sabari Shankar.parthiban start */
        $this->data['menu_priorities'] = dashboard_menu()->getMenuPriority();
        if ($row->parent_id) {
            $parentModule = DB::table('cruds')->where('id', $row->parent_id)
                ->first();
            $menuId = "cms-plugins-" . $parentModule->module_name;
            $childrens = dashboard_menu()->getChildMenuCount($menuId);
            $this->data['child_menus'] = [];
            if (!empty($childrens)) {
                $this->data['child_menus'] = $childrens[0];
            }
        }
        /* @customized by Sabari Shankar.parthiban end */
        $this->data['tableList'] = Crud::getTableList($this->db);
        return view('plugins/crud::module.config', $this->data);
    }

    function getTemplateName($file)
    {
        if ($file == 'addon' or $file == 'core') {
            return 'native';
        } else {
            return $file;
        }
    }

    function getBuild($id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];

        $this->data['module'] = 'module';
        $this->data['module_name'] = $id;
        $this->data['id'] = $row->id;
        return view('plugins/crud::module.build', $this->data);
    }

    function getFormdesign(Request $request, $id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect($this->module)
                ->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);
        $this->data['forms']     = $config['forms'];
        $this->data['module'] = 'module';
        $this->data['form_column'] = (isset($config['form_column']) ? $config['form_column'] : 1);
        if (!is_null($request->input('block')))     $this->data['form_column'] = $request->input('block');

        if (!isset($config['form_layout'])) {
            $this->data['title'] = array($row->module_name);
            $this->data['format'] = 'grid';
            $this->data['display'] = 'vertical';
        } else {
            $this->data['title']     =    explode(",", $config['form_layout']['title']);
            $this->data['format']     =    $config['form_layout']['format'];
            $this->data['display']     =    (isset($config['form_layout']['display']) ? $config['form_layout']['display'] : 'vertical');
        }
        $this->data['module_name'] = $row->module_name;
        $this->data['type']           = $row->module_type;
        return view('plugins/crud::module.formdesign', $this->data);
    }

    //Form Layout
    public function postFormdesign(Request $request, BaseHttpResponse $response)
    {

        $id = $request->input('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];

        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);
        $data = $_POST['reordering'];
        $data = explode('|', $data);
        $currForm = $config['forms'];

        foreach ($currForm as $f) {
            $cform[$f['field']] = $f;
        }

        $i = 0;
        $order = 0;
        $f = array();
        foreach ($data as $dat) {

            $forms = explode(",", $dat);
            foreach ($forms as $form) {
                if (isset($cform[$form])) {
                    $cform[$form]['form_group'] = $i;
                    $cform[$form]['sortlist'] = $order;
                    $f[] = $cform[$form];
                }
                $order++;
            }
            $i++;
        }

        $config['form_column'] = count($data);
        $config['form_layout'] = array(
            'column'    => count($data),
            'title' => implode(',', $request->input('title')),
            'format' => $request->input('format'),
            'display' => $request->input('display')

        );
        // print_r($config['form_layout']); exit;


        unset($config["forms"]);
        $new_config =     array_merge($config, array("forms" => $f));
        $data['module_config'] = CF_encode_json($new_config);


        DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));
        return $response->setMessage('Forms Design Has Changed Successful');
    }


    function getStats(Request $request, $id = '')
    {
        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $this->data['row'] = $row;
        $this->data['fields'] = $config['grid'];
        $this->data['stats'] = (isset($config['stats']) ? $config['stats'] : array());
        $qryParams = $request->input('mod');
        $qryParams2 = $request->input('parent_mod');
        $this->data['subform_detail'] = [];
        $this->data['roles'] = \Impiger\ACL\Models\Role::get();
        $workflow = \Impiger\Workflows\Models\Workflows::where('module_controller',$row->module_db)->first();
        $workflowMeta = ($workflow) ? $workflow->workflow_meta_data : [];
         $this->data['workflow_meta_data'] = $workflowMeta;
        if ($qryParams) {
            $subformData = array_filter($this->data['stats'], function ($value) use ($qryParams,$qryParams2) {
                if ($value['slug'] == $qryParams && $value['parent_stats_id'] == $qryParams2) {
                    return true;
                }
            });
            $subformData = array_values($subformData);
            $this->data['subform_detail'] = Arr::has($subformData, '0') ? $subformData[0] : [];
        }

        $this->data['stats_operations'] = STATS_OPERATION;
        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['module'] = $row->module_name;
        $this->data['module_name'] = $id;
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        $this->data['modules'] = Crud::all();
        return view('plugins/crud::module.stats', $this->data);
    }

    function postSaveStats(Request $request, BaseHttpResponse $response)
    {
        if (true) {

            $id = $request->get('id');
            $row = DB::table('cruds')->where('id', $id)
                ->get();
            if (count($row) <= 0) {
                return redirect('admin/cruds')
                    ->with('message', 'Can not find module')->with('status', 'error');
            }
            $row = $row[0];
            $this->data['row'] = $row;
            $config = CF_decode_json($row->module_config);

            $requestData = array(
                'title'            => $request->get('title'),
                'slug' => $request->get('slug'),
                'field'        => $request->get('field'),
                'stats_operation'    => $request->get('stats_operation'),
                'sql_cndn'        => trim($request->get('sql_cndn')),
                'is_sub_stats'            => $request->get('is_sub_stats'),
                'parent_stats_id'            => $request->get('parent_stats_id'),
                'field_access_type'     => $request->get('field_access_type'),
                'restricted_role_id'     => $request->get('restricted_role_id'),
                'route' => $request->get('route'),
                'icon' => $request->get('icon'),
                'color' => $request->get('color'),
                'order' => $request->get('order'),
                'workflow_meta' => $request->get('workflow_meta'),
                'stats_type' => $request->get('stats_type'),
                'operation_type' => $request->get('operation_type'),
                'sql_join' => $request->get('sql_join'),
                'sql_group_by' => $request->get('sql_group_by'),
                'show_backend' => $request->get('show_backend'),
                'show_frontend' => $request->get('show_frontend')
            );

            $stats = array();
            $moduleUpdate = false;
            if (isset($config["stats"])) {
                foreach ($config['stats'] as $sb) {
                    if ($requestData['slug'] == $sb['slug'] && $requestData['parent_stats_id'] == $sb['parent_stats_id']) {
                        $stats[] = $requestData;
                        $moduleUpdate = true;
                    } else {
                        $stats[] = $sb;
                    }
                }
            }

            if (!$moduleUpdate) {
                $requestData['slug'] = Str::slug($requestData['title']);
                $newData[] = $requestData;
                $stats = array_merge($stats, $newData);
            }
            if (isset($config["stats"])) unset($config["stats"]);
            $new_config =     array_merge($config, array("stats" => $stats));




            $affected = DB::table('cruds')
                ->where('id', '=', $id)
                ->update(array('module_config' => CF_encode_json($new_config)));


            if ($request->ajax() == true) {
                return response()->json(array('status' => 'success', 'message' => 'Stats Has beed added Successful.'));
            } else {
                return $response->setMessage('Stats Has beed added Successful.');
            }
        } else {
            return $response->setError()->setMessage('Failed to save stats.');
        }
    }

    function getSub(Request $request, $id = '')
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $this->data['row'] = $row;
        $this->data['fields'] = $config['grid'];
        $this->data['subs'] = (isset($config['subgrid']) ? $config['subgrid'] : array());
        $qryParams = $request->input('mod');
        $this->data['subform_detail'] = [];
        $this->data['roles'] = \Impiger\ACL\Models\Role::get();

        if ($qryParams) {
            $subformData = array_filter($this->data['subs'], function ($value) use ($qryParams) {
                if ($value['module'] == $qryParams) {
                    return true;
                }
            });
            $subformData = array_values($subformData);
            $this->data['subform_detail'] = Arr::has($subformData, '0') ? $subformData[0] : [];
        }

        if (Arr::has($this->data['subs'], 0)) {
            foreach ($this->data['subs'] as $sub) {
                $moreFields = [];
                $moreFields['field'] = \Str::plural(\Str::snake(str_replace('-', '_', $sub['module'])));
                $moreFields['alias'] = $sub['table'];
                $moreFields['label'] = $sub['title'];
                $this->data['fields'][] = $moreFields;
            }
        }

        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['module'] = $row->module_name;
        $this->data['module_name'] = $id;
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        $this->data['modules'] = Crud::all();
        return view('plugins/crud::module.sub', $this->data);
    }

    function postSavesub(Request $request, BaseHttpResponse $response)
    {
        if (true) {

            $id = $request->get('id');
            $row = DB::table('cruds')->where('id', $id)
                ->get();
            if (count($row) <= 0) {
                return redirect('admin/cruds')
                    ->with('message', 'Can not find module')->with('status', 'error');
            }
            $row = $row[0];
            $this->data['row'] = $row;
            $config = CF_decode_json($row->module_config);

            $requestData = array(
                'title'            => $request->get('title'),
                'master'        => $request->get('master'),
                'master_key'    => $request->get('master_key'),
                'module'        => $request->get('module'),
                'table'            => $request->get('table'),
                'key'            => $request->get('key'),
                'field_after'     => $request->get('field_after'),
                'is_new_tab'     => $request->get('is_new_tab'),
                'module_relation'     => ($request->get('module_relation')) ? $request->get('module_relation') : 'single',
                'hide_header_label'     => $request->get('hide_header_label'),
                'field_access_type'     => $request->get('field_access_type'),
                'restricted_role_id'     => $request->get('restricted_role_id'),
                'custom_group_key'     => $request->get('custom_group_key'),
                'wrapper_cls'     => $request->get('wrapper_cls'),
                'prevent_delete'     => $request->get('prevent_delete'),
            );


            $subgrid = array();
            $moduleUpdate = false;
            if (isset($config["subgrid"])) {
                foreach ($config['subgrid'] as $sb) {
                    if ($requestData['module'] == $sb['module']) {
                        $subgrid[] = $requestData;
                        $moduleUpdate = true;
                    } else {
                        $subgrid[] = $sb;
                    }
                }
            }

            if (!$moduleUpdate) {
                $newData[] = $requestData;
                $subgrid = array_merge($subgrid, $newData);
            }

            if (isset($config["subgrid"])) unset($config["subgrid"]);
            $new_config =     array_merge($config, array("subgrid" => $subgrid));


            $affected = DB::table('cruds')
                ->where('id', '=', $id)
                ->update(array('module_config' => CF_encode_json($new_config)));


            if ($request->ajax() == true) {
                return response()->json(array('status' => 'success', 'message' => 'Master Has beed added Successful.'));
            } else {
                return $response->setMessage('Master Has beed added Successful.');
            }
        } else {
            return redirect('admin/cruds/sub/' . $request->get('module_name'))
                ->with('message', 'The following errors occurred')->with('status', 'error')
                ->withErrors($validator)->withInput();
        }
    }

    function getRemovesub(Request $request)
    {
        $id = $request->get('id');
        $module = $request->get('mod');
        $row = DB::table('cruds')->where('id', $id)->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')
                ->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;

        $config = CF_decode_json($row->module_config);
        $subgrid = array();

        foreach ($config['subgrid'] as $sb) {
            if ($sb['module'] != $module) {
                $subgrid[] = $sb;
            }
        }
        unset($config["subgrid"]);
        $new_config =     array_merge($config, array("subgrid" => $subgrid));


        $affected = DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));


        return redirect('admin/cruds/sub/' . $row->module_name)
            ->with('message', 'Master Has removed Successful.')->with('status', 'success');
    }

    function removeStats(Request $request)
    {
        $id = $request->get('id');
        $module = $request->get('mod');
        $parentModule = $request->get('parent_mod');
        $row = DB::table('cruds')->where('id', $id)->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')
                ->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;

        $config = CF_decode_json($row->module_config);
        $stats = array();

        foreach ($config['stats'] as $sb) {
            if ($sb['slug'] != $module) {
                $stats[] = $sb;
            }
        }
        unset($config["stats"]);
        $new_config =     array_merge($config, array("stats" => $stats));


        $affected = DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));


        return redirect('admin/cruds/stats/' . $row->module_name)
            ->with('message', 'Stats Has removed Successful.')->with('status', 'success');
    }
    
    function getReports(Request $request, $id = '')
    {
        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->report_config);
        $this->data['row'] = $row;
        $this->data['reports'] = [];
        $this->data['reportsData'] = [];
        $qryParams = $request->input('mod');
        $qryParams2 = $request->input('parent_mod');
        if($config){
            $this->data['reports'] = $config;       
        }
         if ($qryParams) {
            $reportsData = array_filter($this->data['reports'], function ($value) use ($qryParams,$qryParams2) {
                if ($value['slug'] == $qryParams) {
                    return true;
                }
            });
            $reportsData = array_values($reportsData);
            $this->data['reportsData'] = Arr::has($reportsData, '0') ? $reportsData[0] : []; 
        }
        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['module'] = $row->module_name;
        $this->data['module_name'] = $id;
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        $this->data['modules'] = Crud::all();
        return view('plugins/crud::module.reports', $this->data);
    }

    function postSaveReports(Request $request, BaseHttpResponse $response)
    {
        if (true) {

            $id = $request->get('id');
            $row = DB::table('cruds')->where('id', $id)
                ->get();
            if (count($row) <= 0) {
                return redirect('admin/cruds')
                    ->with('message', 'Can not find module')->with('status', 'error');
            }
            $row = $row[0];
            $this->data['row'] = $row;
            $config = (CF_decode_json($row->report_config)) ? CF_decode_json($row->report_config) : [];

            $requestData = array(
                'title'            => $request->get('title'),
                'is_shortcode' => $request->get('is_shortcode'),
                'slug' => $request->get('slug'),
                'field'     => $request->get('field'),
                'sel_fields'     => $request->get('sel_fields'),
                'sql_query'  => trim($request->get('sql_query'))
               
            );

            $reports = array();
            $moduleUpdate = false;
            if (isset($config)) {
                foreach ($config as $sb) {
                    if ($requestData['slug'] == $sb['slug'] ) {
                        $reports[] = $requestData;
                        $moduleUpdate = true;
                    } else {
                        $reports[] = $sb;
                    }
                }
            }else{
                $reports = $requestData;
            }

            if (!$moduleUpdate) {
                $requestData['slug'] = Str::slug($requestData['title']);
                $newData[] = $requestData;
                $reports = array_merge($reports, $newData);
            }
            $config = [];
            $new_config =     array_merge($config, $reports);




            $affected = DB::table('cruds')
                ->where('id', '=', $id)
                ->update(array('report_config' => CF_encode_json($new_config)));


            if ($request->ajax() == true) {
                return response()->json(array('status' => 'success', 'message' => 'Reports config Has beed added Successful.'));
            } else {
                return $response->setMessage('Reports config Has beed added Successful.');
            }
        } else {
            return $response->setError()->setMessage('Failed to save reports.');
        }
    }
    
    function getScheduler(Request $request, $id = '')
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $this->data['row'] = $row;
        $this->data['fields'] = $config['grid'];
        $this->data['schedulers'] = (isset($config['scheduler']) ? $config['scheduler'] : array());

        $this->data['scheduler_detail'] = [];
        $this->data['roles'] = \Impiger\ACL\Models\Role::get();

        $this->data['scheduler_detail'] = Arr::get($this->data, 'schedulers') ? $this->data['schedulers'] : [];

        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['module'] = $row->module_name;
        $this->data['module_name'] = $id;
        $this->data['type'] = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        $this->data['modules'] = Crud::all();
        return view('plugins/crud::module.scheduler', $this->data);
    }

    function postSaveScheduler(Request $request, BaseHttpResponse $response)
    {
        if (true) {

            $id = $request->get('id');
            $row = DB::table('cruds')->where('id', $id)
                ->get();
            if (count($row) <= 0) {
                return redirect('admin/cruds')
                    ->with('message', 'Can not find module')->with('status', 'error');
            }
            $row = $row[0];
            $this->data['row'] = $row;
            $config = CF_decode_json($row->module_config);

            $requestData = array(
                'title' => $request->get('title'),
                'status_change_to' => $request->get('status_change_to'),
                'status_change_value' => $request->get('status_change_value'),
                'sql_where' => $request->get('sql_where'),
                'notification' => ($request->get('notification')) ? $request->get('notification') : 0,
                'prior_notification' => ($request->get('prior_notification')) ? $request->get('prior_notification') : 0,
                'prior_check' => $request->get('prior_check'),
                'notification_subject' => $request->get('notification_subject'),
                'notification_message' => $request->get('notification_message'),
                'prior_notification_subject' => $request->get('prior_notification_subject'),
                'prior_notification_message' => $request->get('prior_notification_message'),
                'send_to' => $request->get('send_to'),
                'reciever' => $request->get('reciever'),
                'default_reciever' => $request->get('default_reciever'),
                //                'prior_check_field'     => $request->get('prior_check_field'),
                //                'prior_notification_start'     => $request->get('prior_notification_start'),
                //                'prior_notification_stop'     => $request->get('prior_notification_stop'),
            );


            $scheduler = array();
            if (isset($config["scheduler"])) {
                $scheduler = $requestData;
            }



            if (isset($config["scheduler"]))
                unset($config["scheduler"]);
            $new_config = array_merge($config, array("scheduler" => $scheduler));


            $affected = DB::table('cruds')
                ->where('id', '=', $id)
                ->update(array('module_config' => CF_encode_json($new_config)));


            if ($request->ajax() == true) {
                return response()->json(array('status' => 'success', 'message' => 'Scheduler Has been added Successful.'));
            } else {
                return $response->setMessage('Scheduler Has been added Successful.');
            }
        } else {
            return redirect('admin/cruds/sub/' . $request->get('module_name'))
                ->with('message', 'The following errors occurred')->with('status', 'error')
                ->withErrors($validator)->withInput();
        }
    }

    function getEmailConfig(Request $request, $id = '')
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $this->data['row'] = $row;
        $this->data['fields'] = $config['grid'];
        $this->data['email_config'] = (isset($config['email_config']) ? $config['email_config'] : array());

        $this->data['roles'] = \Impiger\ACL\Models\Role::get();

        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['module'] = $row->module_name;
        $this->data['module_name'] = $id;
        $this->data['type'] = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        $this->data['modules'] = Crud::all();
        return view('plugins/crud::module.email-config', $this->data);
    }

    function postSaveEmailConfig(Request $request, BaseHttpResponse $response)
    {
        if (true) {

            $id = $request->get('id');
            $row = DB::table('cruds')->where('id', $id)
                ->get();
            if (count($row) <= 0) {
                return redirect('admin/cruds')
                    ->with('message', 'Can not find module')->with('status', 'error');
            }
            $row = $row[0];
            $this->data['row'] = $row;
            $config = CF_decode_json($row->module_config);

            $requestData = array(
                'create' => $request->get('create'),
                'edit' => $request->get('edit'),
                'subject' => $request->get('subject'),
                'message' => $request->get('message'),
                'send_to' => $request->get('send_to'),
                'reciever_role' => $request->get('reciever_role'),
                'reciever_field' => $request->get('reciever_field'),
                'default_reciever' => $request->get('default_reciever'),
            );


            $emailConfig = array();
            if (isset($config["email_config"])) {
                $emailConfig = $requestData;
            }

            if (isset($config["email_config"]))
                unset($config["email_config"]);
            $new_config = array_merge($config, array("email_config" => $emailConfig));


            $affected = DB::table('cruds')
                ->where('id', '=', $id)
                ->update(array('module_config' => CF_encode_json($new_config)));


            if ($request->ajax() == true) {
                return response()->json(array('status' => 'success', 'message' => 'EmailConfig Has been added Successful.'));
            } else {
                return $response->setMessage('EmailConfig Has been added Successful.');
            }
        } else {
            return redirect('admin/cruds/sub/' . $request->get('module_name'))
                ->with('message', 'The following errors occurred')->with('status', 'error')
                ->withErrors($validator)->withInput();
        }
    }

    function getSql(Request $request, $id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);
        $this->data['sql_select']         = $config['sql_select'];
        $this->data['sql_where']         = $config['sql_where'];
        $this->data['sql_group']         = $config['sql_group'];
        $this->data['module_name']         = $row->module_name;
        $this->data['module']             = 'module';
        $this->data['type']             = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);

        return view('plugins/crud::module.sql', $this->data);
    }

    function postSavesql(Request $request, $id, BaseHttpResponse $response)
    {

        $select     = $request->input('sql_select');
        $where     = $request->input('sql_where');
        $group     = $request->input('sql_group');

        $options = array('facade' => 'DB::');
        $converter = new \RexShijaku\SQLToLaravelBuilder\SQLToLaravelBuilder($options);
        $moduleQuery =  $converter->convert($select . ' ' . $where . ' ' . $group);

        $qryArray = explode("->", $moduleQuery);
        if (Arr::has($qryArray, 0)) {
            $qryLen = count($qryArray);
            unset($qryArray[0]);
            unset($qryArray[$qryLen - 1]);
            $moduleQuery = implode("->", $qryArray);
        }

        try {
            DB::select($select . ' ' . $where . ' ' . $group);
        } catch (Exception $e) {
            // Do something when query fails.
            $error = 'Error : ' . $select . ' ' . $where . ' ' . $group;
            return redirect('admin/cruds/sql/' . $request->input('module_name'))
                ->with('message', $error)->with('status', 'error');
        }

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect($this->module)
                ->with('message', 'Can not find module')->with('status', 'error');
        }

        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $oldColumns = [];
        foreach ($config['forms'] as $info) {
            $oldColumns[$info['field']] = $info;
        }
        $alterSqlData = ($row->alter_script_data && $row->alter_script_data != "null") ? json_decode($row->alter_script_data, true) : ["create" => [], "modify" => [], "remove" => []];
        $oldColumnKeys = array_keys($oldColumns);
        $newColumnKeys = [];
        $this->data['row'] = $row;

        $pdo = DB::getPdo();
        $columns = Crud::getColoumnInfo($select . ' ' . $where . ' ' . $group);
        $i = 0;
        $form = array();
        $grid = array();
        foreach ($columns as $idx => $field) {
            $fieldConfig = (object)[];
            $fieldConfig->Null = Arr::get($field, 'Null');
            $fieldConfig->dbType = Arr::get($field, 'dbType');
            $Key = (isset($field['flags'][1]) && $field['flags'][1] == 'primary_key'  ? 'PRI' : '');
            $fieldConfig->Field = $field['name'];
            $fieldConfig->Table = $field['table'];
            $fieldConfig->Type = $field['native_type'];
            $fieldConfig->Key = $Key;

            $name = $field['name'];
            $alias = $field['table'];
            $type = $field['native_type'];
            $grids =  self::configGrid($name, $alias, $type, $i);

            foreach ($config['grid'] as $g) {
                if (!isset($g['type'])) $g['type'] = 'text';
                if ($g['field'] == $name && $g['alias'] == $alias) {
                    $grids = $g;
                }
            }
            $grid[] = $grids;

            if ($row->module_db == $alias) {
                $forms = self::configForm($name, $alias, $type, $i, [], $fieldConfig);
                foreach ($config['forms'] as $f) {
                    if ($f['field'] == $name && $f['alias'] == $alias) {
                        $f['dbType'] = $forms['dbType'];
                        $f['isNullable'] = $forms['isNullable'];
                        $f['defaultVal'] = $forms['defaultVal'];
                        $forms = $f;
                    }
                }

                $fieldInfo = Arr::get($oldColumns, $name);
                $newColumnKeys[] = $name;
                $data = [];
                $data['type'] = $forms['type'];
                $data['isNullable'] = $forms['isNullable'];
                $data['defaultVal'] = $forms['defaultVal'];
                $data['dbType'] = $forms['dbType'];
                $data['field'] = $forms['field'];
                $data['alias'] = $forms['alias'];
                $fieldAfter = ($idx > 0) ? Arr::get($columns, ($idx - 1) . ".name") : "";

                if ($fieldInfo) {
                    $oldFormNullField = (Arr::get($fieldInfo, 'isNullable')) ? Arr::get($fieldInfo, 'isNullable') : "NO";
                    $curFormNullField = (Arr::get($forms, 'isNullable')) ? Arr::get($forms, 'isNullable') : "NO";
                    $data['after'] = $fieldAfter;

                    if (strtolower(Arr::get($fieldInfo, 'dbType')) != strtolower($data['dbType'])) {
                        $alterSqlData['modify'][$name] = $data;
                    }

                    if (strtolower(Arr::get($fieldInfo, 'defaultVal')) != strtolower($data['defaultVal'])) {
                        $alterSqlData['modify'][$name] = $data;
                    }

                    if ($oldFormNullField != $curFormNullField) {
                        $alterSqlData['modify'][$name] = $data;
                    }
                } else {
                    if (!in_array($name, $oldColumnKeys)) {
                        $data['after'] = $fieldAfter;
                        $alterSqlData['create'][$name] = $data;
                    }
                }

                $form[] = $forms;
            }


            $i++;
        }

        $diff = array_values(array_diff($oldColumnKeys, $newColumnKeys));
        $alterSqlData['remove'] = (is_array($diff) && is_array($alterSqlData['remove'])) ? array_merge($alterSqlData['remove'], $diff) : $alterSqlData['remove'];
        // Remove Old Grid
        unset($config["forms"]);
        // Remove Old Form
        unset($config["grid"]);
        // Remove Old Query
        unset($config["sql_group"]);
        unset($config["sql_select"]);
        unset($config["sql_where"]);

        // Inject New Grid
        $new_config = array(
            "sql_select"         => $select,
            "sql_where"            => $where,
            "sql_group"            => $group,
            "grid"                 => $grid,
            "forms"             => $form
        );

        $config =     array_merge($config, $new_config);
        $moduleQueries = $moduleQuery;
        $alterSqlData = $alterSqlData;

        DB::table('cruds')
            ->where('id', '=', $row->id)
            ->update(array('module_config' => CF_encode_json($config), 'module_queries' => $moduleQueries, 'alter_script_data' => json_encode($alterSqlData)));

        if ($request->ajax() == true) {
            return response()->json(array('status' => 'success', 'message' => 'SQL Has Changed Successful.'));
        } else {

            return $response->setMessage('SQL Has Changed Successful.');
        }
    }

    function getTable($id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $fp = base_path() . '/platform/plugins/crud/resources/views/module/template/' . $this->getTemplateName($row->module_type) . '/config/info.json';
        $fp = json_decode(file_get_contents($fp));
        $this->data['config'] = $fp;

        $config = CF_decode_json($row->module_config);
        $this->data['tables']     = $config['grid'];

        $this->data['module'] = 'module';
        $this->data['module_name'] = $row->module_name;
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        return view('plugins/crud::module.table', $this->data);
    }


    function getForm($id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);
        // Get config Info
        $fp = base_path() . '/platform/plugins/crud/resources/views/module/template/' . $this->getTemplateName($row->module_type) . '/config/info.json';
        $fp = json_decode(file_get_contents($fp));
        $this->data['config'] = $fp;

        $this->data['forms']     = $config['forms'];
        $this->data['form_column'] = (isset($config['form_column']) ? $config['form_column'] : 1);
        $this->data['module'] = 'module';
        $this->data['module_name'] = $row->module_name;
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        return view('plugins/crud::module.form', $this->data);
    }

    public function postSaveform(Request $request, BaseHttpResponse $response)
    {

        $id = $request->input('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];

        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);
        $lang = langOption();
        $this->data['tables']     = $config['grid'];
        $total = count($_POST['field']);
        extract($_POST);
        $f = array();
        $formOldConfig = [];
        foreach ($config['forms'] as $fieldInfo) {
            $formOldConfig[$fieldInfo['field']] = $fieldInfo;
        }

        for ($i = 1; $i <= $total; $i++) {
            $language = array();
            foreach ($lang as $l) {
                if ($l['folder'] != 'en') {
                    $label_lang = (isset($_POST['language'][$i][$l['folder']]) ? $_POST['language'][$i][$l['folder']] : '');
                    $language[$l['folder']] = $label_lang;
                }
            }
            $f[] = array(
                "field"         => $field[$i],
                "alias"         => $alias[$i],
                "language"         => $language,
                "label"         => $label[$i],
                'form_group'    => $form_group[$i],
                'required'        => (isset($required[$i]) ? $required[$i] : 0),
                'bulk_edit'        => (isset($bulk_edit[$i]) ? $bulk_edit[$i] : 0),
                'view'            => (isset($view[$i]) ? 1 : 0),
                'type'            => $type[$i],
                'dbType'            => Arr::get($formOldConfig, $field[$i] . '.dbType'),
                'isNullable'            => Arr::get($formOldConfig, $field[$i] . '.isNullable'),
                'defaultVal'            => Arr::get($formOldConfig, $field[$i] . '.defaultVal'),
                'casting'            => (isset($casting[$i]) ? $casting[$i] : ''),
                'add'            => 1,
                'size'            => '0',
                'edit'            => 1,
                'search'        => (isset($search[$i]) ? $search[$i] : 0),
                "sortlist"         => $sortlist[$i],
                'limited'    => (isset($limited[$i]) ? $limited[$i] : ''),
                'repeater_data'    => Arr::get($formOldConfig, $field[$i] . '.repeater_data'),
                'editor_config_buttons'    => Arr::get($formOldConfig, $field[$i] . '.editor_config_buttons'),
                'option'        => array(
                    "opt_type"                 => $opt_type[$i],
                    "specific_entity_type" => (isset($specific_entity_type[$i]) ? $specific_entity_type[$i] : ''),
                    "custom_function" => (isset($custom_function[$i]) ? $custom_function[$i] : ''),
                    "dependant_table" => (isset($dependant_table[$i]) ? $dependant_table[$i] : ''),
                    "lookup_query"             => $lookup_query[$i],
                    "lookup_table"             => $lookup_table[$i],
                    "lookup_key"             => $lookup_key[$i],
                    "lookup_value"            => $lookup_value[$i],
                    "where_cndn"            => (isset($where_cndn[$i]) ? $where_cndn[$i] : ''),
                    'is_dependency'            => $is_dependency[$i],
                    'select_multiple'            => (isset($select_multiple[$i]) ? $select_multiple[$i] : 0),
                    'image_multiple'            => (isset($image_multiple[$i]) ? $image_multiple[$i] : 0),
                    'lookup_dependency_key'    => $lookup_dependency_key[$i],
                    'path_to_upload'        => $path_to_upload[$i],
                    'resize_width'            => $resize_width[$i],
                    'resize_height'            => $resize_height[$i],
                    'upload_type'            => $upload_type[$i],
                    'tooltip'                => $tooltip[$i],
                    'attribute'                => $attribute[$i],
                    'extend_class'            => $extend_class[$i],
                    'field_access_type' => Arr::get($formOldConfig, $field[$i] . '.option.field_access_type'),
                    'restricted_role_id' => Arr::get($formOldConfig, $field[$i] . '.option.restricted_role_id'),
                    'restrict_based_on' => Arr::get($formOldConfig, $field[$i] . '.option.restrict_based_on'),
                    'disabled' => Arr::get($formOldConfig, $field[$i] . '.option.disabled'),
                    'hidden' => Arr::get($formOldConfig, $field[$i] . '.option.hidden'),
                    'validation_msg' => Arr::get($formOldConfig, $field[$i] . '.option.validation_msg'),
                    'wrapper_grid_cls' => Arr::get($formOldConfig, $field[$i] . '.option.wrapper_grid_cls'),
                    'generate_custom_code' => Arr::get($formOldConfig, $field[$i] . '.option.generate_custom_code'),
                    'readonly' => Arr::get($formOldConfig, $field[$i] . '.option.readonly'),
                    'default_value' => Arr::get($formOldConfig, $field[$i] . '.option.default_value'),
                ),
            );
        }

        unset($config["forms"]);
        $new_config =     array_merge($config, array("forms" => $f));
        $data['module_config'] = CF_encode_json($new_config);

        DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));

        if ($request->ajax() == true) {
            return response()->json(array('status' => 'success', 'message' => 'Module Forms Has Changed Successful'));
        } else {

            return $response->setMessage('Module Forms Has Changed Successful.');
        }
    }


    public function getEditform(Request $request, $id)
    {
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);

        $id = $id;
        $field_id     = $request->input('field');
        $alias         = $request->input('alias');

        $f = array();
        foreach ($config['forms'] as $form) {
            $tooltip = '';
            $attribute = '';
            if (isset($form['option']['tooltip'])) $tooltip = $form['option']['tooltip'];
            if (isset($form['option']['attribute'])) $attribute = $form['option']['attribute'];
            $size = isset($form['size']) ? $form['size'] : 'span12';
            if ($form['field'] == $field_id && $form['alias'] == $alias) {
                //$multiVal = explode(":",$form['option']['lookup_value']);
                $f = array(
                    "field"     => $form['field'],
                    "alias"     => $form['alias'],
                    "label"     =>  $form['label'],
                    'form_group'    =>  $form['form_group'],
                    'required'        => $form['required'],
                    'bulk_edit'        => isset($form['bulk_edit']) ? $form['bulk_edit'] : "",
                    'view'            => $form['view'],
                    'type'            => $form['type'],
                    'dbType'            => isset($form['dbType']) ? $form['dbType'] : "",
                    'isNullable'            => isset($form['isNullable']) ? $form['isNullable'] : "",
                    'defaultVal'            => isset($form['defaultVal']) ? $form['defaultVal'] : "",
                    'casting'            => isset($form['casting']) ? $form['casting'] : "",
                    'add'            => $form['add'],
                    'size'            => $size,
                    'edit'            => $form['edit'],
                    'search'        => $form['search'],
                    "sortlist"         => $form['sortlist'],
                    'limited'           => isset($form['limited']) ? $form['limited'] : '',
                    'repeater_data'           => Arr::get($form, 'repeater_data'),
                    'editor_config_buttons'           => Arr::get($form, 'editor_config_buttons'),
                    'option'        => array(
                        "opt_type"                 => $form['option']['opt_type'],
                        "specific_entity_type" => (isset($form['option']['specific_entity_type']) ? $form['option']['specific_entity_type'] : ''),
                        "custom_function" => (isset($form['option']['custom_function']) ? $form['option']['custom_function'] : ''),
                        "dependant_table" => (isset($form['option']['dependant_table']) ? $form['option']['dependant_table'] : ''),
                        "lookup_query"             => $form['option']['lookup_query'],
                        "lookup_table"             => $form['option']['lookup_table'],
                        "lookup_key"             => $form['option']['lookup_key'],
                        "lookup_value"            => $form['option']['lookup_value'],
                        "where_cndn"            => isset($form['option']['where_cndn']) ? $form['option']['where_cndn'] : "",
                        'is_dependency'            => $form['option']['is_dependency'],
                        'select_multiple'            => (isset($form['option']['select_multiple']) ? $form['option']['select_multiple'] : 0),
                        'image_multiple'            => (isset($form['option']['image_multiple']) ? $form['option']['image_multiple'] : 0),
                        'lookup_dependency_key'    => $form['option']['lookup_dependency_key'],
                        'path_to_upload'        => $form['option']['path_to_upload'],
                        'upload_type'            => $form['option']['upload_type'],
                        'resize_width'            => isset($form['option']['resize_width']) ? $form['option']['resize_width'] : '',
                        'resize_height'            => isset($form['option']['resize_height']) ? $form['option']['resize_height'] : '',
                        'extend_class'            => isset($form['option']['extend_class']) ? $form['option']['extend_class'] : '',
                        'tooltip'                => $tooltip,
                        'attribute'                => $attribute,
                        'extend_class'            => isset($form['option']['extend_class']) ? $form['option']['extend_class'] : '',
                        'prefix'            => isset($form['option']['prefix']) ? $form['option']['prefix'] : '',
                        'sufix'            => isset($form['option']['sufix']) ? $form['option']['sufix'] : '',
                        'field_access_type' => isset($form['option']['field_access_type']) ? $form['option']['field_access_type'] : 1,
                        'restricted_role_id' => isset($form['option']['restricted_role_id']) ? $form['option']['restricted_role_id'] : '',
                        'restrict_based_on' => isset($form['option']['restrict_based_on']) ? $form['option']['restrict_based_on'] : '',
                        'disabled' => isset($form['option']['disabled']) ? $form['option']['disabled'] : '',
                        'hidden' => isset($form['option']['hidden']) ? $form['option']['hidden'] : '',
                        'validation_msg' => isset($form['option']['validation_msg']) ? $form['option']['validation_msg'] : '',
                        'wrapper_grid_cls' => isset($form['option']['wrapper_grid_cls']) ? $form['option']['wrapper_grid_cls'] : '',
                        'generate_custom_code' => isset($form['option']['generate_custom_code']) ? $form['option']['generate_custom_code'] : '',
                        'readonly' => isset($form['option']['readonly']) ? $form['option']['readonly'] : '',
                        'default_value' => isset($form['option']['default_value']) ? $form['option']['default_value'] : '',
                    ),
                );
            }
        }


        $this->data['field_type_opt'] = array(
            'hidden'        => 'Hidden',
            'text'            => 'Text',
            'number'            => 'Number',
            'password'       => 'Password',
            'text_date'        => 'Date',
            'text_datetime'        => 'Date & Time',
            'textarea'        => 'Textarea',
            'textarea_editor'    => 'Textarea With Editor ',
            'select'        => 'Select Option',
            'radio'            => 'Radio',
            'checkbox'        => 'Checkbox',
            'file'            => 'Upload File',
            'color'        => 'Color Picker',
            'maps'        => 'Google Maps',
            'repeater'        => 'Repeater',
            'time'        => 'Time',
            'tags'        => 'Tags',
        );

        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['roles'] = \Impiger\ACL\Models\Role::get();
        $this->data['f']     = $f;
        $this->data['id']     = $id;
        $this->data['entity_modules'] = Crud::where('is_entity', 1)->select(['id', 'module_name'])->get();
        $this->data['module'] = 'module';
        $this->data['module_name'] = $row->module_name;
        return view('plugins/crud::module.field', $this->data);
    }

    function postSaveformfield(Request $request, BaseHttpResponse $response)
    {
        $lookup_value = (is_array($request->input('lookup_value')) ? implode("|", array_filter($request->input('lookup_value'))) : '');
        $id = $request->input('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return $response->setPreviousUrl(route('admin/cruds'))
                ->setMessage('Can not find module');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);

        $view = 0;
        $search = 0;
        if (!is_null($request->input('view'))) $view = 1;
        if (!is_null($request->input('search'))) $search = 1;

        if (preg_match('/(select|radio|checkbox)/', $request->input('type'))) {
            if ($request->input('opt_type') == 'datalist') {
                $datalist = '';
                $cf_val     = $request->input('custom_field_val');
                $cf_display = $request->input('custom_field_display');
                for ($i = 0; $i < count($cf_val); $i++) {
                    $value         = $cf_val[$i];
                    if (isset($cf_display[$i])) {
                        $display = $cf_display[$i];
                    } else {
                        $display = 'none';
                    }
                    $datalist .= $value . ':' . $display . '|';
                }
                $datalist = substr($datalist, 0, strlen($datalist) - 1);
            } else {
                $datalist = '';
            }
        } else {
            $datalist = '';
        }

        $new_field = array(
            "field"         => $request->input('field'),
            "alias"         => $request->input('alias'),
            "label"         => $request->input('label'),
            "form_group"     => $request->input('form_group'),
            'required'        => $request->input('required'),
            'bulk_edit'        => (!is_null($request->input('bulk_edit')) ? '1' : '0'),
            'view'            => $view,
            'type'            => $request->input('type'),
            'dbType'          => $request->input('dbType'),
            'isNullable'      => $request->input('isNullable'),
            'defaultVal'      => $request->input('defaultVal'),
            'casting'      => $request->input('casting'),
            'add'            => 1,
            'edit'            => 1,
            'search'        => $request->input('search'),
            'size'            =>     '',
            'sortlist'        => $request->input('sortlist'),
            'limited'           => $request->input('limited'),
            'repeater_data' => (!is_null($request->input('repeater_data'))) ? $request->input('repeater_data') : "",
            'editor_config_buttons' => (!is_null($request->input('editor_config_buttons'))) ? $request->input('editor_config_buttons') : 0,
            'option'        => array(
                "opt_type"         =>  $request->input('opt_type'),
                "specific_entity_type" => (!is_null($request->input('specific_entity_type'))) ? $request->input('specific_entity_type') : '',
                "custom_function" => (!is_null($request->input('custom_function'))) ? $request->input('custom_function') : '',
                "dependant_table" => (!is_null($request->input('dependant_table'))) ? $request->input('dependant_table') : '',
                "lookup_query"     =>  $datalist,
                "lookup_table"     =>  $request->input('lookup_table'),
                "lookup_key"     =>  $request->input('lookup_key'),
                "lookup_value"    =>     $lookup_value,
                "where_cndn"    =>     $request->input('where_cndn'),
                'is_dependency'    =>  $request->input('is_dependency'),
                'select_multiple'    => (!is_null($request->input('select_multiple')) ? '1' : '0'),
                'image_multiple'    => (!is_null($request->input('image_multiple')) ? '1' : '0'),
                'lookup_dependency_key' =>  $request->input('lookup_dependency_key'),
                'path_to_upload' =>  $request->input('path_to_upload'),
                'upload_type'    =>  $request->input('upload_type'),
                'resize_width'    =>  $request->input('resize_width'),
                'resize_height'    =>  $request->input('resize_height'),
                'tooltip'        =>  $request->input('tooltip'),
                'attribute'        =>  $request->input('attribute'),
                'extend_class'    =>  $request->input('extend_class'),
                'prefix'                => $request->input('prefix'),
                'sufix'                => $request->input('sufix'),
                'field_access_type' => (!is_null($request->input('field_access_type'))) ? $request->input('field_access_type') : 1,
                'restricted_role_id' => (!is_null($request->input('restricted_role_id'))) ? $request->input('restricted_role_id') : '',
                'restrict_based_on' => (!is_null($request->input('restrict_based_on'))) ? $request->input('restrict_based_on') : '',
                'disabled' => (!is_null($request->input('disabled'))) ? $request->input('disabled') : '',
                'hidden' => (!is_null($request->input('hidden'))) ? $request->input('hidden') : '',
                'validation_msg' => (!is_null($request->input('validation_msg'))) ? $request->input('validation_msg') : '',
                'wrapper_grid_cls' => (!is_null($request->input('wrapper_grid_cls'))) ? $request->input('wrapper_grid_cls') : '',
                'generate_custom_code' => (!is_null($request->input('generate_custom_code'))) ? $request->input('generate_custom_code') : '',
                'readonly' => (!is_null($request->input('readonly'))) ? $request->input('readonly') : '',
                'default_value' => (!is_null($request->input('default_value'))) ? $request->input('default_value') : '',
            )
        );

        $forms = array();

        foreach ($config['forms'] as $form_view) {
            if ($form_view['field'] == $request->input('field') && $form_view['alias'] == $request->input('alias')) {
                $new_form = $new_field;
            } else {
                $new_form  = $form_view;
            }
            $forms[] = $new_form;
        }

        unset($config["forms"]);
        $new_config =     array_merge($config, array("forms" => $forms));
        DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));
        return $response->setMessage('Forms Has Changed Successful.');
    }


    public function postSavetable(Request $request, BaseHttpResponse $response)
    {
        //$this->beforeFilter('csrf', array('on'=>'post'));

        $id = $request->input('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $lang   = langOption();
        $grid   = array();
        $total  = count($_POST['field']);
        extract($_POST);
        for ($i = 1; $i <= $total; $i++) {
            $language = array();
            foreach ($lang as $l) {
                if ($l['folder'] != 'en') {
                    $label_lang = (isset($_POST['language'][$i][$l['folder']]) ? $_POST['language'][$i][$l['folder']] : '');
                    $language[$l['folder']] = $label_lang;
                }
            }

            $grid[] = array(
                'field'        => $field[$i],
                'alias'        => $alias[$i],
                'language'    => $language,
                'label'        => $label[$i],
                'view'        => (isset($view[$i]) ? 1 : 0),
                'edit'        => (isset($edit[$i]) ? 1 : 0),
                'visibility'    => (isset($visibility[$i]) ? 1 : 0),
                'sortable'    => (isset($sortable[$i]) ? 1 : 0),
                'search'    => (isset($search[$i]) ? 1 : 0),
                'download'    => (isset($download[$i]) ? 1 : 0),
                'frozen'    => (isset($frozen[$i]) ? 1 : 0),
                'limited'    => (isset($limited[$i]) ? $limited[$i] : ''),
                'width'        => $width[$i],
                'align'        => $align[$i],
                'sortlist'    => $sortlist[$i],
                'conn'    =>     array(
                    'valid'     => $conn_valid[$i],
                    'db'        => $conn_db[$i],
                    'key'        => $conn_key[$i],
                    'display'    => $conn_display[$i]
                ),
                'format_as'     => (isset($format_as[$i]) ? $format_as[$i] : ''),
                'format_value'  => (isset($format_value[$i]) ? $format_value[$i] : '')
            );
        }

        unset($config["grid"]);
        $new_config =     array_merge($config, array("grid" => $grid));
        $data['module_config'] = CF_encode_json($new_config);



        DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));

        if ($request->ajax() == true) {
            return response()->json(array('status' => 'success', 'message' => 'Module Table Has Been Save Successfull'));
        } else {
            return $response->setMessage('Module Table Has Been Save Successfull');
        }
    }

    function getPermission($id)
    {

        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        // Get config Info
        $fp = base_path() . '/platform/plugins/crud/resources/views/module/template/' . $this->getTemplateName($row->module_type) . '/config/info.json';
        $fp = json_decode(file_get_contents($fp), true);
        $this->data['config'] = $fp;

        $this->data['module'] = 'module';
        $this->data['module_name'] = $row->module_name;
        $config = CF_decode_json($row->module_config);
        $this->data['type']     = $row->module_type;

        $tasks = $fp['access'];
        /* Update permission global / own access new ver 1.1
           Adding new param is_global
           End Update permission global / own access new ver 1.1
        */
        if (isset($config['tasks'])) {
            foreach ($config['tasks'] as $row) {
                if ($row['item'] == 'is_CSV') {
                    $row['item'] = 'is_csv';
                }

                $tasks[$row['item']] = $row['title'];
            }
        }
        unset($tasks['is_CSV']);
        $tasks['is_csv'] = 'CSV';
        $this->data['tasks'] = $tasks;
        $this->data['groups'] = DB::table('tb_groups')->get();

        $access = array();
        foreach ($this->data['groups'] as $r) {
            //    $GA =  Crud::gAccessss($this->uri->rsegment(3),$row['group_id']);
            $group = ($r->group_id != null ? "and group_id ='" . $r->group_id . "'" : "");
            $GA = DB::select("SELECT * FROM tb_groups_access where id ='" . $row->id . "' $group");
            if (count($GA) >= 1) {
                $GA = $GA[0];
            }

            $access_data = (isset($GA->access_data) ? json_decode($GA->access_data, true) : array());
            $rows = array();
            //$access_data = json_decode($AD,true);
            $rows['group_id'] = $r->group_id;
            $rows['group_name'] = $r->name;
            foreach ($tasks as $item => $val) {
                $rows[$item] = (isset($access_data[$item]) && $access_data[$item] == 1  ? 1 : 0);
            }
            $access[$r->name] = $rows;
        }
        $this->data['access'] = $access;
        $this->data['groups_access'] = DB::select("select * from tb_groups_access where id ='" . $row->id . "'");
        $this->data['type']     = ($row->module_type == 'ajax' ? 'addon' : $row->module_type);
        return view('plugins/crud::module.permission', $this->data);
    }

    public function postSavepermission(Request $request, BaseHttpResponse $response)
    {

        $id = $request->input('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);

        $fp = base_path() . '/platform/plugins/crud/resources/views/module/template/' . $this->getTemplateName($row->module_type) . '/config/info.json';
        $fp = json_decode(file_get_contents($fp), true);
        $tasks = $fp['access'];
        /* Update permission global / own access new ver 1.1
           Adding new param is_global
           End Update permission global / own access new ver 1.1
        */
        if (isset($config['tasks'])) {
            foreach ($config['tasks'] as $row) {
                $tasks[$row['item']] = $row['title'];
            }
        }
        unset($tasks['is_CSV']);
        $tasks['is_csv'] = 'CSV';
        $permission = array();
        $groupID = $request->input('group_id');
        for ($i = 0; $i < count($groupID); $i++) {
            // remove current group_access
            $group_id = $groupID[$i];
            DB::table('tb_groups_access')
                ->where('id', '=', $request->input('id'))
                ->where('group_id', '=', $group_id)
                ->delete();

            $arr = array();
            $id = $groupID[$i];
            foreach ($tasks as $t => $v) {
                $arr[$t] = (isset($_POST[$t][$id]) ? "1" : "0");
            }
            $permissions = json_encode($arr);


            $data = array(
                "access_data"    => $permissions,
                "id"        => $request->input('id'),
                "group_id"        => $groupID[$i],
            );
            DB::table('tb_groups_access')->insert($data);
        }

        if ($request->ajax() == true) {
            return response()->json(array('status' => 'success', 'message' => 'Permission Has Changed Successful'));
        } else {
            return $response->setMessage('Permission Has Changed Successful');
        }
    }

    function getSource(Request $request, $id)
    {
        $row = DB::table('cruds')->where('module_name', $id)->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $this->data['module'] = 'module';

        $this->data['module_lang'] = json_decode($row->module_lang, true);
        $this->data['module_name'] = $row->module_name;
        $config = CF_decode_json($row->module_config, true);
        $this->data['tables']     = $config['grid'];
        $this->data['type']     = $row->module_type;

        return view('plugins/crud::module.source', $this->data);
    }

    function postSource(Request $request, BaseHttpResponse $response)
    {

        $_POST['dir'] = urldecode($_POST['dir']);
        $root = base_path() . '/resources/views';
        $res = '';


        if (file_exists($root . $_POST['dir'])) {
            $files = scandir($root . $_POST['dir']);
            natcasesort($files);
            if (count($files) > 2) { /* The 2 accounts for . and .. */
                $res .=  "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
                // All dirs
                foreach ($files as $file) {
                    if (file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file)) {
                        $res .=  "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
                    }
                }
                // All files
                foreach ($files as $file) {
                    if (file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file)) {
                        $ext = preg_replace('/^.*\./', '', $file);
                        $res .=  "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
                    }
                }
                $res .=  "</ul>";
            }

            return $res;
        } else {

            return 'Folder is not exists';
        }
    }

    function getCode(Request $request)
    {
        $path = $request->input('path');
        $file = base_path() . '/resources/views' . $path;
        if (file_exists($file)) {
            return array(
                'path'  =>  'resources/views' . $path,
                'content'   => file_get_contents($file)
            );
        } else {
            return 'error';
        }
    }

    function postCode(Request $request, $id, BaseHttpResponse $response)
    {
        $content = $request->input('content_html');
        $filename = base_path() . '/' . $request->input('path');
        if (file_exists($filename)) {
            $fp = fopen($filename, "w+");
            fwrite($fp, $content);
            fclose($fp);
            return $response->setMessage('File has been changed');
        } else {
            return response()->json(['status' => 'error', 'message' =>  alert('success', 'Error while saving changes')]);
        }
    }

    public function getDuplicate(Request $request, $id)
    {

        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;

        $this->data['module'] = 'module';
        $this->data['module_lang'] = json_decode($row->module_lang, true);
        $this->data['module_name'] = $row->module_name;

        $config = CF_decode_json($row->module_config, true);

        $this->data['tables']     = $config['grid'];
        $this->data['type']     = $row->module_type;

        return view('plugins/crud::module.duplicate', $this->data);
    }

    public function postDuplicate(Request $request, $id, BaseHttpResponse $response)
    {

        $rules = array(
            'module_name'    => 'required|alpha|min:2|unique:cruds',
            'module_title'    => 'required',
            'module_note'    => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {

            $row = DB::table('cruds')->where('id', $id)
                ->get();
            if (count($row) <= 0) {
                return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
            }
            $row = $row[0];
            $this->data['row'] = $row;
            $config = CF_decode_json($row->module_config, true);

            foreach (DB::select("SHOW COLUMNS FROM cruds ") as $column) {
                if ($column->Field != 'id')
                    $columns[] = $column->Field;
            }

            $sql = "INSERT INTO cruds (" . implode(",", $columns) . ") ";
            $sql .= " SELECT " . implode(",", $columns) . " FROM cruds WHERE id = '" . $id . "'";
            DB::select($sql);

            $res = DB::select('select * from cruds order by id desc limit 1');
            if (count($res) >= 1) {
                $row = $res[0];
                $data = array(
                    'module_title'  => trim($request->module_title),
                    'module_name'   => trim($request->module_name),
                    'module_note'   => trim($request->module_note),
                    'module_author' => \Session::get('fid')
                );
                DB::table('cruds')->where('id', $row->id)->update($data);

                // Add Default permission
                $tasks = array(
                    'is_global'        => 'Global',
                    'is_view'        => 'View ',
                    'is_detail'        => 'Detail',
                    'is_add'        => 'Add ',
                    'is_edit'        => 'Edit ',
                    'is_remove'        => 'Remove ',
                    'is_excel'        => 'Excel ',

                );
                $groups = DB::table('tb_groups')->get();
                $rows = DB::table('cruds')->where('id', $row->id)->get();
                if (count($rows) >= 1) {
                    $id = $rows[0];

                    foreach ($groups as $g) {
                        $arr = array();
                        foreach ($tasks as $t => $v) {
                            if ($g->group_id == '1') {
                                $arr[$t] = '1';
                            } else {
                                $arr[$t] = '0';
                            }
                        }
                        $data = array(
                            "access_data"    => json_encode($arr),
                            "id"        => $id->id,
                            "group_id"        => $g->group_id,
                        );
                        DB::table('tb_groups_access')->insert($data);
                    }
                }
                return redirect('admin/cruds/rebuild/' . $row->id . '?mode=duplicate');
            } else {
                return redirect('admin/cruds')->with('message', 'Failed to Duplicate Module !')->with('status', 'error');
            }
        }
    }

    function getSubform(Request $request, $id = 0)
    {
        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $this->data['row'] = $row;
        $this->data['fields'] = $config['grid'];
        $this->data['subform'] = (isset($config['subform']) ? $config['subform'] : array());
        $this->data['tables'] = Crud::getTableList($this->db);
        $this->data['module'] = $row->module_name;
        $this->data['module_name'] = $id;
        $this->data['type']           = $row->module_type;
        $this->data['modules'] = Crud::all();

        return view('plugins/crud::module.subform', $this->data);
    }

    function postSaveconfig(Request $request, BaseHttpResponse $response)
    {
        $data = array(
            'module_note'      => $request->input('module_note'),
            'module_alias'      => $request->input('module_alias'),
            'is_entity'        => $request->input('is_entity'),
            'is_bulkupload'    => $request->input('is_bulkupload'),
            'is_multi_lingual' => $request->input('is_multi_lingual'),
            'is_customized' => ($request->input('is_customized')) ?: 0,
            'module_actions' => $request->input('module_actions'),
            'module_action_meta' => $request->input('module_action_meta'),
            'dependent_module' => $request->input('dependent_module'),
            'module_before_insert' => $request->input('module_before_insert'),
            'insert_user_before' => $request->input('insert_user_before'),
            'is_shortcode_form' => $request->input('is_shortcode_form'),
            'is_shortcode_table' => $request->input('is_shortcode_table'),
            'shortcode_options' => $request->input('shortcode_options'),
        );
        $lang = langOption();
        $language = array();
        foreach ($lang as $l) {
            if ($l['folder'] != 'en') {
                $label_lang = (isset($_POST['language_title'][$l['folder']]) ? $_POST['language_title'][$l['folder']] : '');
                $note_lang = (isset($_POST['language_note'][$l['folder']]) ? $_POST['language_note'][$l['folder']] : '');

                $language['title'][$l['folder']] = $label_lang;
                $language['note'][$l['folder']] = $note_lang;
            }
        }

        $data['module_lang'] = json_encode($language);
        $id = $request->input('id');
        DB::table('cruds')->where('id', '=', $id)->update($data);

        if ($request->ajax() == true) {
            return $response->setMessage('Module Info Has Been updated Successfull');
        } else {
            return $response->setMessage('Module Info Has Been updated Successfull');
        }
    }

    public function postSavesetting(Request $request, BaseHttpResponse $response)
    {
        $id = $request->input('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);
        $setting = array(
            'gridtype'        => '',
            'orderby'        => $request->input('orderby'),
            'ordertype'        => $request->input('ordertype'),
            'perpage'        => $request->input('perpage'),
            'frozen'        => (!is_null($request->input('frozen'))  ? 'true' : 'false'),
            'form-method'   => (!is_null($request->input('form-method'))  ? $request->input('form-method') : 'native'),
            'view-method'        => (!is_null($request->input('view-method'))  ? $request->input('view-method') : 'native'),
            'inline'        => (!is_null($request->input('inline'))  ? 'true' : 'false'),
            'view_details_type' => (!is_null($request->input('view_details_type'))  ? $request->input('view_details_type') : 0),
            'hide_menu' => (!is_null($request->input('hide_menu'))  ? $request->input('hide_menu') : false),
            'special_notes' => (!is_null($request->input('special_notes'))  ? $request->input('special_notes') : ""),
            'is_captcha' => (!is_null($request->input('is_captcha'))  ? $request->input('is_captcha') : 0),
            'action_box' => (!is_null($request->input('action_box'))  ? $request->input('action_box') : 'right'),
            'menu_priority' => (!is_null($request->input('menu_priority'))  ? $request->input('menu_priority') : 5),
            'menu_icon' => (!is_null($request->input('menu_icon'))  ? $request->input('menu_icon') : NULL),
            'custom_js' => (!is_null($request->input('custom_js'))  ? $request->input('custom_js') : 0),
            'is_gallery_image' => (!is_null($request->input('is_gallery_image'))  ? $request->input('is_gallery_image') : 0),
            'is_custom_action' => (!is_null($request->input('is_custom_action'))  ? $request->input('is_custom_action') : 0),
            'is_subscription_related_module' => (!is_null($request->input('is_subscription_related_module'))  ? $request->input('is_subscription_related_module') : 0),
            'hide_module_actions' => (!is_null($request->input('hide_module_actions'))  ? $request->input('hide_module_actions') : ""),
            'revision_history' => (!is_null($request->input('revision_history'))  ? $request->input('revision_history') : ""),
            'domain_mapping' => (!is_null($request->input('domain_mapping'))  ? $request->input('domain_mapping') : 0),
        );
        if (isset($config['setting'])) unset($config['setting']);

        $new_config =     array_merge($config, array("setting" => $setting));
        $data['module_config'] = CF_encode_json($new_config);
        $data['module_type']    =  $request->input('module_type');

        DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config), 'module_type' => $request->input('module_type')));


        if ($request->ajax() == true) {
            return $response->setMessage('Module Setting Has Been Save Successfull');
        } else {
            return $response->setMessage('Module Setting Has Been Save Successfull');
        }
    }

    function postSavesubform(Request $request, BaseHttpResponse $response)
    {
        $id = $request->get('id');
        $row = DB::table('cruds')->where('id', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds/subform/' . $request->get('module_name'))
                ->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);

        $subform = array(
            'title'            => $request->get('title'),
            'master'        => $request->get('master'),
            'master_key'    => $request->get('master_key'),
            'module'        => $request->get('module'),
            'table'            => $request->get('table'),
            'key'            => $request->get('key'),
            'field_after'     => $request->get('field_after'),
            'is_new_tab'     => $request->get('is_new_tab'),
            'module_relation'     => ($request->get('module_relation')) ? $request->get('module_relation') : 'single',
            'hide_header_label'     => $request->get('hide_header_label'),
            'field_access_type'     => $request->get('field_access_type'),
            'restricted_role_id'     => $request->get('restricted_role_id'),
            'custom_group_key'     => $request->get('custom_group_key'),
            'wrapper_cls'     => $request->get('wrapper_cls'),
            'prevent_delete' => (!is_null($request->input('prevent_delete'))) ? $request->input('prevent_delete') : '',
        );

        if (isset($config["subform"])) unset($config["subform"]);
        $new_config =     array_merge($config, array("subform" => $subform));


        $affected = DB::table('cruds')
            ->where('id', '=', $id)
            ->update(array('module_config' => CF_encode_json($new_config)));

        if ($request->ajax() == true) {
            return $response->setMessage('Subform has beed added Successful');
        } else {
            return redirect('admin/cruds/subform/' . $row->module_name)
                ->with('message', 'Subform has beed added Successful.')->with('status', 'success');
        }
    }

    function getSubformremove(Request $request, $id = 0)
    {
        $row = DB::table('cruds')->where('module_name', $id)
            ->get();
        if (count($row) <= 0) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $config = CF_decode_json($row->module_config);

        unset($config["subform"]);

        $affected = DB::table('cruds')
            ->where('id', '=', $row->id)
            ->update(array('module_config' => CF_encode_json($config)));

        return redirect('admin/cruds/subform/' . $row->module_name)
            ->with('message', 'Subform has beed Removed Successful.')->with('status', 'success');
    }

    function getComboselect(Request $request)
    {
        if ($request->ajax() == true && Auth::check() == true) {
            $param = explode(':', $request->input('filter'));
            $parent = (!is_null($request->input('parent')) ? $request->input('parent') : null);
            $limit = (!is_null($request->input('limit')) ? $request->input('limit') : null);
            $rows =  Crud::getComboselect($param, $limit, $parent);
            $items = array();

            $fields = explode("|", $param[2]);

            foreach ($rows as $row) {
                $value = "";
                foreach ($fields as $item => $val) {
                    if ($val != "") $value .= $row->{$val} . " ";
                }
                $items[] = array($row->{$param['1']}, $value);
            }

            return json_encode($items);
        } else {
            return json_encode(array('OMG' => " Ops .. Cant access the page !"));
        }
    }

    public function getCombotable(Request $request)
    {
        //		if(Request::ajax() == true && Auth::check() == true)
        //		{
        $rows =  Crud::getTableList($this->db);
        $items = array();
        foreach ($rows as $row) {
            $items[] = array($row, $row);
        }
        return json_encode($items);
        //		} else {
        //			return json_encode(array('OMG'=>"  Ops .. Cant access the page !"));
        //		}
    }

    public function getCombotablefield(Request $request)
    {
        if ($request->input('table') == '') return json_encode(array());
        //		if(Request::ajax() == true && Auth::check() == true)
        //		{
        $items = array();
        $table = $request->input('table');
        if ($table != '') {
            $rows =  Crud::getTableField($request->input('table'));
            foreach ($rows as $row)
                if($request->has('isHashMap')) {
                    $items[] = ['id' => $row, 'text' => $row];
                } else {
                    $items[] = array($row, $row);
                }
        }
        return json_encode($items);
        //		} else {
        //			return json_encode(array('OMG'=>"  Ops .. Cant access the page !"));
        //		}
    }

    function postDobuild(Request $request, $id)
    {

        $id = $request->input('id');
        $c = (isset($_POST['controller']) ? 'y' : 'n');
        $m = (isset($_POST['model']) ? 'y' : 'n');
        $g = (isset($_POST['grid']) ? 'y' : 'n');
        $f = (isset($_POST['form']) ? 'y' : 'n');
        $v = (isset($_POST['view']) ? 'y' : 'n');
        $fg = (isset($_POST['frontgrid']) ? 'y' : 'n');
        $fv = (isset($_POST['frontview']) ? 'y' : 'n');
        $ff = (isset($_POST['frontform']) ? 'y' : 'n');

        //return redirect('')
        $url = base_path() . '/platform/plugins/crud/resources/views/module/rebuild/' . $id . "?rebuild=y&c={$c}&m={$m}&g={$g}&f={$f}&v={$v}&fg={$fg}&fv={$fv}&ff={$ff}";

        if (\Request::ajax() == 'ajax') {
            return response()->json(array('status' => 'success', 'url' => url($url)));
        } else {
            return redirect($url);
        }
    }

    public function getRebuild(Request $request, $id = 0)
    {

        $row = DB::table('cruds')->where('id', $id)->get();
        if (count($row) <= 0) {
            return redirect('sximo/module')->with('message', 'Can not find module')->with('status', 'error');
        }
        $row = $row[0];
        $this->data['row'] = $row;
        $config = CF_decode_json($row->module_config);
        $class         = $row->module_name;
        $ctr = ucwords($row->module_name);
        $path         = $row->module_name;
        // build Field entry
        $f = '';
        $req = '';

        // End Build Fi global $codes;
        $codes = array(
            'controller'        => ucwords($class),
            'class'                => $class,
            'fields'            => $f,
            'required'            => $req,
            'table'                => $row->module_db,
            'title'                => $row->module_title,
            'note'                => $row->module_note,
            'key'                => $row->module_db_key,
            'sql_select'                => $config['sql_select'],
            'sql_where'                    => $config['sql_where'],
            'sql_group'                    => $config['sql_group'],
        );
        if (!isset($config['form_layout'])) {
            $config['form_layout'] = array('column' => 1, 'title' => $row->module_title, 'format' => 'grid', 'display' => 'vertical');
        }

        $codes['form_javascript'] = toJavascript($config['forms'], $path, $class);
        $codes['form_entry'] = toForm($config['forms'], $config['form_layout']);
        $codes['form_display'] = (isset($config['form_layout']['display']) ? $config['form_layout']['display'] : 'vertical');
        $codes['form_view'] = toView($config['grid']);
        if ($config['form_layout']['format'] == 'wizzard') {
            $codes['form_wizard'] = createWizard();
        } else {
            $codes['form_wizard'] = '';
        }
        // $codes['form_maps'] = toMaps($config['grid']);

        $codes['masterdetailmodel']  = '';
        $codes['masterdetailinfo']   = '';
        $codes['masterdetailgrid']   = '';
        $codes['masterdetailsave']   = '';
        $codes['masterdetailform']   = '';
        $codes['masterdetailsubform']   = '';
        $codes['masterdetailview']   = '';
        $codes['masterdetailjs']   = '';
        $codes['masterdetaildelete']   = '';

        /* Subform */
        if (isset($config['subform'])) {
            $md = toMasterDetail($config['subform']);
            $codes['masterdetailmodel']  = $md['masterdetailmodel'];
            $codes['masterdetailinfo']   = $md['masterdetailinfo'];
            $codes['masterdetailsave']   = $md['masterdetailsave'];
            $codes['masterdetailsubform']   = $md['masterdetailsubform'];
            $codes['masterdetailform']   = $md['masterdetailform'];
            $codes['masterdetaildelete'] = $md['masterdetaildelete'];
            $codes['masterdetailjs']     = $md['masterdetailjs'];
        }


        /* End Master Detail */
        $dir = base_path() . '/resources/views/' . $class;
        $dirPublic = base_path() . '/resources/views/' . $class . '/public';
        $dirC = app_path() . '/Http/Controllers/';
        $dirApi = app_path() . '/Http/Controllers/Services/';
        $dirM = app_path() . '/Models/';

        if (!is_dir($dir))               mkdir($dir, 0777);
        if (!is_dir($dirPublic))         mkdir($dirPublic, 0777);

        /* find type of module and generate it  */


        $mType = ($row->module_type == 'addon' ? 'native' :  $row->module_type);
        if (is_dir(base_path() . '/resources/views/sximo/module/template/' . $mType)) {
            require_once(base_path() . '/resources/views/sximo/module/template/' . $mType . '/config/config.php');
        } else {

            if ($request->ajax() == true && Auth::check() == true) {
                return response()->json(array('status' => 'success', 'message' => 'Template is Not Exists'));
            } else {
                return redirect('sximo/module')->with('message', 'Template is Not Exists')
                    ->with('status', 'success');
            }
        }
        self::createRouters();

        if ($request->ajax() == true && Auth::check() == true) {
            return response()->json(array('status' => 'success', 'message' => 'Code Script has been replaced successfull'));
        } else {

            return redirect('sximo/module')->with('message', 'Code Script has been replaced successfull')->with('status', 'success');
        }
    }

    function postMultisearch(Request $request)
    {
        $post = $_POST;
        $items = '';
        foreach ($post as $item => $val) :
            if ($_POST[$item] != '' and $item != '_token' and $item != 'md' && $item != 'id') :
                $items .= $item . ':' . trim($val) . '|';
            endif;

        endforeach;

        return Redirect::to($this->module . '?search=' . substr($items, 0, strlen($items) - 1) . '&md=' . $request->inpuy('md'));
    }

    function buildSearch($map = false)
    {

        $keywords = '';
        $fields = '';
        $param = '';
        $allowsearch = $this->info['config']['forms'];
        foreach ($allowsearch as $as) $arr[$as['field']] = $as;
        $mapping = '';
        if ($_GET['search'] != '') {
            $type = explode("|", $_GET['search']);
            if (count($type) >= 1) {
                foreach ($type as $t) {
                    $keys = explode(":", $t);
                    if (in_array($keys[0], array_keys($arr))) :
                        if ($arr[$keys[0]]['type'] == 'select' || $arr[$keys[0]]['type'] == 'radio') {
                            $param .= " AND " . $arr[$keys[0]]['alias'] . "." . $keys[0] . " " . self::searchOperation($keys[1]) . " '" . $keys[2] . "' ";
                            $mapping .= $keys[0] . ' ' . self::searchOperation($keys[1]) . ' ' . $keys[2] . '<br />';
                        } else {
                            $operate = self::searchOperation($keys[1]);
                            if ($operate == 'like') {
                                $param .= " AND " . $arr[$keys[0]]['alias'] . "." . $keys[0] . " REGEXP '" . $keys[2] . "' ";
                                $mapping .= $keys[0] . ' LIKE ' . $keys[2] . '<br />';
                            } else if ($operate == 'is_null') {
                                $param .= " AND " . $arr[$keys[0]]['alias'] . "." . $keys[0] . " IS NULL ";
                                $mapping .= $keys[0] . ' IS NULL <br />';
                            } else if ($operate == 'not_null') {
                                $param .= " AND " . $arr[$keys[0]]['alias'] . "." . $keys[0] . " IS NOT NULL ";
                                $mapping .= $keys[0] . ' IS NOT NULL <br />';
                            } else if ($operate == 'between') {
                                $param .= " AND (" . $arr[$keys[0]]['alias'] . "." . $keys[0] . " BETWEEN '" . $keys[2] . "' AND '" . $keys[3] . "' ) ";
                                $mapping .= $keys[0] . ' BETWEEN ' . $keys[2] . ' - ' . $keys[3] . '<br />';
                            } else {
                                $param .= " AND " . $arr[$keys[0]]['alias'] . "." . $keys[0] . " " . self::searchOperation($keys[1]) . " '" . $keys[2] . "' ";
                                $mapping .= $keys[0] . ' ' . self::searchOperation($keys[1]) . ' ' . $keys[2] . '<br />';
                            }
                        }
                    endif;
                }
            }
        }

        if ($map == true) {
            return $param = array(
                'param'    => $param,
                'maps'    => '
					<div class="infobox infobox-info fade in" style="font-size:10px;">
					  <button data-dismiss="alert" class="close" type="button"> x </button>
					 <b class="text-danger"> Search Result </b> :  <br /> ' . $mapping . '
					</div>
					'
            );
        } else {
            return $param;
        }
    }


    function onSearch($params)
    {
        // Used for extracting URL GET search
        $psearch = explode('|', $params);
        $currentSearch = array();
        foreach ($psearch as $ps) {
            $tosearch = explode(':', $ps);
            if (count($tosearch) >= 2)
                $currentSearch[$tosearch[0]] = $tosearch[2];
        }
        return $currentSearch;
    }

    function searchOperation($operate)
    {
        $val = '';
        switch ($operate) {
            case 'equal':
                $val = '=';
                break;
            case 'bigger_equal':
                $val = '>=';
                break;
            case 'smaller_equal':
                $val = '<=';
                break;
            case 'smaller':
                $val = '<';
                break;
            case 'bigger':
                $val = '>';
                break;
            case 'not_null':
                $val = 'not_null';
                break;

            case 'is_null':
                $val = 'is_null';
                break;

            case 'like':
                $val = 'like';
                break;

            case 'between':
                $val = 'between';
                break;

            default:
                $val = '=';
                break;
        }
        return $val;
    }

    function inputLogs(Request $request, $note = NULL)
    {
        $data = array(
            'module'        => $request->segment(1),
            'action'        => $request->segment(2),
            'user_id'       => Session::get('uid'),
            'ipaddress'     => $request->getClientIp(),
            'reference_user' => Session::get('uid'),
            'reference_id'    => Session::get('uid'),
            'type'          => 'info',
            'request'        => $note
        );
        DB::table('audit_histories')->insert($data);;
    }

    function validateForm($forms = array())
    {
        if (count($forms) <= 0)
            $forms = $this->info['config']['forms'];

        $rules = array();
        foreach ($forms as $form) {

            if ($form['required'] == '0') {
                $form['required'] = '';
            }
            if ($form['required'] != '') {
                if ($form['type'] != 'file') {
                    $rules[$form['field']] = $form['required'];
                } else {

                    if ($form['required'] == 'required') {
                        $validation = 'required';
                        if ($form['option']['upload_type'] == 'image') {
                            $validation .= '|mimes:jpg,jpeg,png,gif,bmp';
                        } else {
                            $validation .= '|mimes:zip,csv,xls,doc,docx,xlsx,pdf,rtf';
                        }

                        if ($form['option']['image_multiple'] != '1') {
                            // IF SINGLE UPLOAD FILE OR IMAGE
                            $rules[$form['field']] = $validation;
                        } else {
                            // IF MULTIPLE UPLOAD FILE OR IMAGE
                            $FilesArray = [];
                            if (isset($_FILES[$form['field']])) {
                                if (count($_FILES[$form['field']]) >= 1) {
                                    $nbr = count($_FILES[$form['field']]) - 1;
                                    foreach (range(0, $nbr) as $index) {
                                        // $imagesArray['images.' . $index] = 'required|image';
                                        $rules[$form['field'] . '.' . $index] = $validation;
                                    }
                                }
                            }
                        }
                    } else {

                        if ($form['option']['upload_type'] == 'image') {
                            $validation = 'mimes:jpg,jpeg,png,gif,bmp';
                        } else {
                            $validation = 'mimes:zip,csv,xls,doc,docx,xlsx';
                        }

                        if ($form['option']['image_multiple'] != '1') {
                            // IF SINGLE UPLOAD FILE OR IMAGE
                            $rules[$form['field']] = $validation;
                        } else {
                            // IF MULTIPLE UPLOAD FILE OR IMAGE
                            $FilesArray = [];
                            if (isset($_FILES[$form['field']])) {
                                if (count($_FILES[$form['field']]) >= 1) {
                                    $nbr = count($_FILES[$form['field']]) - 1;
                                    foreach (range(0, $nbr) as $index) {
                                        // $imagesArray['images.' . $index] = 'required|image';
                                        $rules[$form['field'] . '.' . $index] = $validation;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $rules;
    }

    function validateListError($rules)
    {
        $errMsg = __('core.note_error');
        $errMsg .= '<hr /> <ul>';
        foreach ($rules as $key => $val) {
            $errMsg .= '<li>' . $key . ' : ' . $val[0] . '</li>';
        }
        $errMsg .= '</li>';
        return $errMsg;
    }

    function validatePost($request)
    {
        $str = $this->info['config']['forms'];
        $data = array();
        foreach ($str as $f) {
            $field = $f['field'];
            // Update for V5.1.5 issue on Autofilled createOn and updatedOn fields
            if ($field == 'createdOn') $data['createdOn'] = date('Y-m-d H:i:s');
            if ($field == 'updatedOn') $data['updatedOn'] = date('Y-m-d H:i:s');
            if ($f['view'] == 1) {


                if ($f['type'] == 'textarea_editor' || $f['type'] == 'textarea') {
                    $data[$field] = $request->input($field);
                } else {

                    if (!is_null($request->input($field))) $data[$field] = $request->input($field);
                    // if post is file or image
                    if ($f['type'] == 'file') {

                        if (!is_dir(public_path() . $f['option']['path_to_upload']))
                            mkdir(public_path() . $f['option']['path_to_upload'], 0777);

                        $files = '';
                        if ($f['option']['upload_type'] == 'file') {

                            if (isset($f['option']['image_multiple']) && $f['option']['image_multiple'] == 1) {
                                if (isset($_POST['curr' . $field])) {
                                    $curr =  '';
                                    for ($i = 0; $i < count($_POST['curr' . $field]); $i++) {
                                        $files .= $_POST['curr' . $field][$i] . ',';
                                    }
                                }

                                if (!is_null($request->file($field))) {

                                    $destinationPath = '.' . $f['option']['path_to_upload'];
                                    foreach ($_FILES[$field]['tmp_name'] as $key => $tmp_name) {
                                        $file_name = $_FILES[$field]['name'][$key];
                                        $file_tmp = $_FILES[$field]['tmp_name'][$key];
                                        if ($file_name != '') {
                                            move_uploaded_file($file_tmp, $destinationPath . '/' . $file_name);
                                            $files .= $file_name . ',';
                                        }
                                    }

                                    if ($files != '')    $files = substr($files, 0, strlen($files) - 1);
                                    $data[$field] = $files;
                                } else {
                                    unset($data[$field]);
                                }
                            } else {

                                if (!is_null($request->file($field))) {

                                    // check if folder exists

                                    $file = $request->file($field);
                                    $destinationPath = '.' . $f['option']['path_to_upload'];
                                    $filename = $file->getClientOriginalName();
                                    $extension = $file->getClientOriginalExtension(); //if you need extension of the file
                                    $rand = rand(1000, 100000000);
                                    $newfilename = strtotime(date('Y-m-d H:i:s')) . '-' . $rand . '.' . $extension;
                                    $uploadSuccess = $file->move($destinationPath, $newfilename);
                                    if ($uploadSuccess) {
                                        $data[$field] = $newfilename;
                                    }
                                }
                            }
                        } else {

                            if (!is_dir(public_path() . $f['option']['path_to_upload']))
                                mkdir(public_path() . $f['option']['path_to_upload'], 0777);

                            if (isset($f['option']['image_multiple']) && $f['option']['image_multiple'] == 1) {
                                $files = '';
                                if (isset($_POST['curr' . $field])) {
                                    $curr =  '';
                                    for ($i = 0; $i < count($_POST['curr' . $field]); $i++) {
                                        $files .= $_POST['curr' . $field][$i] . ',';
                                    }
                                }

                                $destinationPath = '.' . $f['option']['path_to_upload'];
                                if (count($request->file($f['field'])) >= 1) {

                                    $destinationPath = '.' . $f['option']['path_to_upload'];
                                    foreach ($_FILES[$field]['tmp_name'] as $key => $tmp_name) {
                                        $file_name = $_FILES[$field]['name'][$key];
                                        $file_tmp = $_FILES[$field]['tmp_name'][$key];
                                        if ($file_name != '') {
                                            //move_uploaded_file($file_tmp,$destinationPath.'/'.$file_name);
                                            $file = $request->file($field)[$key];
                                            $filename = $file->getClientOriginalName();
                                            $extension = $file->getClientOriginalExtension(); //if you need extension of the file
                                            $rand = rand(1000, 100000000);
                                            $newfilename = strtotime(date('Y-m-d H:i:s')) . '-' . $rand . '.' . $extension;
                                            $files .= $newfilename . ',';

                                            $uploadSuccess = $file->move($destinationPath, $newfilename);


                                            if ($f['option']['resize_width'] != '0' && $f['option']['resize_width'] != '') {
                                                if ($f['option']['resize_height'] == 0) {
                                                    $f['option']['resize_height']    = $f['option']['resize_width'];
                                                }
                                                $orgFile = $destinationPath . '/' . $newfilename;
                                                cropImage($f['option']['resize_width'], $f['option']['resize_height'], $orgFile,  $extension,     $orgFile);
                                            }
                                        }
                                    }
                                }
                                if ($files != '')    $files = substr($files, 0, strlen($files) - 1);
                                $data[$field] = $files;
                            } else {

                                if (!is_null($request->file($field))) {
                                    $file = $request->file($field);
                                    $destinationPath = '.' . $f['option']['path_to_upload'];
                                    $filename = $file->getClientOriginalName();
                                    $extension = $file->getClientOriginalExtension(); //if you need extension of the file
                                    $rand = rand(1000, 100000000);
                                    $newfilename = strtotime(date('Y-m-d H:i:s')) . '-' . $rand . '.' . $extension;


                                    $uploadSuccess = $file->move($destinationPath, $newfilename);


                                    if ($f['option']['resize_width'] != '0' && $f['option']['resize_width'] != '') {
                                        if ($f['option']['resize_height'] == 0) {
                                            $f['option']['resize_height']    = $f['option']['resize_width'];
                                        }
                                        $orgFile = $destinationPath . '/' . $newfilename;
                                        cropImage($f['option']['resize_width'], $f['option']['resize_height'], $orgFile,  $extension,     $orgFile);
                                    }

                                    if ($uploadSuccess) {
                                        $data[$field] = $newfilename;
                                    }
                                }
                            }
                        }
                    }

                    // Handle Checkbox input
                    if ($f['type'] == 'checkbox') {
                        if (!is_null($request->{$field})) {
                            $data[$field] = implode(",", $request->input($field));
                        } else {
                            $data[$field] = '0';
                        }
                    }
                    // if post is date
                    if ($f['type'] == 'date') {
                        $data[$field] = date("Y-m-d", strtotime($request->input($field)));
                    }

                    // if post is seelct multiple
                    if ($f['type'] == 'select') {
                        //echo '<pre>'; print_r( $_POST[$field] ); echo '</pre>';
                        if (isset($f['option']['select_multiple']) &&  $f['option']['select_multiple'] == 1) {
                            if (isset($_POST[$field])) {
                                $multival = implode(",", $request->input($field));
                                $data[$field] = $multival;
                            }
                        } else {
                            $data[$field] = $request->input($field);
                        }
                    }
                }
            }
        }
        $this->access =  Crud::validAccess($this->info['id'], session('gid'));
        $global    = (isset($this->access['is_global']) ? $this->access['is_global'] : 0);

        if ($global == 0)
            //$data['entry_by'] = \Session::get('uid');
            /* Added for Compatibility laravel 5.2 */
            $values = array();
        foreach ($data as $key => $val) {
            if ($val != '') $values[$key] = $val;
        }
        return $values;
    }


    function postFilter(Request $request)
    {
        $module = $this->module;
        $sort     = (!is_null($request->input('sort')) ? $request->input('sort') : '');
        $order     = (!is_null($request->input('order')) ? $request->input('order') : '');
        $rows     = (!is_null($request->input('rows')) ? $request->input('rows') : '');
        $md     = (!is_null($request->input('md')) ? $request->input('md') : '');

        $filter = '?';
        if ($sort != '') $filter .= '&sort=' . $sort;
        if ($order != '') $filter .= '&order=' . $order;
        if ($rows != '') $filter .= '&rows=' . $rows;
        if ($md != '') $filter .= '&md=' . $md;



        return Redirect::to($this->data['pageModule'] . $filter);
    }

    function injectPaginate()
    {

        $sort     = (isset($_GET['sort'])     ? $_GET['sort'] : '');
        $order     = (isset($_GET['order'])     ? $_GET['order'] : '');
        $rows     = (isset($_GET['rows'])     ? $_GET['rows'] : '');
        $search = (isset($_GET['search']) ? $_GET['search'] : '');
        $s         = (isset($_GET['s']) ? $_GET['s'] : '');

        $appends = array();
        if ($sort != '')     $appends['sort'] = $sort;
        if ($order != '')     $appends['order'] = $order;
        if ($rows != '')     $appends['rows'] = $rows;
        if ($search != '') $appends['search'] = $search;
        if ($s != '') $appends['s'] = $s;

        return $appends;
    }

    function returnUrl()
    {
        $pages     = (isset($_GET['page']) ? $_GET['page'] : '');
        $sort     = (isset($_GET['sort']) ? $_GET['sort'] : '');
        $order     = (isset($_GET['order']) ? $_GET['order'] : '');
        $rows     = (isset($_GET['rows']) ? $_GET['rows'] : '');
        $search     = (isset($_GET['search']) ? $_GET['search'] : '');

        $appends = array();
        if ($pages != '')     $appends['page'] = $pages;
        if ($sort != '')     $appends['sort'] = $sort;
        if ($order != '')     $appends['order'] = $order;
        if ($rows != '')     $appends['rows'] = $rows;
        if ($search != '') $appends['search'] = $search;

        $url = "";
        foreach ($appends as $key => $val) {
            $url .= "&$key=$val";
        }
        return $url;
    }

    public function getRemovecurrentfiles(Request $request)
    {
        $id     = $request->input('id');
        $field     = $request->input('field');
        $file     = $request->input('file');
        if (file_exists('./' . $file) && $file != '') {
            if (unlink('.' . $file)) {
                DB::table($this->info['table'])->where($this->info['key'], $id)->update(array($field => ''));
            }
            return response()->json(array('status' => 'success'));
        } else {
            return response()->json(array('status' => 'error'));
        }
    }

    public function getRemovefiles($request)
    {
        $files = '.' . $request->input('file');
        if (file_exists($files) && $files != '') {
            unlink($files);
        }
        return response()->json(array('status' => 'success'));
    }


    public function getSearch($mode = 'native')
    {
        if (isset($_GET['type']))
            $mode = $_GET['type'];

        $this->data['tableForm']     = $this->info['config']['forms'];
        $this->data['tableGrid']     = $this->info['config']['grid'];
        $this->data['searchMode']     = $mode;
        $this->data['pageUrl']        = url($this->module);
        return view('sximo.module.utility.search', $this->data);
    }

    function getImport(Request $request)
    {
        $task = $request->input('template');
        if ($task != '') {
            $fields =  $this->info['config']['grid'];
            $output = fopen('php://output', 'w');
            $head = array();
            foreach ($fields as $f) {
                $head[] = $f['field'];
            }

            fputcsv($output, $head);
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=' . $this->module . '.csv');
        } else {
            $this->data['url'] = url($this->module);
            $this->data['module'] = $this->module;
            return view('sximo.module.utility.import', $this->data);
        }
    }

    function postImport($request)
    {

        if (!is_null($request->file('fileimport'))) {
            $file =     $request->file('fileimport');
            $filename = $file->getClientOriginalName();
            $uploadSuccess = $file->move('./uploads/', $filename);
            if ($uploadSuccess) {
                $csv = array_map('str_getcsv', file('./uploads/' . $filename));
                $table = $this->info['config']['grid'];
                $fields = array();
                foreach ($table as $f) {
                    $fields[] = $f['field'];
                }
                //print_r($fields);
                foreach ($csv as $row) {
                    $data = array();
                    foreach ($fields as $key => $val) {
                        if ($key != 0)
                            $data[$val] = (isset($row[$key]) ? $row[$key] : '');
                    }
                    //print_r($data);
                    //echo $row[0];
                    Crud::insertRow($data, $row[0]);
                }

                return response()->json(array('status'    => 'success', 'message' => 'Csv Imported Successful !'));
            } else {
                return response()->json(array('status'    => 'error', 'message' => 'Upload Failed!'));
            }
        } else {
            return response()->json(array('status'    => 'error', 'message' => 'Please select file to Upload!'));
        }
    }

    public function getExpotion()
    {
        $this->data['pageUrl'] = url($this->data['pageModule']);
        return view('sximo.module.utility.export', $this->data);
    }
    public function getExport(Request $request, $t = 'excel')
    {
        if ($request->has('filter'))
            return $this->getExpotion();

        $t = $request->input('do');
        $this->access =  Crud::validAccess($this->info['id'], session('gid'));

        if (isset($this->access['is_' . $t])) {
            if ($this->access['is_' . $t] == 0)
                return redirect('dashboard')->with('message', __('core.note_restric'))->with('status', 'error');
        } else {
            return redirect('dashboard')->with('message', __('core.note_restric'))->with('status', 'error');
        }

        $info         =  Crud::makeInfo($this->module);
        $filter = '';
        if (!is_null($request->input('search'))) {
            $search =     $this->buildSearch('maps');
            $filter = $search['param'];
            $this->data['search_map'] = $search['maps'];
        }

        $params     = array(
            'params'    => $filter,
            'fstart'    => $request->input('fstart'),
            'flimit'    => $request->input('flimit')
        );

        $results     =  Crud::getRows($params);
        $fields        = $info['config']['grid'];
        $rows        = $results['rows'];
        $content     = array(
            'fields' => $fields,
            'rows' => $rows,
            'title' => $this->data['pageTitle'],
        );

        if ($t == 'word') {
            return view('sximo.module.utility.word', $content);
        } else if ($t == 'pdf') {

            $pdf = PDF::loadView('sximo.module.utility.pdf', $content)->setPaper('a4', 'landscape');
            return $pdf->download('invoice.pdf');
        } else if ($t == 'csv') {

            return view('sximo.module.utility.csv', $content);
        } else if ($t == 'print') {

            //return view('sximo.module.utility.print',$content);
            $data['html'] = view('sximo.module.utility.print', $content)->render();
            return view('layouts.blank', $data);
        } else {

            return view('sximo.module.utility.excel', $content);
        }
    }

    function getLookup($request)
    {
        $id = $request->input('param');
        $args = explode("-", $id);
        if (count($args) >= 2) {

            $model = '\\App\\Models\\' . ucwords($args['3']);
            $model = new $model();
            $info = $model->makeInfo($args['3']);
            $data['pageTitle'] = $info['title'];
            $data['pageNote'] = $info['note'];
            $params = array(
                'params'    => " And " . $args['4'] . "." . $args['5'] . " ='" . $args['6'] . "'",
                //'global'	=> (isset($this->access['is_global']) ? $this->access['is_global'] : 0 )
            );
            $results = $model->getRows($params);
            $data['access']        = $model->validAccess($info['id']);
            $data['rowData']        = $results['rows'];
            $data['tableGrid']     = $info['config']['grid'];
            $data['tableForm']     = $info['config']['forms'];
            $data['colspan']        = $this->viewColSpan($info['config']['grid']);
            $data['nested_subgrid']    = (isset($info['config']['subgrid']) ? $info['config']['subgrid'] : array());
            $data['id']         = $args[6];
            $data['key']        = $info['key'];
            //$data['ids']		= 'md'-$info['id'];
            return view('sximo.module.utility.masterdetail', $data);
        } else {
            return 'Invalid Argument';
        }
    }


    function detailview($model, $detail, $id)
    {

        $info = $model->makeInfo($detail['module']);
        $params = array(
            'params'    => " And `" . $detail['key'] . "` ='" . $id . "'",
            'global'    => (isset($this->access['is_global']) ? $this->access['is_global'] : 0)
        );
        $results = $model->getRows($params);
        $data['rowData']        = $results['rows'];
        $data['tableGrid']         = $info['config']['grid'];
        $data['tableForm']         = $info['config']['forms'];

        return $data;
    }


    function detailviewsave($model, $request, $detail, $id)

    {
        //DB::table($detail['table'])->where($detail['key'],$request[$detail['key']])->delete();

        $info = $model->makeInfo($detail['module']);
        $relation_key = $info['key'];
        $access = $model->validAccess($info['id'], session('gid'));

        if ($access['is_add'] == '1' && $access['is_edit'] == '1') {
            $str = $info['config']['forms'];
            $global    = (isset($access['is_global']) ? $access['is_global'] : 0);
            $total = count($request['counter']);
            $mkeys = array();
            $getAllCurrentData = DB::table($detail['table'])->where($detail['master_key'], $id)->get();

            $pkeys = array();
            for ($i = 0; $i < $total; $i++)
                $pkeys[] = $request['bulk_' . $relation_key][$i];

            foreach ($getAllCurrentData as $keys) {
                if (!in_array($keys->{$relation_key}, $pkeys)) {
                    // Remove If items is not resubmited
                    DB::table($detail['table'])->where($relation_key, $keys->{$relation_key})->delete();
                }
            }

            for ($i = 0; $i < $total; $i++) {
                $data = array();
                foreach ($str as $f) {
                    $field = $f['field'];
                    if ($f['view'] == 1) {
                        if (isset($request['bulk_' . $field][$i]) && $request['bulk_' . $field][$i] != '') {
                            $data[$f['field']] = $request['bulk_' . $field][$i];
                        }
                    }
                }

                $rules = self::validateForm($str);
                $validator = Validator::make($data, $rules);
                if ($validator->passes()) {
                    $data[$detail['key']] =  $id;
                    if ($global == 0)
                        //$data['entry_by'] = \Session::get('uid');

                        // Check if data currentry exist
                        $check = DB::table($detail['table'])->where($relation_key, $request['bulk_' . $relation_key][$i])->get();
                    if (count($check) >= 1) {
                        DB::table($detail['table'])->where($relation_key,  $request['bulk_' . $relation_key][$i])->update($data);
                    } else {
                        unset($data[$relation_key]);
                        DB::table($detail['table'])->insert($data);
                    }
                }
            }
        }
    }

    public function liveSearch($request)
    {
        $query = '';
        $forms = $this->info['config']['forms'];
        $keyword = trim($request->input('s'));
        if (!is_null($request->input('search')))
            $keyword = trim($request->input('search')['value']);

        $i = 0;
        foreach ($forms as $form) {
            if ($form['search'] == '1') {
                if ($i == 0) {
                    $query .= " AND ( " . $form['alias'] . "." . $form['field'] . " REGEXP '" . $keyword . "'  ";
                } else {
                    $query .= " OR  " . $form['alias'] . "." . $form['field'] . " REGEXP '" . $keyword . "'  ";
                }
                $i++;
            }
        }
        if ($query != '') {
            $query .= ' ) ';
        }
        return $query;
    }

    public function grab($request, $args = array())
    {

        if (isset($request->get('profile')->group_id)) {
            $this->access = $this->access($this->info['id'], $request->get('profile')->group_id);
        } else {
            $this->access = $this->access($this->info['id']);
        }


        $sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']);
        $order = (!is_null($request->input('order')) ? $request->input('order') :  $this->info['setting']['ordertype']);
        // End Filter sort and order for query
        // Filter Search for query
        $filter = '';
        $filter .= (isset($args['params']) ? $args['params'] : '');
        if (!is_null($request->input('search'))) {
            $search =     $this->buildSearch('maps');
            $filter = $search['param'];
            $this->data['search_map'] = $search['maps'];
        }
        if (!is_null($request->input('s'))) {
            $filter .= $this->liveSearch($request);
        }


        $page = $request->input('page', 1);
        $params = array(
            'page'        => $page,
            'limit'        => (!is_null($request->input('rows')) ? filter_var($request->input('rows'), FILTER_VALIDATE_INT) : static::$per_page),
            'sort'        => $sort,
            'order'        => $order,
            'params'    => $filter,
            'global'    => (isset($this->access['is_global']) ? $this->access['is_global'] : 0)
        );
        // Get Query
        $results =  Crud::getRows($params, session('uid'));

        // Build pagination setting
        $pagination = new Paginator($results['rows'], $results['total'], $params['limit']);
        if ($this->info['type'] == 'ajax') {
            $pagination->setPath($this->module . '/data');
        } else {
            $pagination->setPath($this->module);
        }
        $this->data['param']        = $params;
        $this->data['rowData']        = $results['rows'];
        // Build Pagination
        $this->data['pagination']    = $pagination;
        // Build pager number and append current param GET
        $this->data['pager']         = $this->injectPaginate();
        // Row grid Number
        $this->data['i']            = ($page * $params['limit']) - $params['limit'];
        // Grid Configuration
        usort($this->info['config']['grid'], "self::_sort");
        $this->data['tableGrid']     = $this->info['config']['grid'];
        $this->data['tableForm']     = $this->info['config']['forms'];
        $this->data['colspan']         = $this->viewColSpan($this->info['config']['grid']);
        // Group users permission
        $this->data['access']        = $this->access;
        // Detail from master if any
        $this->data['fields'] =  $this->fieldLang($this->info['config']['grid']);
        // Master detail link if any
        $this->data['subgrid']    = (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
        $this->data['setting']         = $this->info['setting'];

        $this->data['insort']    = $sort;
        $this->data['inorder']    = $order;

        return  $this->data;
    }
    public function _sort($a, $b)
    {

        if ($a['sortlist'] == $a['sortlist']) {
            return strnatcmp($a['sortlist'], $b['sortlist']);
        }
        return strnatcmp($a['sortlist'], $b['sortlist']);
    }
    public function viewColSpan($grid)
    {
        $i = 0;
        foreach ($grid as $t) :
            if ($t['view'] == '1') ++$i;
        endforeach;
        return $i;
    }
    public function grabApi($request, $args = array())
    {

        if (isset($request->get('profile')->group_id)) {
            $this->access = $this->access($this->info['id'], $request->get('profile')->group_id);
        } else {
            $this->access = $this->access($this->info['id']);
        }


        $sort = (!is_null($request->input('sort')) ? $request->input('sort') : $this->info['setting']['orderby']);
        $order = (!is_null($request->input('order')) ? $request->input('order') :  $this->info['setting']['ordertype']);
        // End Filter sort and order for query
        // Filter Search for query
        $filter = '';
        $filter .= (isset($args['params']) ? $args['params'] : '');
        if (!is_null($request->input('search'))) {
            $search =     $this->buildSearch('maps');
            $filter = $search['param'];
            $this->data['search_map'] = $search['maps'];
        }
        if (!is_null($request->input('s'))) {
            $filter .= $this->liveSearch($request);
        }


        $page = $request->input('page', 1);
        $params = array(
            'page'        => $page,
            'limit'        => (!is_null($request->input('rows')) ? filter_var($request->input('rows'), FILTER_VALIDATE_INT) : static::$per_page),
            'sort'        => $sort,
            'order'        => $order,
            'params'    => $filter,
            'global'    => (isset($this->access['is_global']) ? $this->access['is_global'] : 0)
        );
        // Get Query
        $results =  Crud::getRows($params, session('uid'));
        // Build pagination setting
        $pagination = new Paginator($results['rows'], $results['total'], $params['limit']);
        $this->data['pagination']    = $pagination;

        $this->data['rowData']        = $results['rows'];

        return  $this->data;
    }

    public function hook($request, $id = null)
    {
        $this->access = $this->access($this->info['id']);
        $row =  Crud::getRow($id);
        if ($row) {
            $this->data['row'] =  $row;
            $this->data['fields']         =  $this->fieldLang($this->info['config']['grid']);
            $this->data['id'] = $id;
            $this->data['access']        = $this->access;
            $this->data['subgrid']    = (isset($this->info['config']['subgrid']) ? $this->info['config']['subgrid'] : array());
            $this->data['fields'] =  $this->fieldLang($this->info['config']['grid']);
            $this->data['prevnext'] =  Crud::prevNext($id);
            $this->data['setting']         = $this->info['setting'];
        }
        return $this->data;
    }

    public function fieldLang($fields)
    {
        $l = array();
        foreach ($fields as $fs) {
            foreach ($fs as $f)
                $l[$fs['field']] = $fs;
        }
        return $l;
    }

    function copy($request)
    {
        foreach (DB::select("SHOW COLUMNS FROM " .  $this->info['table']) as $column) {
            if ($column->Field != $this->info['key'])
                $columns[] = $column->Field;
        }

        if (count($request->input('ids')) >= 1) {
            $toCopy = implode(",", $request->input('ids'));
            $sql = "INSERT INTO " .  $this->info['table'] . " (" . implode(",", $columns) . ") ";
            $sql .= " SELECT " . implode(",", $columns) . " FROM " . $this->info['table'] . " WHERE " . $this->info['key'] . " IN (" . $toCopy . ")";
            DB::select($sql);
            return ['message' => __('core.note_success'), 'status' => 'success'];
        } else {
            return ['message' => __('Please select row to copy'), 'status' => 'error'];
        }
    }

    function access($id, $gid = null)
    {
        return true;
    }
    function postPackage(Request $request)
    {
        $id = $request->input('id');
        if (count($id) < 1) {
            return redirect('admin/cruds')->with('message', 'Can not find module')->with('status', 'error');
        };


        $_id = array();
        foreach ($id as $k => $v) {
            if (!is_numeric($v)) continue;
            $_id[] = $v;
        }

        $ids = implode(',', $_id);

        $sql = "
            SELECT * FROM cruds
            WHERE id IN (" . $ids . ")
            ORDER by id
            ";

        $rows = DB::select($sql);

        $this->data['zip_content'] = array();
        $app_info = array();
        $inc_tables = array();

        foreach ($rows as $k => $row) {

            $zip_content[] = array(
                'id'   =>  $row->id,
                'module_name' =>  $row->module_name,
                'module_db'   =>  $row->module_db,
                'module_type' =>  $row->module_type,
            );
        }

        // encrypt info
        $this->data['enc_module'] = base64_encode(serialize($zip_content));
        $this->data['enc_id'] = base64_encode(serialize($id));

        // module info
        $this->data['zip_content'] = $zip_content;

        /* CHANGE START HERE */
        $app_path = base_path();

        // file helper list
        $_path_inc = array('app/Library', 'resources/lang/en');

        foreach ($_path_inc as $path) {
            $file_inc[$path]  = scandir($app_path . '/' . $path);
            foreach ($file_inc[$path] as $k => $v) {
                if ($v == '.' || $v == '..') unset($file_inc[$path][$k]);
                if (!preg_match('/.php$/i', $v)) unset($file_inc[$path][$k]);
            }
        }


        $this->data['file_inc'] = $file_inc;

        /* CHANGE END HERE */
        // echo '<pre>';print_r($this->data); echo '</pre>'; exit;
        return view('crud/package', $this->data);
    }


    function postDopackage(Request $request)
    {

        // app name
        $app_name     = $request->input('app_name');

        // encrypt info
        $enc_module   = $request->input('enc_module');
        $enc_id       = $request->input('enc_id');

        // query command || file
        $sql_cmd      = $request->input('sql_cmd');

        if (!($_FILES['sql_cmd_upload']['error'])) {
            $sql_path     = $request->file('sql_cmd_upload')->getrealpath();
            if ($sql_content = file_get_contents($sql_path)) {
                $sql_cmd = $sql_content;
            }
        }

        /* CHANGE START */

        // file to include
        $file_library = $request->input('file_library');
        $file_lang    = $request->input('file_lang');

        /* CHANGE END */

        // create app name
        $tapp_code    = preg_replace('/([s[:punct:]]+)/', ' ', $app_name);
        $app_code     = str_replace(' ', '_', trim($tapp_code));

        $id    = unserialize(base64_decode($enc_id));
        $modules      = unserialize(base64_decode($enc_module));
        $c_module_id  = implode(',', $id);

        $zip_file = "./uploads/zip/{$app_code}.zip";

        $cf_zip = new \ZipHelpers;

        $app_path = app_path();

        $cf_zip->add_data(".mysql", $sql_cmd);

        // App ID Name
        $ain = $id;
        $cf_zip->add_data(".ain", base64_encode(serialize($ain)));

        // setting
        $sql = " select * from cruds where id in ( {$c_module_id} )";

        $_modules = DB::select($sql);

        foreach ($_modules as $n => $_module) {
            $_modules[$n]->id = '';
        }

        $setting['cruds'] = $_modules;

        $cf_zip->add_data(".setting", base64_encode(serialize($setting)));

        unset($_module);

        foreach ($_modules as $n => $_module) {
            $file = $_module->module_name;
            $cf_zip->add_data(
                "app/Http/Controllers/" . ucwords($file) . "Controller.php",
                file_get_contents($app_path . "/Http/Controllers/" . ucwords($file) . "Controller.php")
            );
            $cf_zip->add_data("app/Models/" . ucwords($file) . ".php", file_get_contents($app_path . "/Models/" . ucwords($file) . ".php"));
            $cf_zip->get_files_from_folder("../resources/views/{$file}/", "resources/views/{$file}/");
        }

        // CHANGE START

        // push library files
        if (!empty($file_library)) {
            foreach ($file_library as $k => $file) {
                $cf_zip->add_data(
                    "app/Library/" . $file,
                    file_get_contents($app_path . "/Library/" . $file)
                );
            }
        }

        // push language files

        if (!empty($file_lang)) {
            $lang_path = scandir(base_path() . '/resources/lang/');
            foreach ($lang_path as $k => $path) {
                if ($path == '.' || $path == '..') continue;
                if (is_file($app_path . '/' . $path)) continue;

                foreach ($file_lang as $k => $file) {
                    $cf_zip->add_data(
                        'resources/lang/' . $path . '/' . $file,
                        file_get_contents(base_path() . "/resources/lang/" . $path . '/' . $file)
                    );
                }
            }
            $this->data['lang_path'] = $lang_path;
        }


        // CHANGE END

        $_zip = $cf_zip->archive($zip_file);

        $cf_zip->clear_data();

        $this->data['download_link'] = link_to("uploads/zip/{$app_name}.zip", "download here", array('target' => '_new'));

        $this->data['module_title'] = "ZIP Packager";
        $this->data['app_name'] = $app_name;

        return redirect('sximo/module')
            ->with('message', ' Module(s) zipped successful ! ')->with('status', 'success');
    }

    function getTrainingTitle(Request $request, BaseHttpResponse $response) {
        return app(CrudInterface::class)->getTrainingTitleLists(0)->get()->toArray();
    }

    /**
    * @param Request $request
    * @param BaseHttpResponse $response
    * @return BaseHttpResponse
    * @throws Throwable
    */
    public function getTrainingTitleList(Request $request, BaseHttpResponse $response)
    {
        $limit = (int)$request->input('paginate', 8);
        $institutions = app(CrudInterface::class)->getTrainingTitleLists($limit);
        return $response
            ->setData(view('plugins/crud::training-title-list', compact('training-title'))->render());
    }

     /**
     * {@inheritDoc}
     */
    public static function getBulkChanges($module, $isFilter = false, $isSubscribe = false): array
    {
        $row = \DB::table('cruds')->where('module_name', $module)->get()->first();
        $moduleConfig = CF_decode_json($row->module_config);
        $columns = [];
        $bulkChangesType = ['text', 'select', 'text_datetime', 'select-search', 'number', 'date', 'text_date', 'textarea', 'radio', 'hidden'];
        $gridConfig = [];$excludeColumns = ['id'];
        if($module == 'student') {
            $subForms = \DB::table('cruds')->where('parent_id', $row->id)->get();
        }
        
        foreach($moduleConfig['grid'] as $grid)
            $gridConfig[$grid['field']] = $grid;
        
            $columns = SELF::getBulkChangesColumn($moduleConfig,$gridConfig,$bulkChangesType,$excludeColumns,$isFilter);
            if($module == 'student' && $subForms){
                $excludeColumns = array_merge($excludeColumns,['created_at','updated_at']);
                foreach($subForms as $subForm){
                    $subModuleConfig = CF_decode_json($subForm->module_config);
                    if($isFilter && $subForm->module_name == 'academic-info'){
                        $subColumns = SELF::getBulkChangesColumn($subModuleConfig,$gridConfig,$bulkChangesType,$excludeColumns,$isFilter);
                        $columns =array_merge($columns,$subColumns);
                    }
                }
            }

            if($isFilter) {
                if(in_array($row->module_db, config('plugins.crud.general.entity_filter_supported_models'))) {
                    $roles = app(\Impiger\ACL\Roles::class)->getChildRolesUsingLogin(false, true, true);
                    $roles = ($roles) ? $roles->pluck('name', 'id') : [];

                    if(request()->getHost() === \Multidomain::getDefaultDomainHost()) {
                        $customColumns[$row->module_db.'.entity_type'] = [
                            'title'    => 'Entity Type',
                            'type'     => 'select',
                            'choices'  => CrudController::getSelectBoxChoices(['field' => 'entity_type'])
                        ];
    
                        $customColumns[$row->module_db.'.entity_id'] = [
                            'title'    => 'Entity',
                            'type'     => 'select',
                            "callback" => "getEntityFilter"
                        ];
                    }

                    $customColumns[$row->module_db.'.role'] = [
                        'title'    => 'Role',
                        'type'     => 'select',
                        'choices' => $roles
                    ];

                    $columns = $customColumns + $columns;
                }

                if ($isSubscribe) {
                    $user = Auth::user();
                    $subscritionModule = $module;
                    if(in_array($module, config('plugins.crud.general.tp_subscription_module'))) {
                        $subscritionModule = 'training-program';
                    }
                    if ($user && $user->applyDataLevelSecurity() && $user->hasPermission($subscritionModule.'.subscribe')) {
                        $customColumns['my_subscribtion'] = [
                            'title'    => 'My Subscription',
                            'type'     => 'select',
                            'choices'  => [1 => 'Yes', 0 => 'No']
                        ];

                        $columns = $customColumns + $columns;
                    }
                }
            }

        return $columns;
    }

    public static function getBulkChangesColumn($moduleConfig,$gridConfig,$bulkChangesType,$excludeColumns = null,$isFilter=false){
        $columns=[];
        foreach ($moduleConfig['forms'] as $val) {
            if(in_array($val['field'], ['entity_id','entity_type','entity']) && request()->getHost() !== \Multidomain::getDefaultDomainHost()) {
                continue;
            }
            $gConfig = (isset($gridConfig[$val['field']])) ? $gridConfig[$val['field']] : [];
            $isAllowedType = (Arr::get($gConfig, 'view') && in_array($val['type'], $bulkChangesType)) ? true : false;
            if (
                (!$isFilter && $isAllowedType && Arr::get($val, 'bulk_edit')) || 
                ($isFilter && $val['search'] && $isAllowedType)
             ) {


                $config = [
                    'title' => $val['label'],
                    'type' => $val['type'],
                    'validate' => $val['required'],
                    'meta' => $val['option'] 
                ];
                
                if($val['field']=='is_enabled')
                {
                    $val['type']='radio';
                    $val['option']["lookup_query"] = "1:Active|0:In-Active";
                    $val['option']["opt_type"] = "datalist";
                }

                if (in_array($val['type'], ['select','radio','entity']) || $val['field'] == 'entity_type') {
                   if($val['field'] == 'entity_id') {
                    $config['callback'] = 'getEntityFilter';
                   } else {
                       if(Arr::get($val, 'option.opt_type') == 'customFunction') {
                        $config['type'] = 'select';
                       }
                    $config['choices'] = CrudController::getSelectBoxChoices($val);
                   }
                    $config['type'] = 'select';
                } else if ($val['type'] == 'text_datetime' || $val['type'] == 'text_date') {
                    $config['type'] = 'date';
                }
                if($config['type'] == 'select'){
                    $config['type'] == 'select-search';
                }
                if(!empty($excludeColumns)){
                    if(!in_array($val['field'],$excludeColumns))
                            $columns[$val['alias'] . "." . $val['field']] = $config;
                }else{
                    $columns[$val['alias'] . "." . $val['field']] = $config;
                }
            }
        }
        
        return $columns;
    }

    public static function getSelectBoxChoices($val, $optionType = null, $lookupTable = null, $lookupValue = null, $lookupKey = null, $whereCondn = null, $applyAcademicYearCndn = false, $groupBy = [])
    {
        $optionType = ($optionType) ? $optionType : Arr::get($val, 'option.opt_type');
        $choices = [];

        if ($optionType == 'customFunction') {
            return call_user_func(array('SELF', Arr::get($val, 'option.custom_function')));
        }
        else if ($optionType == 'datalist') {
            $lookupValues = Arr::get($val, 'option.lookup_query');
            $optionList = explode("|", $lookupValues);

            foreach ($optionList as $opt) {
                list($key, $value) = explode(":", $opt);
                $choices[$key] = $value;
            }
        } else if (in_array($optionType, ['external','entity']) || $val['field'] == 'entity_type') {
            $lookupTable = ($lookupTable) ? $lookupTable : Arr::get($val, 'option.lookup_table');
            $lookupValue = ($lookupValue) ? $lookupValue : Arr::get($val, 'option.lookup_value');
            $lookupKey = ($lookupKey) ? $lookupKey :  Arr::get($val, 'option.lookup_key');
            $whereCondn = ($whereCondn) ? $whereCondn :Arr::get($val, 'option.where_cndn');
            $groupBy = ($groupBy) ? $groupBy :Arr::get($val, 'option.group_by');

            if(Arr::get($val, 'field') == 'entity_type') {
                $lookupTable = 'cruds';
                $lookupValue = 'module_title';
                $lookupKey = 'id';
                $whereCondn = "is_entity = 1";
            } elseif($optionType == 'entity' && Arr::get($val, 'option.specific_entity_type')) {
                $lookupTable = Crud::where('is_entity', 1)-> where('module_name',Arr::get($val, 'option.specific_entity_type'))->pluck('module_db')->first();
                $lookupValue = 'name';
                $lookupKey = 'id';
            }
            
            if($optionType == 'entity' && Arr::get($val,'option.specific_entity_type')){
                $specificEntity = $val['option']['specific_entity_type'];
                $entityDetail = Crud::where('is_entity', 1)->select(['id', 'module_db'])->where('module_name', $specificEntity)->first();
                if (!$entityDetail) {
                    return [];
                }
                 $lookupTable = $entityDetail->module_db;
                 $lookupKey = ($lookupKey) ? $lookupKey : 'id';
                 $lookupValue = 'name';
            }

            if (!$lookupTable || !$lookupKey || !$lookupValue) {
                return $choices;
            }
            $concatRequired = str_contains($lookupValue, "|");
            $fields = ($concatRequired) ? DB::raw("CONCAT_WS(' '," . str_replace('|', ',', $lookupValue) . ") AS displayCusText") : $lookupTable.".".$lookupValue . " AS displayCusText";
            if ($lookupTable == 'training_program_intakes' && str_contains($lookupValue,'month')) {
                $fields = DB::raw('MONTHNAME(STR_TO_DATE(' . $lookupValue . ', "%m")) as displayCusText');
                
            }
            $query = DB::table($lookupTable)->select($fields, $lookupKey);
            if ($lookupTable == 'financial_year' && !$applyAcademicYearCndn) {
                $query = $query->orderBy('is_running','desc');
                $whereCondn = "";
            }
            $query = ($whereCondn) ? $query->whereRaw($whereCondn) : $query;
            if ($lookupTable == 'multidomains') {
                $query = $query->whereRaw('name !="'.env('DEFAULT_DOMAIN').'"');
            }
            if(in_array($lookupTable,['users','impiger_users'])) {
                $dependentKey = ($lookupTable == 'users') ? 'id' : 'user_id';
                $query->join('role_users', 'role_users.user_id', $lookupTable . '.'.$dependentKey)
                        ->join('roles', 'roles.id', '=', 'role_users.role_id');
            }
            $query = SELF::applyStudentAcademicFilter($query, $lookupTable);
            $rawCondition = get_common_condition($lookupTable);
            if(!empty($rawCondition)){
                $query = $query->whereRaw($rawCondition);
            }
            $instituteSecurity = Arr::get($val, 'option.specific_entity_type') ?:$lookupTable;
            $query = SELF::applyInstituteSecurityCondition($instituteSecurity, $query, $lookupTable);
            $model = get_model_from_table($lookupTable);
           
            if ($model) {
                $model = new $model();
                $query = apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, [$fields, $lookupTable . "." . $lookupKey], false);
            }
            if (request()->input('filter_columns')) {
                $requestFilters = getRequestFilters(true);
                $dependency = Arr::get($val, 'option.lookup_dependency_key');
                
                if ($dependency) {
                    $cols = explode("|",$dependency);
                    if(isValidArray($cols) && count($cols) > 1) {
                        $depedentFilterKey = $cols[1];
                        $depedentFilterVal = Arr::get($requestFilters, $depedentFilterKey);

                        if($depedentFilterKey && $depedentFilterVal) {
                            if($lookupTable == 'course_units' && $depedentFilterKey == 'exam_name') {
                                $query = $query->leftJoin('exam_schedule AS ES', $lookupTable.'.id', '=', 'ES.course_unit');
                                $query = (Arr::get($requestFilters, 'term')) ? $query->where('ES.term', Arr::get($requestFilters, 'term')) : $query;
                            }

                            $query = $query->where($depedentFilterKey, $depedentFilterVal);
                        }
                    }
                }

                if(in_array($lookupTable, ['departments', 'training_program'])) {
                    $query = SELF::applyTrainingProgramSecurityCondition($lookupTable, $query);
                    if($lookupTable == 'training_program') {
                        $query = (Arr::get($requestFilters, 'institute_id')) ? $query->where('institute_id', Arr::get($requestFilters, 'institute_id')) : $query;
                        $query = (Arr::get($requestFilters, 'department_id')) ? $query->where('department_id', Arr::get($requestFilters, 'department_id')) : $query;
                    }
                }
            }
            if($groupBy) {
                $lookupKey=$lookupValue;
                $query->groupBy($groupBy);
            }
            if($lookupKey == $lookupValue) {
                $lookupKey = 'displayCusText';
            }
            $query->having('displayCusText', "!=", "");
            $choices = $query->pluck("displayCusText", $lookupKey)->toArray();
        }
        // $choices = (isValidArray($choices)) ? ["" => "Select Option"] + $choices : $choices;
        return $choices;
    }

    public static function applyStudentAcademicFilter($query, $lookupTable) {
        $intakeDetails = ['training_program_intakes','training_program_intake_semester_mapping'];
        if (in_array($lookupTable,$intakeDetails)) {
            $user = Auth::user();
            $userRoles = ($user) ? $user->roles : null;
            $roleSlugs = ($userRoles) ? $userRoles->pluck('slug')->toArray() : [];
            

            if (in_array(STUDENT_ROLE_SLUG, $roleSlugs)) {
                $studentProfileId = storeAndRetrieveStudentProfileId($user);
                $academicDetail = \Impiger\Student\Models\AcademicInfo::where(['imp_student_id' => $studentProfileId, 'status' => STUDENT_JOINED_STATUS])->whereNull('academic_status')->first();
                if($academicDetail && $academicDetail->training_program_id) {
                    $cndn = [
                        $lookupTable . '.training_program_id' => $academicDetail->training_program_id,
                    ];
                    if($lookupTable == 'training_program_intake_semester_mapping') {
                        $cndn[$lookupTable . '.intake_id'] = $academicDetail->intake_id;
                    }
                    $query = $query->where($cndn);
                }
            }
        }
        return $query;
    }

    public static function applyInstituteSecurityCondition($specificEntity,$query, $table = "") {
        if ($specificEntity == 'institution') {
            $instituteIds = SELF::getInstituteIdsByLogin();
            if(Arr::has($instituteIds, 0)) {
                $query = ($table) ?  $query->whereIn($table.'.id', $instituteIds) : $query->whereIn('id', $instituteIds);
            }
        }

        return $query;
    }

    public static function applyTrainingProgramSecurityCondition($table,$query) {
        if (in_array($table, ['training_program','departments','subjects','syllabus', 'course_units'])) {
            $instituteIds = SELF::getInstituteIdsByLogin();
            $user = Auth::user();

            if($table == 'departments' && !joinTableExists($query, 'training_program')) {
                $query = $query->leftJoin('training_program', 'training_program.department_id', '=', $table.'.id');
            } elseif($table == 'course_units' && !joinTableExists($query, 'training_program')) {
                $query = $query->leftJoin('training_program_course_map AS TPCM', 'course_units.id', '=','TPCM.course_unit_id');
                $query = $query->leftJoin('training_program', 'training_program.id', '=','TPCM.training_program_id');
            }

            if(!joinTableExists($query, 'training_program_subscriptions')) {
                $query = $query->leftJoin('training_program_subscriptions AS TPS', 'training_program.id', '=', 'TPS.training_program_id')
                ->where(["TPS.wf_status" => APPROVED_STATE_SLUG]);
                if($user && $user->applyDataLevelSecurity() && Arr::has($instituteIds, 0)) {
                    $query = $query->whereIn('TPS.institute_id', $instituteIds);
                }
            }
        }
        return $query;
    }

    public static function filterByArrayValue($inputArray, $filterKey) {
        $output = [];
        $filtered = Arr::where($inputArray, function ($value, $key) use($filterKey) {
            return str_ends_with($value['column'], $filterKey);
        });

        if(count($filtered) > 0) {
            $value = array_values($filtered);
            $output = $value[0];
        }

        return $output;
    }

    public static function applyFilterCondition($repository, $query, string $key, string $operator, ?string $value) {
        $dbKey = $key;
        $model = $repository->getModel();
        $table = $model->getTable();
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        } else {
            $dbKey = $table . '.' .$key;
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                if (!$value) {
                    break;
                }

                $value = \Carbon\Carbon::createFromFormat(config('core.base.general.date_format.date'), $value)->toDateString();
                $query = $query->whereDate($dbKey, $operator, $value);
                break;
            case 'my_subscribtion':
                if ($value == "") {
                    break;
                }

                $query = $query->havingRaw('my_subscribtion =' . $value);
                break;
            default:
            /* @Customized By Ramesh Esakki */
                if (is_null($value)) {
                    break;
                }

                if(in_array($table, config('plugins.crud.general.entity_filter_supported_models')) ||
                (Str::contains($value, "|") || Str::endsWith($dbKey, 'entity_type'))) {

                    if(Schema::hasColumn($table, 'entity_type') || Schema::hasColumn($table, 'entity_id')) {
                        if(Str::contains($value, "|")) {
                            list($value, $entityId) = explode("|", $value);
                            $query = $query->where($table.".entity_type", $operator, $entityId)
                            ->where($table.".entity_id", $operator, $value);
                        } elseif(Str::endsWith($dbKey, 'entity_type')) {
                            $query = $query->where($table.".entity_type", $operator, $value);
                        }
                        break;
                    } else {
                        if(!joinTableExists($query, 'user_permissions', 'UP')) {
                            $query = $query->leftjoin('user_permissions AS UP', 'UP.user_id', $table . '.user_id')
                            ->where('UP.is_retired', 0);
                        }
                        if(Str::contains($value, "|")) {
                            list($value, $entityId) = explode("|", $value);
                            $query = $query->where("UP.reference_key", $operator, $entityId)
                            ->where("UP.reference_id", $operator, $value);
                            break;
                        } elseif(Str::endsWith($dbKey, 'entity_type')) {
                            $query = $query->where("UP.reference_key", $operator, $value);
                            break;
                        } 
                    }
                }
                if(in_array(get_class($model),config('plugins.crud.general.intake_supported_modules', [])) && Str::endsWith($dbKey, ['intake_id','intake'])){
                    if ($value == "") {
                        break;
                    }
                    return $query->whereRaw("(SELECT MONTHNAME(STR_TO_DATE(intake_start_month, '%m')) FROM training_program_intakes WHERE id = $dbKey)='" . $value . "'");
                }
                if(in_array(get_class($model),config('plugins.crud.general.intake_supported_modules', [])) && Str::endsWith($dbKey, ['semester_id','term'])){
                   if ($value == "") {
                        break;
                    }                    
                    return $query->whereRaw("(SELECT session_name FROM training_program_intake_semester_mapping WHERE id = $dbKey)='" . $value . "'"); 
                }
                if(in_array(get_class($model),config('plugins.crud.general.governing_council_models', [])) && Str::endsWith($dbKey, ['institution'])){
                    if ($value == "") {
                        break;
                    }
                    return $query->whereJsonContains($dbKey, $value);
                }
                if ($operator === 'like') {
                    $query = $query->where($dbKey, $operator, '%' . $value . '%');
                    break;
                }

                if ($operator !== '=') {
                    $value = (float)$value;
                }
                
                $query = $query->where($dbKey, $operator, $value);
        }
        

        return $query;
    }

    /**
     * @return html
     */
    public static function getCustomFields($field, $options,$model = null)
    {
        $permission = "";
        if($model){
            $pluginName = get_plugin_name($model);
            $permission = $pluginName.'.inline_edit';
            $modulePermission = [];
            if(Arr::has($options,'key')){
                $item = $model::where('id',$options['key'])->first();
                $modulePermission = SELF::checkEnityWisePermissions(['inline_edit'=>$permission],$item);
            }
            $inlineEdit = (Arr::get($modulePermission,'inline_edit')) ? Auth::user()->hasPermission($modulePermission['inline_edit'])  : false;
        }
        
        if($permission && $inlineEdit){
            if (empty($options['type'])) {
                $options['type']='hidden';                
            }
            return view('core/table::partials.custom-fields', compact('field'), compact('options'))->render();
        }

        if($inlineEdit && isset($options['value'])) {
            return $options['value'];
        }
        if(isset($options['type'])&& !$inlineEdit && $options['type'] =='hidden' ) {
            return "";
        }
        return (isset($model->$field) ? $model->$field : '');
    }

    public static function checkEnityWisePermissions($permissions,$item){
        $entityRelationkeys = config('plugins.crud.general.entity_relation_key', []);
        $user = \Auth::user();
        $roleIds = ($user) ? $user->role_ids : []; 
        if($entityRelationkeys && $user){
            foreach($entityRelationkeys as $entityTable => $field){
                $referenceKey = getEntityId($entityTable,'module_db');
                if($item && isset($item->$field) && $referenceKey && $user && $user->applyDataLevelSecurity()){
                   $userPermissions = $user->userPermission()->where(['reference_key'=>$referenceKey,'user_id'=>$user->id,'reference_id'=>$item->$field])->first();
                   $rolePermissions=[];
                   if($userPermissions){
                       $rolePermissions = $userPermissions->role_permissions;                       
                   }else if($roleIds && in_array(getRoleIdFromSlug(STUDENT_ROLE_SLUG),$roleIds)){
                        $referenceKey = getEntityId('institution');
                        $referenceId = getStudentActiveInstitute();
                        $userPermission = $user->roles()->where('roles.slug', STUDENT_ROLE_SLUG)->first();
                        $rolePermissions=$userPermission->permissions;
                   }
                   foreach($permissions as $key => $value){
                        if(!Arr::get($rolePermissions,$permissions[$key])){
                             $permissions[$key] = "";
                         }
                    }
                }
            }
        }
        return $permissions;
    }

}
