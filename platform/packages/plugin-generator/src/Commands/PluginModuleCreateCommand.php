<?php

namespace Impiger\PluginGenerator\Commands;

use Impiger\DevTool\Commands\Abstracts\BaseMakeCommand;
use File;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use DB;
use League\Flysystem\MountManager;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Arr;
use Impiger\CustomField\Models\FieldGroup;
use Impiger\CustomField\Models\FieldItem;
use Illuminate\Support\Facades\Schema;

class PluginModuleCreateCommand extends BaseMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:plugin:module:create {name : The plugin that you want to create} {is_edit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a plugin in the /platform/plugins directory using CRUD builder.';

    /**
     * The console command description.
     *
     * @var int
     */
    protected $moduleId = 0;

    /**
     * The module db table name.
     *
     * @var string
     */
    protected $moduleDBName = '';

    /**
     * The crud configuration data.
     *
     * @var array
     */
    protected $crudModuleConfig = array();

    /**
     * string.
     *
     * @var string
     */
    protected $moduleQry = "";
    protected $moduleTitle = "";

    /**
     * Module Settings.
     *
     * @var boolean
     */
    protected $isBulkUpload = false;
    protected $isMultiLingual = false;
    protected $isEntityBased = false;
    protected $isInlineEdit = false;
    protected $isRowActivation = false;
    protected $subscription = false;
    protected $isEdit = false;
    protected $isDelete = false;
    protected $isCreate = false;
    protected $insertUserBefore = false;
    protected $isWorkflow = false;


    /**
     * The subform data.
     *
     * @var array
     */
    protected $subformData = [];

    /**
     * The listing page config data.
     *
     * @var array
     */
    protected $gridConfigData = [];

    /**
     * The form add/edit page config data.
     *
     * @var array
     */
    protected $formConfigData = [];

    /**
     * The subform has new tab.
     *
     * @var array
     */
    protected $subformWithNewTab = [];

    /**
     * The bootstrap grid class.
     *
     * @var array
     */
    protected $blockConfig = [1 => 'col-md-12', 2 => 'col-md-6', 3 => 'col-md-4', 4 => 'col-md-4'];

    /**
     * The ignore fields on form creation.
     *
     * @var array
     */
    protected $ignorFields = array('updated_at', 'created_at', 'created_by', 'updated_by', 'deleted_at');


    /**
     * The same as above field cofig.
     *
     * @var array
     */
    protected $sameAsAboveConfig = [];

    /**
     * The modify db migration cofig.
     *
     * @var array
     */
    protected $alterScriptData = [];

    /**
     * The migration file constant.
     *
     * @var array
     */
    protected $timestampSuffix = "";


    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws FileNotFoundException
     */
    public function handle()
    {
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('name'))) {
            $this->error('Only alphabetic characters are allowed.');
            return 1;
        }

        $plugin = strtolower($this->argument('name'));
        $isEdit = strtolower($this->argument('is_edit'));

        $row = \DB::table('cruds')->where('module_name', $plugin)->get()->first();
        if (!$row) {
            $this->error('Module name not exists in DB.');
            return 1;
        }

        $this->moduleId = $row->id;
        $this->moduleDBName = $row->module_db;
        $this->crudModuleConfig = CF_decode_json($row->module_config);
        $this->moduleQry = $row->module_queries;
        $this->moduleTitle = ($row->module_alias) ? $row->module_alias : $row->module_name;
        $location = plugin_path($plugin);

        if (isset($row->is_bulkupload) && $row->is_bulkupload) {
            $this->isBulkUpload = true;
        }
        if (isset($row->is_multi_lingual) && $row->is_multi_lingual) {
            $this->isMultiLingual = true;
        }
        if (isset($row->insert_user_before) && $row->insert_user_before) {
            $this->insertUserBefore = true;
        }
        if (isset($row->is_workflow_support) && $row->is_workflow_support) {
            $this->isWorkflow = true;
        }

        if (isset($row->alter_script_data) && $row->alter_script_data) {
            $this->alterScriptData = $this->setAlterScriptData($row->alter_script_data);
        }

        $this->subformData = Arr::get($this->crudModuleConfig, 'subgrid');
        $this->subformWithNewTab = $this->getsubformWithNewTabProp();
        $this->timestampSuffix = str::random(12);
        if (!$isEdit) {
            if (File::isDirectory($location)) {
                $this->error('A plugin named [' . $plugin . '] already exists.');
                return 1;
            }
        } else {
            // $this->removeMigrationFiles($location, $plugin);
        }

        $this->publishStubs($this->getStub(), $location);

        if (!$isEdit) {
            File::copy(__DIR__ . '/../../stubs/plugin.json', $location . '/plugin.json');
            File::copy(__DIR__ . '/../../stubs/Plugin.stub', $location . '/src/Plugin.php');
        } else {
            File::delete($location . '/database/migrations/{migrate_date}_{module}_create_{name}_table.stub');

            if (!$this->isValidArray($this->alterScriptData)) {
                File::delete($location . '/database/migrations/{migrate_date}_{module}_modify_{name}_table{timestampSuffix}.stub');
            }
        }
        /* Remove Scheduler related files*/
        if (!Arr::get($this->crudModuleConfig, 'scheduler')) {
            $files = File::files($location . '/src/Commands/');
            $fileCount = count($files);
            if ($fileCount <= 1) {
                File::delete($location . '/src/Providers/CommandServiceProvider.stub');
                File::deleteDirectory($location . '/src/Commands');
            } else {
                File::delete($location . '/src/Commands/{Name}SchedulerCommand.stub');
            }
        }

        $this->renameFiles($plugin, $location);
        $this->searchAndReplaceInFiles($plugin, $location);
        $this->removeUnusedFiles($location);
        $this->resetAlterScriptData($plugin);
        $this->line('------------------');
        $this->line('<info>The plugin</info> <comment>' . $plugin . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>');
        $this->line('------------------');
        $this->call('cache:clear');
        return 0;
    }

    /**
     * Search and replace all occurrences of ‘Module’
     * in all files with the name of the new module.
     * @param string $pattern
     * @param string $location
     * @param null $stub
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws FileNotFoundException
     */
    public function searchAndReplaceInFiles(string $pattern, string $location, $stub = null): bool
    {
        $replacements = $this->replacements($pattern);

        if (File::isFile($location)) {
            if (!$stub) {
                $stub = File::get($this->getStub());
            }

            $replace = $this->getReplacements($pattern) + $this->baseReplacements($pattern);
            $content = str_replace(array_keys($replace), $replace, $stub);

            File::put($location, $content);
            return true;
        }

        $manager = new MountManager([
            'directory' => new Filesystem(new LocalAdapter($location)),
        ]);

        foreach ($manager->listContents('directory://', true) as $file) {
            if ($file['type'] === 'file') {
                $content = str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $manager->read('directory://' . $file['path'])
                );

                $manager->put('directory://' . $file['path'], $content);
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getStub(): string
    {
        return __DIR__ . '/../../../dev-tool/stubs/crudmodule';
    }

    /**
     * @param string $location
     */
    protected function removeUnusedFiles(string $location)
    {
        File::delete($location . '/composer.json');
    }


    /**
     * @param string $location
     */
    protected function removeMigrationFiles(string $location, $plugin)
    {
        // Delete Data from migrations table
        $endStr = '_' . Str::snake(str_replace('-', '_', $plugin)) . '_create_' . Str::snake(str_replace('-', '_', $plugin)) . '_table';
        DB::table('migrations')->whereRaw("migration LIKE '%" . $endStr . "'")->delete();

        // Delete files from migrations directory
        $files = glob($location . '/database/migrations/*' . $endStr . '.php');
        foreach ($files as $file) {
            if (File::isFile($file))
                File::delete($file);
        }
    }

    public function getFormConfigData()
    {
        $formConfig = Arr::get($this->crudModuleConfig, 'forms');
        foreach ($formConfig as $key => $fieldInfo) {
            $this->formConfigData[$fieldInfo['field']] = $fieldInfo;
            $group = Arr::get($fieldInfo, 'form_group');

            if (Arr::get($fieldInfo, 'option.opt_type') == 'sameAsAbove' && $group) {
                $this->sameAsAboveConfig[$group] = $fieldInfo;
            }
        }

        return $this->formConfigData;
    }

    public function resetAlterScriptData($plugin)
    {
        DB::table('cruds')
            ->where('module_name', '=', $plugin)
            ->update(array('alter_script_data' => NULL));
    }

    /**
     * {@inheritDoc}
     */
    public function getReplacements(string $replaceText): array
    {
        $grid = $this->parseCrudGridData();
        $moduleQry = $this->moduleQry;
        $gridColumns = (isset($grid['columns'])) ? $grid['columns'] : "";

        if (Arr::get($this->crudModuleConfig, 'sql_select')) {
            // preg_match("/->select(.*)/", $this->moduleQry, $match);
            $qryArray = explode("->", $this->moduleQry);

            if (Arr::has($qryArray, 0)) {
                $columnStr = str_replace("select(", "", $qryArray[0]);
                if ($columnStr) {
                    $columnStr = substr($columnStr, 0, -1);
                    $gridColumns = stripslashes($columnStr);
                    unset($qryArray[0]);
                    $moduleQry = "->" . implode("->", $qryArray);
                    $moduleQry = str_replace("\n", "\r\n\t\t\t", $moduleQry);
                }
            }
        } else {
            $moduleQry = "";
        }

        $gridColumnConfig = (isset($grid['columnConfig'])) ? $grid['columnConfig'] : "";
        $gridColumnLookup = (isset($grid['columnLookup'])) ? $grid['columnLookup'] : "";
        $submenu = $this->getSubModuleDetails($replaceText);
        $subform = $this->getSubformDetails($replaceText, $replaceText);
        $bulkUpload = $this->getBulkUploadDetails($replaceText);
        $registerLang = ($this->isMultiLingual) ? '\Language::registerModule([' . ucfirst(Str::camel($replaceText)) . '::class]);' : '';
        $gridDefaultActions = $this->gridDefaultActionBtns($replaceText);
        $menuConfig = $this->getMenuConfig();
        $insertUserBeforeCode = $this->getInsertUserBefore();
        $validation = $this->parseMandatoryData();
        $viewGalleryForm = $this->getViewGalleryFormMethod($replaceText);
        $prevUrl = ($this->insertUserBefore) ? "'users.profile.view',((\str_contains(url()->current(), 'edit-profile/')) ? \$user->user_id : \$user->user_id.'?user_navigation='.\$user->id)" : "'" . strtolower($replaceText) . ".index'";
        $scheduler = $this->getSchedulerDetails($replaceText);
        $emailConfig = $this->getEmailConfigs($replaceText);
        $hideModuleActions = $this->getHideActionSettings();
        $moduleShortCode = $this->getModuleShortCodeFnCall($replaceText);
        $joinFields = $this->applyJoinByQueryintoModel($moduleQry);
        $dashboardStats = $this->getDashboardStatsFilter($replaceText, $replaceText);
        $revisionHistory = $this->getRevisionHistorySettings();
        $workflowTraits = $this->getWorkflowSettings();
        
        return [
            '{type}'     => 'plugin',
            '{types}'    => 'plugins',
            '{++Titles}'    => str_replace('_', ' ',
                ucfirst(Str::plural(Str::snake(str_replace('-', '_', $this->moduleTitle))))),
            '{++title}'    => str_replace('_', ' ', Str::snake(str_replace('-', '_', $this->moduleTitle))),
            '{-module}'  => strtolower($replaceText),
            '{module}'   => Str::snake(str_replace('-', '_', $replaceText)),
            '{+module}'  => Str::camel($replaceText),
            '{modules}'  => Str::plural(Str::snake(str_replace('-', '_', $replaceText))),
            '{Modules}'  => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $replaceText)))),
            '{-modules}' => Str::plural($replaceText),
            '{MODULE}'   => strtoupper(Str::snake(str_replace('-', '_', $replaceText))),
            '{Module}'   => ucfirst(Str::camel($replaceText)),
            '{crud_schema}' => $this->parseCrudDBData(),
            '{alter_crud_schema}' => $this->getAlterScriptData(),
            '{crud_form}' => $this->toBuildForm($replaceText),
            '{view_crud_form}' => $this->toBuildForm($replaceText, true),
            '{fillable_fields}' => $this->getFillableFields($this->crudModuleConfig),
            '{casting_fields}' => $this->getCastingFields($this->crudModuleConfig),
            '{grid_columns_field_name}' => $gridColumns,
            '{grid_columns_config}' => $gridColumnConfig,
            '{names}' => $this->moduleDBName,
            '{namePlural}' => Str::plural(Str::snake(str_replace('-', '_', $replaceText))),            '{crud_config_table}' => 'cruds',
            '{crud_module_name}' => 'module_name',
            '{crud_mandatory_field}' => Arr::get($validation, 'mandatoryFields'),
            '{crud_validation_msg}' => Arr::get($validation, 'validationMsg'),
            '{grid_column_lookups}' => $gridColumnLookup,
            '{set_grid_column_row_class}' => $this->getGridColumnRowClassScript(),
            '{apply_subscription_filter_cndn}' => $this->applySubscriptionFilterCndn(),
            '{isSubscription}' => $this->isSubscriptionParam(),
            '{load_jquery_steps_assets}' => $this->loadJqueryAssets($replaceText, $replaceText),
            '#{register_sub_module}' => Arr::get($submenu, 'registerList'),
            '#{register_submodule_class}' => Arr::get($submenu, 'regiserClass'),
            '{register_external_module}' => $this->getExternalRegisterModule($replaceText, $replaceText),
            '#{register_external_submodule}' => Arr::get($submenu, 'registerExtMod'),
            '{add_dashboard_stats_filter}' => Arr::get($dashboardStats, 'fn_declare'),
            '{callback_dashboard_stats_filter}' => Arr::get($dashboardStats, 'fn_callback'),
            '#{submodule_menus}' => Arr::get($submenu, 'menus'),
            '#{mainmodule_menu}' => Arr::get($submenu, 'mainMenu'),
            '#{submodule_routes}' => Arr::get($submenu, 'routes'),
            '{module_allowed_actions}' => $this->getSubModuleModuleActions($replaceText),
            '#{module_flag}' => Arr::get($submenu, 'moduleFlag'),
            '#{submodule_constants}' => Arr::get($submenu, 'constants'),
            '{query_builder}' => $moduleQry,
            '{subform_relationship_fns}' => Arr::get($subform, 'subformRelFns'),
            '{subform_create_script}' => Arr::get($subform, 'subformCreateScript'),
            '{subform_update_script}' => Arr::get($subform, 'subformUpdateScript'),
            '{subform_validation_script}' => Arr::get($subform, 'validations'),
            '{subform_validation_msg}' => Arr::get($subform, 'validationMsg'),
            '{table_view}' => '"' . Arr::get($bulkUpload, 'table_view') . '"',
            '{import_button}' => Arr::get($bulkUpload, 'upload_buttons'),
            '{inline_edit_button}' => $this->getInlineEditBtn($replaceText),
            '{upload_route}' => Arr::get($bulkUpload, 'upload_route'),
            '{viewgallery_route}' =>  $this->getViewGalleryRoute($replaceText),
            '{viewgallery_controller_method}' =>  $this->getViewGalleryControllerMethod($replaceText),
            '{viewgallery_form_method}' =>  Arr::get($viewGalleryForm, 'methodStr'),
            '{viewgallery_form_declr}' =>  Arr::get($viewGalleryForm, 'declrStr'),
            '{register_lang_module}' => $registerLang,
            '{grid_action_create_button}' => Arr::get($gridDefaultActions, 'create'),
            '{grid_action_edit_button}' => Arr::get($gridDefaultActions, 'edit'),
            '{grid_action_delete_button}' => Arr::get($gridDefaultActions, 'delete'),
            '{grid_action_permission}' => Arr::get($gridDefaultActions, 'has_permission'),
            '{grid_action_extra_buttons}' => $this->getGridActionBtns($replaceText),
            '{form_display_template}' => $this->getFormDisplayTemplate($replaceText),
            '{build_form_template}' => $this->getBuildFormTemplate($replaceText),
            '{entity_method}' => ($this->isEntityBased) ? "CrudHelper::isEntityCheck(\$this);" : " ",
            '{captcha_required}' => $this->getFormCaptchaRequired(),
            '#{submodule_public_routes}' => Arr::get($submenu, 'publicRoutes'),
            '#{render_module_short_code_method}' => Arr::get($moduleShortCode, 'shortcode'),
            '#{render_module_short_code_options}' => Arr::get($moduleShortCode, 'options'),
            '#{render_short_code_method}' => Arr::get($submenu, 'shortcode'),
            '#{render_short_code_method_call}' => Arr::get($submenu, 'shortcodeFnCall'),
            '{menu_priority}' => Arr::get($menuConfig, 'menuOrder'),
            '{menu_icon}' => Arr::get($menuConfig, 'menuIcon'),
            '{user_create_repo_instance}' => Arr::get($insertUserBeforeCode, 'userCreateRepoInstance'),
            '{user_create_declaration}' => Arr::get($insertUserBeforeCode, 'userCreateDeclaration'),
            '{user_update_repo_instance}' => Arr::get($insertUserBeforeCode, 'userUpdateRepoInstance'),
            '{user_update_declaration}' => Arr::get($insertUserBeforeCode, 'userUpdateDeclaration'),
            '{user_delete_repo_instance}' => Arr::get($insertUserBeforeCode, 'userDelete'),
            '{edit-profile-route}' => $this->getChangeProfileRouteScript($replaceText),
            '{check_profile_edit_permission}' => $this->getCheckEditProfilePermissionScript(),
            '{set_prev_url}' => $prevUrl,
            '{timestampSuffix}' => $this->timestampSuffix,
            '#{register_command_service}' => Arr::get($scheduler, 'registerCommandServiceProvider'),
            '#{call_scheduler_command}' => Arr::get($scheduler, 'callSchedulerCommand'),
            '#{scheduler_command_class}' => Arr::get($scheduler, 'schedulerCommandClass'),
            '#{scheduler}' => Arr::get($scheduler, 'schedules'),
            '#{callEmailMethodOnCreate}' => Arr::get($emailConfig, 'createEmail'),
            '#{callEmailMethodOnEdit}' => Arr::get($emailConfig, 'editEmail'),
            '#{hideOnEditAction}' => Arr::get($hideModuleActions, 'edit'),
            '#{hideOnEditLink}' => Arr::get($hideModuleActions, 'edit_name'),
            '#{hideOnDeleteAction}' => Arr::get($hideModuleActions, 'delete'),
            '#{moduleCustomJs}' => $this->getCustomJs($replaceText, $replaceText),
            '#{moduleDomainMapping}' => $this->getDomainMapping($replaceText, $replaceText),
            '{inline_edit_permission}' => ($this->isInlineEdit) ? "&& !Auth::user()->hasPermission('{-name}.inline_edit')" : "",
            '{join_fields}' => ($joinFields) ? $joinFields : "",
            '{merge_created_by_fields}' => (Arr::get($this->formConfigData, 'created_by')) ? "\$request->merge(['created_by' => \Auth::id()]);" : "",
            '{revision_trait_path}' => Arr::get($revisionHistory, 'revision_trait_path'),
            '{revision_trait}' => Arr::get($revisionHistory, 'revision_trait'),
            '{revision_properties}' => Arr::get($revisionHistory, 'revision_properties'),
            '{workflow_trait_path}' => Arr::get($workflowTraits, 'workflow_trait_path'),
            '{workflow_trait}' => Arr::get($workflowTraits, 'workflow_trait'),
            '{workflow_support}' => Arr::get($workflowTraits, 'workflow_support'),
        ];
    }

    public function getDashboardStatsFilter($module, $parent)
    {
        $result = \App\Models\Crud::select(['id', 'module_name', 'module_config', 'parent_id'])
            ->where(function ($query) use ($module) {
                $query->where('module_name', $module)
                    ->orWhereRaw('parent_id IN (SELECT id FROM cruds WHERE module_name="' . $module . '")');
            })->get();

        $addFilter = false;

        if (Arr::has($result, 0)) {
            foreach ($result as $row) {
                $config = CF_decode_json($row->module_config);
                $stats = (isset($config['stats']) ? $config['stats'] : array());

                if (Arr::has($stats, 0)) {
                    $addFilter = true;
                    break;
                }
            }
        }
        $result = [];
        if ($addFilter) {
            $result['fn_declare'] = "add_filter(DASHBOARD_FILTER_ADMIN_LIST, [\$this, 'addCrudStatsWidget'], 15, 2);";
            $result['fn_callback'] = "public function addCrudStatsWidget(\$widgets, \$widgetSettings) {
                return CrudHelper::addCrudStatsWidget('$parent',\$widgets, \$widgetSettings);
            }";
        }

        return $result;
    }

    public function getInsertUserBefore()
    {
        $result = [];
        if ($this->insertUserBefore) {
            $result['userCreateRepoInstance'] = ", \Impiger\ACL\Services\CreateUserService \$service";
            $result['userCreateDeclaration'] = "
            \$request['username'] = $request->input('email');
            \$request['password'] = CrudHelper::randomPassword();
            \$user = \$service->execute(\$request);
            \$request['user_id'] = \$user->id;
            ";

            $result['userUpdateRepoInstance'] = ", \Impiger\ACL\Repositories\Interfaces\UserInterface \$coreUserRepository, \Impiger\ACL\Services\MappingRoleService \$service, \Impiger\ACL\Services\ActivateUserService \$activateUserService";
            $result['userUpdateDeclaration'] = "\$coreuser = CrudHelper::updateCoreUser(\$user->user_id, \$request, \$coreUserRepository, \$service, \$activateUserService);

            if(!\$coreuser->success) {
                return \$response
                    ->setError()
                    ->setMessage(\$coreuser->errorMsg)
                    ->withInput();
            }";

            $result['userDelete'] = "\$coreUser = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->findOrFail(\$user->user_id);
                    if(\$coreUser){
                       app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class)->delete(\$coreUser);
                       CrudHelper::destroyUserSession(\$coreUser);
                    }";
        }

        return $result;
    }

    public function getGridColumnRowClassScript()
    {
        $result = "";
        $isSubscriptionDependant = Arr::get($this->crudModuleConfig, 'setting.is_subscription_related_module');
        if ($this->subscription ) {
            $result = "
            ->setRowClass(function (\$item) {
                return CrudHelper::setSubscriptionRowColor(\$item);
            })";
        }

        return $result;
    }

    public function applySubscriptionFilterCndn()
    {
        $result = "";
        $isSubscriptionDependant = Arr::get($this->crudModuleConfig, 'setting.is_subscription_related_module');
        if ($this->subscription || $isSubscriptionDependant == "1") {
            $result = "
    /**
     * {@inheritDoc}
     */
    public function applyFilterCondition(\$query, string \$key, string \$operator, ?string \$value)
    {
        return CrudHelper::applyFilterCondition(parent::class, \$query, \$key,  \$operator,  \$value);
    }";
        }

        return $result;
    }

    public function isSubscriptionParam()
    {
        $result = false;
        $isSubscriptionDependant = Arr::get($this->crudModuleConfig, 'setting.is_subscription_related_module');
        if ($this->subscription || $isSubscriptionDependant == "1") {
            $result = true;
        }

        return $result;
    }


    public function applyJoinByQueryintoModel($moduleQuery) {
        $joinFields = "";
        if (!empty($moduleQuery)) {
            $queryArr = explode("->", $this->moduleQry);
            $whereNotNull = array_filter($queryArr, function ($v) {
                return str_contains($v, "whereNotNull");
            });
            if (!empty($whereNotNull)) {
                foreach ($whereNotNull as $key => $value) {
                    $field = \Str::between($value, '(', ')');
                    $where = "where(" . $field . ",\$this->id)";
                    $queryArr[$key] = $where;
                }
            }else{
                $where = "where('".$this->moduleDBName.".id',\$this->id)";
                $queryArr[] = $where;
            }
            $query = implode("->", $queryArr);
            $joinFields = "public function join_fields(){ \n\t"
                    . "return \$this->" . $query . "->first();\n\t}";
        }
        return $joinFields;
    }

    public function getChangeProfileRouteScript($replaceText)
    {
        $result = "";
        if ($this->insertUserBefore) {
            $replaceText = ucfirst(Str::camel($replaceText));
            $result = "
            Route::get('edit-profile/{any}', [
                'uses'       => '" . $replaceText . "Controller@edit',
                'permission' => false,
            ]);

            Route::post('edit-profile/{any}', [
                'uses'       => '" . $replaceText . "Controller@update',
                'permission' => false,
            ]);";
        }

        return $result;
    }

    public function getCheckEditProfilePermissionScript()
    {
        $result = "";
        if ($this->insertUserBefore) {
            $result = "
            if (\$request->has('change_profile')) {
                if(\$request->user()->getKey() != \$request->get('change_profile')) {
                    return response()->json(['error' => 'Unauthenticated.'], 401);
                }
            }";
        }

        return $result;
    }

    public function getInlineEditBtn($replaceText)
    {
        $html = "";
        if ($this->isInlineEdit) {
            $html = "\$buttons = CrudHelper::getInlineEditBtn(\$buttons, '" . $replaceText . ".inline_edit',\$this);";
        }

        return $html;
    }

    public function getViewGalleryRoute($module)
    {
        $html = "";
        $moduleConfig = $this->crudModuleConfig;
        if (Arr::get($moduleConfig, 'setting.is_gallery_image')) {
            $html = "Route::get('viewgallery/{any}', [
                'uses'       => '{Name}Controller@viewGallery',
                'permission' => '{-name}.index',
            ]);";
        }

        $search = array('{-names}', '{-name}', '{Name}');
        $replace = array(Str::plural($module), strtolower($module), ucfirst(Str::camel($module)));
        $html = str_replace($search, $replace, $html);

        return $html;
    }

    public function getViewGalleryControllerMethod($module)
    {
        $html = "";
        $moduleConfig = $this->crudModuleConfig;
        if (Arr::get($moduleConfig, 'setting.is_gallery_image')) {
            $html = "

        /**
        * @param int \$id
        * @param FormBuilder \$formBuilder
        * @return string
        */
        public function viewGallery(\$id, FormBuilder \$formBuilder)
        {
            \${-name} = \$this->{-name}Repository->findOrFail(\$id);
            page_title()->setTitle(trans('plugins/{-name}::{-name}.view'));
            return \$formBuilder->create({Name}Form::class, ['model' => \${-name}, 'isViewGallery' => true ])->renderForm();;
        }";
        }

        $search = array('{-names}', '{-name}', '{Name}');
        $replace = array(Str::plural($module), strtolower($module), ucfirst(Str::camel($module)));
        $html = str_replace($search, $replace, $html);

        return $html;
    }

    public function getViewGalleryFormMethod($module)
    {
        $output = [];
        $methodStr = $declrStr = "";
        $moduleConfig = $this->crudModuleConfig;
        if (Arr::get($moduleConfig, 'setting.is_gallery_image')) {
            $methodStr = "

        /**
         * {@inheritDoc}
         */
        public function viewGallery()
        {
            \$this->model = (\Arr::get(\$this->model, '{-names}')  && !\Arr::has(\$this->model, '{-names}.0')) ?(object) \$this->model['{-names}'] : \$this->model;
            \$data = \Impiger\Gallery\Models\GalleryMeta::where(['reference_id' => \$this->model->id, 'reference_type' => get_class(\$this->model)])->first();
            \$data['gallery'] = \$data;
            \$this
                ->setFormOption('template', 'core/base::forms.form-modal')
                ->setupModel(new {Name})
                ->setTitle(page_title()->getTitle())
                ->setValidatorClass({Name}Request::class)
                ->withCustomFields()
                ->setFormOption('class','viewGallery')
                ->add('custom_html_main_open' , 'html', ['html' => view('plugins/gallery::custom-gallery', \$data)->render()])
                ;
        }";

            $search = array('{-names}', '{-name}', '{Name}');
            $replace = array(Str::plural($module), strtolower($module), ucfirst(Str::camel($module)));
            $methodStr = str_replace($search, $replace, $methodStr);
            $declrStr = "
        if((isset(\$this->formOptions['isViewGallery']) && \$this->formOptions['isViewGallery'])) {
            return \$this->viewGallery();
        }";
        }
        $output['methodStr'] = $methodStr;
        $output['declrStr'] = $declrStr;
        return $output;
    }

    public function getGridActionBtns($moduleName, $subModule = false)
    {
        $moduleNames = Str::plural(strtolower($moduleName));
        $moduleConfig = $this->crudModuleConfig;
        $isQuickView = Arr::get($moduleConfig, 'setting.view_details_type');
        $attrStr = ($isQuickView == '1') ? "data-fancybox data-type='ajax' data-src='" . $moduleNames . "/viewdetail/\$item->id ' href='javascript:void(0);'" : " href='" . $moduleNames . "/viewdetail/\$item->id' ";
        $actionExtraButtons = '"' . "<a $attrStr class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>" . '"';
        $name = $moduleName;
        $cruds = DB::table('cruds')->where("module_name", $moduleName)->first();
        if ($subModule) {
            $parentModule = DB::table('cruds')->where("id", $cruds->parent_id)->first();
            $moduleName = $parentModule->module_name;
        }

        $model = "Impiger\{Module}\Models\{Name}";
        $search = array("{Module}", "{Name}");
        $replace = array(ucfirst(Str::camel($moduleName)), ucfirst(Str::camel($name)));
        $model = str_replace($search, $replace, $model);

        if (Arr::get($moduleConfig, 'setting.is_gallery_image')) {
            $actionExtraButtons .= '."' . "<a data-fancybox data-toggle='tooltip' data-type='ajax' data-original-title='View Gallery' data-src='" . $moduleNames . "/viewgallery/\$item->id ' href='javascript:void(0);' class='btn btn-icon btn-sm btn-primary'><i class='fa fa-image'></i></a>" . '"';
        }

        if ($this->isRowActivation && Schema::hasColumn($cruds->module_db, 'is_enabled')) {
            $hideOperation = $this->getHideActionSettings();
            $hideAction = Arr::get($hideOperation, 'enable_disable') ? $hideOperation['enable_disable'] : false;
            $actionExtraButtons .= '.' . "CrudHelper::getRowActivationActionBtn(\$item,'" . $model . "', '" . $cruds->module_name . ".enable_disable','" . $hideAction . "')";
        }

        if ($this->subscription) {
            $subModuleName = ($subModule) ?$cruds->module_name : $moduleName;
            $actionExtraButtons .= '.' . "CrudHelper::getSubscriptionBtn('" . $subModuleName . "',\$item)";
        }

        if (Arr::get($moduleConfig, 'setting.is_custom_action')) {
            $actionExtraButtons .= '.' . "apply_filters(ADD_CUSTOM_ACTION,'',\$this->repository->getModel(),\$item)";
        }

        return $actionExtraButtons;
    }

    public function getFormDisplayTemplate()
    {
        $moduleConfig = $this->crudModuleConfig;
        $isQuickView = Arr::get($moduleConfig, 'setting.view_details_type');
        return ($isQuickView == '1') ? "->setFormOption('template', 'core/base::forms.form-modal')" : "";
    }

    /* @Created by Sabari Shankar.parthiban */
    public function getBuildFormTemplate()
    {
        $moduleConfig = $this->crudModuleConfig;
        $actionBox = Arr::get($moduleConfig, 'setting.action_box');
        $revision = Arr::get($moduleConfig, 'setting.revision_history');
        $template = ($revision) ? 'module.form-tabs-template' : 'module.form-template';
        return ($actionBox == 'bottom') ? "->setFormOption('template','".$template ."')" : "";
    }

    /* @Created by Sabari Shankar.parthiban */
    public function getMenuConfig()
    {
        $menuConfig = [];
        $moduleConfig = $this->crudModuleConfig;
        $menuConfig['menuOrder'] = (Arr::get($moduleConfig, 'setting.menu_priority')) ?: 5;
        $menuConfig['menuIcon'] = (Arr::get($moduleConfig, 'setting.menu_icon')) ?: null;
        return $menuConfig;
    }

    public function getFormCaptchaTemplate()
    {
        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $moduleConfig = $this->crudModuleConfig;
            $isCaptcha = Arr::get($moduleConfig, 'setting.is_captcha');
            $showField = 'front_end';
            if ($isCaptcha) {
                $showField = Arr::get($isCaptcha, 'back_end') ? 'back_end' : $showField;
                if (Arr::get($isCaptcha, 'back_end') && Arr::get($isCaptcha, 'front_end')) {
                    $showField = 'back_end|front_end';
                }
            }
            $captchaFieldTemplate = ";\n\t\tif(CrudHelper::isFieldVisible('','1','$showField')) {
                \$this->add('body', 'textarea', [
                'template' => 'base.utility.captcha'
            ]);}\$this";
            return ($isCaptcha) ? $captchaFieldTemplate : "";
        }
        return "";
    }

    public function getFormCaptchaRequired()
    {
        if (setting('enable_captcha') && is_plugin_active('captcha')) {
            $moduleConfig = $this->crudModuleConfig;
            $isCaptcha = Arr::get($moduleConfig, 'setting.is_captcha');
            $captchaFieldTemplate = '$validationRules=\App\Utils\CrudHelper::getcaptchavalidation($validationRules);';


            return ($isCaptcha) ? $captchaFieldTemplate : "";
        }
        return "";
    }

    public function loadJqueryAssets($name, $module = null)
    {
        $moduleConfig = $this->crudModuleConfig;
        $format = Arr::get($moduleConfig, 'form_layout.format');
        $customJs = Arr::get($moduleConfig, 'setting.custom_js');
        $pluginJsFile = ($customJs) ? ",'vendor/core/plugins/" . $module . "/js/" . $name . ".js'" : "";
        $galleryJsFile = $galleryCssFile = "";

        if (Arr::get($moduleConfig, 'setting.is_gallery_image')) {
            $galleryJsFile = ",'vendor/core/plugins/gallery/js/lightgallery.min.js?v=1.0.0',
            'vendor/core/plugins/gallery/js/imagesloaded.pkgd.min.js?v=1.0.0',
            'vendor/core/plugins/gallery/js/masonry.pkgd.min.js?v=1.0.0',
            'vendor/core/plugins/gallery/js/gallery.js?v=1.0.0'";
            $galleryCssFile = ",'vendor/core/plugins/gallery/css/lightgallery.min.css?v=1.0.0',
            'vendor/core/plugins/gallery/css/gallery.css?v=1.0.0'";
        }

        if (in_array($format, ['wizzard', 'tab'])) {
            return "Assets::addStylesDirectly([
                'vendor/core/core/base/libraries/jquery-steps/css/jquery.steps.css',
                'vendor/core/plugins/crud/css/module_custom_styles.css'
                " . $galleryCssFile . "
                ])
                ->addScriptsDirectly([
                    'vendor/core/core/base/libraries/jquery-steps/js/jquery.steps.js',
                    'vendor/core/plugins/crud/js/custom_save_storage.js',
                    'vendor/core/plugins/crud/js/crud_utils.js'
                    " . $pluginJsFile . "
                    " . $galleryJsFile . "
                ])
                ->addScriptsDirectlyToHeader([
                    'vendor/core/plugins/crud/js/jquery_steps_init.js'
            ]);";
        }

        return "Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js'
            " . $pluginJsFile . "
            " . $galleryJsFile . "
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            " . $galleryCssFile . "
        ]);";
    }

    public function getCustomJs($name, $module = null)
    {
        $cndns = ['id' => $this->moduleId];
        $result = DB::table('cruds')->select(['id', 'module_name', 'module_config'])->where($cndns)->orWhere(['parent_id' => $this->moduleId])->get();
        $moduleConfig = $this->crudModuleConfig;
        $customJs = Arr::get($moduleConfig, 'setting.custom_js');
        $pluginJsFile = '';
        if (!empty($result)) {
            foreach ($result as $row) {
                $subModuleConfig = CF_decode_json($row->module_config);
                $subModuleCustomJs = Arr::get($subModuleConfig, 'setting.custom_js');
                $subModule = $row->module_name;
                if ($subModuleCustomJs) {
                    $pluginJsFile .= "Theme::asset()
                    ->container('footer')
                    ->usePath(false)
                    ->add('" . $subModule . "-custom-js', asset('vendor/core/plugins/" . $module . "/js/" . $subModule . ".js'), ['jquery'], [], '1.0.0');";
                }
            }
        }
        return $pluginJsFile;
    }

    public function getDomainMapping($name, $module = null)
    {
        $moduleConfig = $this->crudModuleConfig;
        $domainMapping = Arr::get($moduleConfig, 'setting.domain_mapping');
        $mappingMethod = ($domainMapping) ? "CrudHelper::mappingDomain(\$request, \$" . $name . ");" : "";

        return $mappingMethod;
    }

    public function parseCrudDBData()
    {
        $formConfig = $this->crudModuleConfig['forms'];
        $crudSchema = "";
        foreach ($formConfig as $key => $fieldInfo) {
            if (isset($fieldInfo['dbType'])) {
                $crudSchema .= $this->buildMigrationData($fieldInfo['dbType'], $fieldInfo);
            }
        }
        return $crudSchema;
    }

    public function setAlterScriptData($alterScriptData)
    {
        if (!$alterScriptData) {
            return [];
        }

        $alterScriptData = json_decode($alterScriptData, true);
        $createSQL = Arr::get($alterScriptData, 'create');
        $modifySQL = Arr::get($alterScriptData, 'modify');
        $removeSQL = Arr::get($alterScriptData, 'remove');

        if ($this->isValidArray($createSQL) || $this->isValidArray($modifySQL) || $this->isValidArray($removeSQL)) {
            return $alterScriptData;
        }
        return [];
    }

    public function getAlterScriptData()
    {
        if (!$this->isValidArray($this->alterScriptData)) {
            return "";
        }

        $HTML = "";
        $createSQL = Arr::get($this->alterScriptData, 'create');
        $modifySQL = Arr::get($this->alterScriptData, 'modify');
        $removeSQL = Arr::get($this->alterScriptData, 'remove');
        $HTML .= $this->formatAlterSql($createSQL);
        $HTML .= $this->formatAlterSql($modifySQL, true);
        $HTML .= $this->removeAlterSql($removeSQL);
        return $HTML;
    }

    public function formatAlterSql($data, $modify = false)
    {
        $html = "";
        if ($data && is_array($data)) {
            foreach ($data as $fieldInfo) {
                if (isset($fieldInfo['dbType'])) {
                    $html .= $this->buildMigrationData($fieldInfo['dbType'], $fieldInfo, false, $modify, Arr::get($fieldInfo, 'after'));
                }
            }
        }

        return $html;
    }

    public function removeAlterSql($data)
    {
        $html = "";
        if ($data && is_array($data)) {
            foreach ($data as $field) {
                $html .= "if (Schema::hasColumn('" . $this->moduleDBName . "', '" . $field . "')){

                        Schema::table('" . $this->moduleDBName . "', function (Blueprint \$table) {
                            \$table->dropColumn('" . $field . "');
                        });
                    }";
            }
        }

        return $html;
    }

    public function parseCrudFormData($replaceText, $isView = false)
    {
        $moduleConfig = $this->crudModuleConfig;
        $crudForm = "";
        $method = ($isView) ? 'viewForm' : 'buildForm';
        foreach ($moduleConfig['forms'] as $key => $fieldInfo) {
            if (Arr::get($fieldInfo, 'view')) {
                $crudForm .= $this->$method($fieldInfo['type'], $fieldInfo);
            }
        }
        if(empty($this->subformData) && $isView){
            $crudForm.=$this->getJoinFieldsToView($moduleConfig['grid']);
        }
        $crudForm .= (!$isView) ? $this->getFormCaptchaTemplate() : "";
        $crudForm .= $this->loadSubForm($replaceText, $this->subformData, true);
        return $crudForm;
    }

    public function getJoinFieldsToView($gridColumns) {
        $previousField = "";
        $crudForm = "";
        foreach ($gridColumns as $key => $fieldInfo) {
            if (Arr::get($fieldInfo, 'view')) {
                if ($this->moduleDBName != Arr::get($fieldInfo, 'alias')) {
                    $crudForm .= $this->viewForm('text', $fieldInfo, false, '', 'vertical', 0, 0, $previousField);
                } else {
                    $previousField = $fieldInfo['field'];
                }
            }
        }
        return $crudForm;
    }

    public function parseMandatoryData()
    {
        $moduleConfig = $this->crudModuleConfig;
        $result = [];
        $mandatoryFields = $validationMsg = '';
        foreach ($moduleConfig['forms'] as $key => $fieldInfo) {
            if (!empty($fieldInfo['required'])) {
                if (Str::contains($fieldInfo['required'], 'customValidationRules')) {
                    $mandatoryFields .= "'" . $fieldInfo['field'] . "'=>" . $fieldInfo['required'] . ",";
                } else {
                    $mandatoryFields .= "'" . $fieldInfo['field'] . "'=>'" . $fieldInfo['required'] . "',";
                }
                $msg = Arr::get($fieldInfo, 'option.validation_msg');
                if ($msg) {
                    $msgList = explode("|", $msg);

                    foreach ($msgList as $val) {
                        $val = explode(":", $val);
                        if (Arr::has($val, '0') && Arr::has($val, '1')) {
                            $validationMsg .= "'" . $fieldInfo['field'] . "." . Arr::get($val, '0') . "'=>'" . Arr::get($val, '1') . "',";
                            $validationMsg .= "\r\n\t";
                        }
                    }
                }
                $mandatoryFields .= "\r\n\t";
            }
        }

        $result['mandatoryFields'] = $mandatoryFields;
        $result['validationMsg'] = $validationMsg;
        return $result;
    }

    /**
     * To construct each form elements here.
     */
    public function buildForm($type, $fieldInfo, $skipNextLine = false, $slug = '', $displayType = 'vertical', $subBlockCnt = 0, $fieldIndex = 0)
    {
        $fieldName = strtolower($fieldInfo['field']);
        $form = '';
        $required = $labelCls = $inputClsAttr = $wrapperClsAtrr = $rules = $cls = '';

        if (in_array($fieldName, $this->ignorFields)) {
            return false;
        }

        if (Arr::get($fieldInfo, 'field') == 'required') {
            $required = 'required';
        }
        if (Arr::get($fieldInfo, 'required')) {
            $rules = strtolower($fieldInfo['required']);
            // if (Str::contains($rules, "required:")) {
            if (preg_match("/\brequired\b/i", $rules)) {
                $required = 'required';
            }
        }
        $customCodeGeneration = '';
        if (Arr::get($fieldInfo, 'option.generate_custom_code')) {
            $generateCode = $fieldInfo['option']['generate_custom_code'];
            $generateCodeTable = $generateCode;
            $generateCodeField = 'id';
            if (Str::contains($generateCode, "|")) {
                $generateCustomCode = explode("|", $fieldInfo['option']['generate_custom_code']);
                $generateCodeTable = $generateCustomCode[0];
                $generateCodeField = $generateCustomCode[1];
            }

            if ($generateCodeTable) {
                $prefix = Arr::get($fieldInfo, 'option.prefix') ? $fieldInfo['option']['prefix'] : '';
                $suffix = Arr::get($fieldInfo, 'option.suffix') ? $fieldInfo['option']['suffix'] : '';
                $customCodeGeneration = ',"value"=>CrudHelper::generateCustomCode($this->model->' . $fieldInfo['field'] . ',"' . $generateCodeTable . '","' . $generateCodeField . '","' . $prefix . '","' . $suffix . '")';
            }
        }
        $dataIndexAttr = ",'data-field_index' => '" . $fieldIndex . "'";
        $disabledAttr = (is_array(Arr::get($fieldInfo, 'option.disabled'))) ? ",'disabled' => CrudHelper::isFieldDisabled('" . implode("|", array_keys(Arr::get($fieldInfo, 'option.disabled'))) . "')" : "";
        $readOnlyAttr = (Arr::get($fieldInfo, 'option.readonly')) ? ",'readonly' => true " : "";
        $wrapperGridClsConfig = Arr::get($fieldInfo, 'option.wrapper_grid_cls');
        $clsB4Wrap = "";
        $disableWrap = ($type == 'radio') ?  $disabledAttr : "";

        if ($subBlockCnt > 0) {
            $cls = (Arr::has($this->blockConfig, $subBlockCnt)) ? Arr::get($this->blockConfig, $subBlockCnt) : "";
            $clsB4Wrap = $cls;
            $cls = ($wrapperGridClsConfig) ? $wrapperGridClsConfig : $cls;
            $wrapperClsAtrr = ",'wrapper' => ['class' => 'form-group " . $cls . "'" . $disableWrap . "]";
        }

        if ($displayType == 'horizontal') {
            $cls = ($wrapperGridClsConfig) ? $wrapperGridClsConfig : $cls;
            $clsB4Wrap = $cls;
            $wrapperClsAtrr = ",'wrapper' => ['class' => 'form-group row " . $cls . "']";
            $labelCls = "col-md-4";
            $inputClsAttr = ",'attr' => ['class' => 'form-control col-md-8'" . $dataIndexAttr . $disabledAttr . "]";
        }

        $entity = (Arr::get($fieldInfo, 'option.opt_type') == 'entity') ? true : false;
        $specificEntity = (Arr::get($fieldInfo, 'option.specific_entity_type')) ? true : false;
        $this->isEntityBased = ($entity && !$specificEntity) ? $entity : $this->isEntityBased;

        switch ($type) {
            case 'hidden':
                $defaultValue = ($fieldInfo['field'] == 'entity_type') ? ',"value"=>CrudHelper::getEntityValue()' : "";
                $defaultValue = ($customCodeGeneration) ?: $defaultValue;
                $defaultValue = (Arr::get($fieldInfo, 'option.default_value')) ? ',"value"=>'.$fieldInfo["option"]["default_value"]:$defaultValue;
                $form = '->add("' . $fieldInfo['field'] . '" , "hidden", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label"],"attr" => [""' . $disabledAttr . '], "rules" => "sometimes|required"' . $wrapperClsAtrr . $defaultValue . '])';
                break;
            case 'text':
            case 'password':
            case 'number':
                $defaultValue = ($customCodeGeneration) ?: "";
                $defaultValue = (Arr::get($fieldInfo, 'option.default_value')) ? ',"value"=>'.$fieldInfo["option"]["default_value"]:$defaultValue;
                $inputClsAttr = ($inputClsAttr) ? $inputClsAttr : ",'attr' => ['data-field_index' => '" . $fieldIndex . "'" . $disabledAttr . $readOnlyAttr . "]";
                $form = '->add("' . $fieldInfo['field'] . '" , "' . $type . '", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . $defaultValue . ', "rules" => "' . $required . '"])';
                if ($wrapperGridClsConfig && str_contains($wrapperGridClsConfig, 'drop-down-xm')) {
                    $form .= '->add("custom_html_dd_sm_close' . $fieldInfo['field'] . '" , "html", ["html" => "</div></div>"])';
                }
                break;
            case 'select':
                $inputCls = ($displayType == 'horizontal') ? " col-md-8" : "";
                $lookupTable = Arr::get($fieldInfo, 'option.lookup_table');
                $lookupKey = Arr::get($fieldInfo, 'option.lookup_key');
                $lookupValue = Arr::get($fieldInfo, 'option.lookup_value');
                $fields = explode("|", Arr::get($fieldInfo, 'option.lookup_dependency_key'));
                $depedentKey = $depedentFilterKey = "";
                if (count($fields) >= 2) {
                    $depedentKey = $fields[0];
                    $depedentFilterKey = $fields[1];
                }

                $whereCondn = Arr::get($fieldInfo, 'option.where_cndn');
                $whereCondn = str_replace("'", '"', $whereCondn);
                $submodule = (Arr::get($this->argument(), 'plugin')) ? Str::plural(Str::snake(str_replace('-', '_', $this->argument('name')))) : null;
                $depedentKey = ($fieldInfo['option']['opt_type'] == 'entity') ? 'entity_type' : $depedentKey;
                $specificEntity = ($fieldInfo['option']['opt_type'] == 'entity') ? Arr::get($fieldInfo, 'option.specific_entity_type') : "";
                $labelName = ($fieldInfo['option']['opt_type'] == 'entity' && !$specificEntity) ? "CrudHelper::getLabelName()" : '"' . $fieldInfo['label'] . '"';
                $methodName = "CrudHelper::getSelectOptionValues('" . $fieldInfo['option']['opt_type'] . "', '" . $fieldInfo['option']['lookup_query'] . "', '" . $lookupTable . "', '" . $lookupKey . "', '" . $lookupValue . "', '" . $whereCondn . "', '" . $depedentFilterKey . "', \$this->model, '" . $depedentKey . "', \$this->getName(), '" . $specificEntity . "')";
                $depedentText = ($depedentKey && $depedentFilterKey) ? ",'data-dd_qry_filterkey' => '" . $depedentFilterKey . "','data-dd_parentkey' => '" . $depedentKey . "','data-dd_table' => '" . $lookupTable . "','data-dd_key' => '" . $lookupKey . "','data-dd_lookup' => '" . $lookupValue . "' " : "";
                $isMultiSelect = Arr::get($fieldInfo, 'option.select_multiple');
                $multipleAttr = ($isMultiSelect) ? ",'multiple' => true" : "";
                $emptyAttr = (!$isMultiSelect) ? ',"empty_value" => "Select"' : "";
                $restrictBasedOn =  Arr::get($fieldInfo, 'option.restrict_based_on');
                $restrictBasedOnAttr = ($restrictBasedOn) ? ",'restrict_based_on' => 'true'" : "";
                if ($fieldInfo['option']['opt_type'] == 'customFunction') {
                    $dependantDetails = $fieldInfo['option']['dependant_table'];
                    $methodName = "CrudHelper::" . $fieldInfo['option']['custom_function'] . "('" . $dependantDetails . "')";
                }

                if ($wrapperGridClsConfig && str_contains($wrapperGridClsConfig, 'drop-down-sm')) {
                    $customHtml = "<div class='form-group " . $clsB4Wrap . "'> <div class='form-group row'>";
                    $form = '->add("sm_open' . $fieldInfo['field'] . '" , "html", ["html" => "' . $customHtml . '"])';
                }

                $form .= '->add("' . $fieldInfo['field'] . '" , "customSelect", ["label" => ' . $labelName . ', "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],'
                    . '"attr" => ["class" => "select-full' . $inputCls . '"' . $depedentText . $dataIndexAttr . $multipleAttr . $restrictBasedOnAttr .  $disabledAttr . '],'
                    . '"choices"    => ' . $methodName . $wrapperClsAtrr . $emptyAttr . ', "rules" => "' . $required . '"])';
                break;
            case 'checkbox':
                $inputCls = ($displayType == 'horizontal') ? " col-md-8" : "";
                $options = $this->getCheckAndCheckBoxes($fieldInfo['option']['lookup_query']);
                $whereCondn = Arr::get($fieldInfo, 'option.where_cndn');
                $methodName = "CrudHelper::getSelectOptionValues('" . $fieldInfo['option']['opt_type'] . "', '" . $fieldInfo['option']['lookup_query'] . "', '" . $fieldInfo['option']['lookup_table'] . "', '" . $fieldInfo['option']['lookup_key'] . "', '" . $fieldInfo['option']['lookup_value'] . "', '" . $whereCondn . "')";
                $form = "";
                if (!empty($options)) {
                    $form .= '->add("' . $fieldInfo['field'] . '" , "choice", ["label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],"choices"    => ' . $methodName . $wrapperClsAtrr . ',"choice_options"=>["wrapper" => ["class" => "choice-wrapper"],"label_attr" => ["class" => "label-class"]],"expanded"=>true,"multiple"=>true], "rules" => "' . $required . '"])';
                } else {
                    $form .= '->add("' . $fieldInfo['field'] . '" , "checkbox", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],'
                        . '"attr" => ["class" => "control-label custom-checkbox' . $inputCls .  $disabledAttr . '"]' . $wrapperClsAtrr . ', "rules" => "' . $required . '"])';
                }
                break;
            case 'text_datetime':
            case 'text_date':
                $format = ($type == 'text_date') ? config('core.base.general.date_format.js.date') : config('core.base.general.date_format.js.date_time');
                $currentDate = "\Carbon\Carbon::now()->format('Y-m-d')";
                $fieldAttr = Arr::get($fieldInfo, 'option.attribute') ? "," . $fieldInfo['option']['attribute'] : "";
                $inputClsAttr = ($inputClsAttr) ? $inputClsAttr . ",'data-date-format' => '" . $format . "'" : ",'attr' => ['data-date-format' => '" . $format . "' " . $fieldAttr . "]";
                $form = '->add("' . $fieldInfo['field'] . '" , "date", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . $disabledAttr . ',"default_value"=>"","rules" => "' . $required . '"])';
                break;
            case 'time':
                $form = '->add("' . $fieldInfo['field'] . '" , "time", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . ', "rules" => "' . $required . '"])';
                break;
            case 'textarea':
                $inputCls = ($displayType == 'horizontal') ? ",'class' => 'form-control col-md-8'" : "";
                $form = '->add("' . $fieldInfo['field'] . '" , "textarea", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required  . ' ' . $labelCls . '"], "attr"=>["rows" => 4' . $inputCls . $dataIndexAttr . ']' . $wrapperClsAtrr . $inputClsAttr . ', "rules" => "' . $required . '"])';
                break;
            case 'image':
                $form = '->add("' . $fieldInfo['field'] . '" , CrudHelper::getFileType("mediaImage"), ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr . ', "rules" => "' . $required . '"])';
                break;
            case 'file':
                $uploadType = Arr::get($fieldInfo, 'option.upload_type');
                $pathToUpload = Arr::get($fieldInfo, 'option.path_to_upload');
                $uploadType = ($uploadType == 'image') ? 'mediaImage' : 'mediaFile';
                $pathToUpload = ($pathToUpload) ? $pathToUpload : '';
                $form = '->add("' . $fieldInfo['field'] . '" , CrudHelper::getFileType("' . $uploadType . '"), ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],rv_media_handle_upload(request()->file("file"), 0, "' . $pathToUpload . '")' . $wrapperClsAtrr . $inputClsAttr . ', "rules" => "' . $required . '"])';
                break;
            case 'number':
                $form = '->add("' . $fieldInfo['field'] . '" , "number", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],"default_value" => 1' . $wrapperClsAtrr . $inputClsAttr . '])';
                break;
            case 'onOff':
                $form = '->add("' . $fieldInfo['field'] . '" , "onOff", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],"default_value" => false' . $wrapperClsAtrr . $inputClsAttr . '])';
                break;
            case 'radio':
                $inputClsAttr = ($displayType == 'horizontal') ? ",'attr' => ['class' => 'col-md-8']" : "";
                $options = $this->getCheckAndCheckBoxes($fieldInfo['option']['lookup_query']);
                $form = "";
                if (!empty($options)) {
                    $methodName = "CrudHelper::getRadioOptionValues('" . $fieldInfo['option']['opt_type'] . "', '" . $fieldInfo['option']['lookup_query'] . "')";
                    $form .= '->add("' . $fieldInfo['field'] . '" , "customRadio", ["label" => "' . $fieldInfo['label'] . '","label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"' . $disabledAttr . '],"choices"    => ' . $methodName . $wrapperClsAtrr . ', "rules" => "' . $required . '"])';
                } else {
                    $form = '->add("' . $fieldInfo['field'] . '" , "radio", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"],'
                        . '"attr" => ["class" => "control-label' . $inputClsAttr . '",]])';
                }
                break;
            case 'repeater':
                $repeaterData = Arr::get($fieldInfo, 'repeater_data');
                if ($repeaterData) {
                    $form = '->add("' . $fieldInfo['field'] . '" , "repeater", ' . $repeaterData . ')';
                }
                break;
            case 'textarea_editor':
                $hideButtons = Arr::get($fieldInfo, 'editor_config_buttons') ? "true" : "false";
                $inputCls = ($displayType == 'horizontal') ? ",'class' => 'form-control col-md-8'" : "";
                $form = '->add("' . $fieldInfo['field'] . '" ,"editor",["label" => "' . $fieldInfo['label'] . '","label_attr" => ["class" => "control-label ' . $required  . ' ' . $labelCls . '"],"attr" =>["without-buttons" => ' . $hideButtons . ']' . $wrapperClsAtrr . ' ])';
                break;
            case 'html':
                $form = '->add("' . $slug . '" , "html", ["html" => "' . $fieldInfo['html'] . '"])';
                break;
            case 'maps':
                $form = '->add("' . $fieldInfo['field'] . '" , "textarea", ["template" => "google-map"])';
                break;
            case 'tags':
                $form = '->addCustomField("tags", \Impiger\Base\Forms\Fields\TagField::class)->add("' . $fieldInfo['field'] . '", "tags", ["label" => "' . $fieldInfo['label'] . '","label_attr" => ["class" => "control-label ' . $required . ' ' . $labelCls . '"]' . $wrapperClsAtrr . ',"attr" => ["class"=>"form-control"], "rules" => "' . $required . '" ,"value"=>CrudHelper::getTagValues($this->model,"' . $fieldInfo['field'] . '")])';
                break;
            default:
                break;
        }

        $restrictTo = (Arr::get($fieldInfo, 'option.restricted_role_id.0')) ? true : false;
        $hiddenConfig = (is_array(Arr::get($fieldInfo, 'option.hidden'))) ? implode("|", array_keys(Arr::get($fieldInfo, 'option.hidden'))) : "";

        if ($restrictTo || $hiddenConfig) {
            $restictedRoleId = ($restrictTo) ? implode("|", Arr::get($fieldInfo, 'option.restricted_role_id')) : "";
            $fieldAccessType = Arr::get($fieldInfo, 'option.field_access_type');
            $form = ";\r\n\t\t\t if(CrudHelper::isFieldVisible('" . $restictedRoleId . "','" . $fieldAccessType . "','" . $hiddenConfig . "')) {\r\n\t\t\t \$this" . $form . ";}\r\n\t\t\t \$this";
        }
        $form .= ($skipNextLine) ? "" : "\r\n\t\t\t";

        return $form;
    }

    /**
     * To construct each form elements for view page
     */
    public function viewForm($type, $fieldInfo, $skipNextLine = false, $slug = '', $displayType = 'vertical', $subBlockCnt = 0, $fieldIndex = 0,$fieldAfter = null)
    {
        $fieldName = strtolower($fieldInfo['field']);
        $title = Arr::get($fieldInfo, 'label');
        $labelCls = $inputClsAttr = $form = $wrapperClsAtrr = $cls = '';
        $inputClsAttr = ",'attr' => ['class' => 'customStaticCls']";
        $attrCls = 'customStaticCls';
        if (in_array($fieldName, $this->ignorFields)) {
            return false;
        }

        if ($subBlockCnt) {
            $cls = (Arr::has($this->blockConfig, $subBlockCnt)) ? Arr::get($this->blockConfig, $subBlockCnt) : "";
            $wrapperClsAtrr = ",'wrapper' => ['class' => 'form-group " . $cls . "']";
        }

        if ($displayType == 'horizontal') {
            $wrapperClsAtrr = ",'wrapper' => ['class' => 'form-group " . $cls . "']";
            $labelCls = " col-md-4";
            $inputClsAttr = ",'attr' => ['class' => 'col-md-8']";
            $attrCls = 'col-md-8';
        }

        $gridConfig = Arr::get($this->gridConfigData, $fieldInfo['field']);
        $val = $rowVal = "";

        if ($gridConfig) {
            $formatAsRequired = Arr::get($gridConfig, 'format_as');

            if ($fieldName == 'entity_id') {
                $rowVal = "CrudHelper::formatEntityValue(\$this->model->entity_type,\$this->model->" . $gridConfig['field'] . ")";
                $val = ',"value" => ' . $rowVal;
            } else if ($fieldName == 'entity_type') {
                $type = "text";
                $rowVal = "CrudHelper::formatEntityTypeValue(\$this->model->entity_type)";
                $val = ',"value" => ' . $rowVal;
            } else if ($formatAsRequired && $formatAsRequired != 'workflow') {
                $conn = (Arr::get($gridConfig, 'conn.valid') == 1 && Arr::get($gridConfig, 'conn.db')) ? implode(':', $gridConfig['conn']) : null;
                $formatValue = Arr::get($gridConfig, 'format_value');
                $rowVal = "CrudHelper::formatRows(\$this->model->" . $gridConfig['field'] . ", '" . $formatAsRequired . "', '" . $formatValue . "', \$this->model, '" . $conn . "')";
                $val = ',"value" => ' . $rowVal;
                if($formatAsRequired == 'file' || $formatAsRequired == 'image'){
                    $type ='file';
                    $val=($formatAsRequired == 'file') ? $rowVal : $val;
                }
            }else if($this->moduleDBName != Arr::get($fieldInfo, 'alias')) {
                $rowVal ="\$this->model->join_fields()->" . $gridConfig['field'];
                $val =',"value" => ' . $rowVal;
            }
        }

        $isMultiSelect = Arr::get($fieldInfo, 'option.select_multiple');
        $type = ($isMultiSelect) ? "select_multiple" : $type;
        $addType = ($fieldAfter) ? '->addAfter("'.$fieldAfter.'",' : '->add(';
        switch ($type) {
            case 'hidden':
                break;
            case 'select_multiple':
                $lookupTable = Arr::get($fieldInfo, 'option.lookup_table');
                $lookupKey = Arr::get($fieldInfo, 'option.lookup_key');
                $lookupValue = Arr::get($fieldInfo, 'option.lookup_value');
                $whereCondn = Arr::get($fieldInfo, 'option.where_cndn');
                $whereCondn = str_replace("'", '"', $whereCondn);
                $val = ",'value' => CrudHelper::getMultiSelectText('" . $lookupTable . "','" . $lookupKey . "', '" . $fieldName . "', '" . $lookupValue . "', '" . $whereCondn . "', \$this->model)";
                $form = $addType.'"' . $fieldInfo['field'] . '" , "static", ["tag" => "div" ' . $val . ', "label" => "' . $title . '" , "label_attr" => ["class" => "control-label ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . '])';
                break;
            case 'file':
                $uploadType = Arr::get($fieldInfo, 'option.upload_type');
                $pathToUpload = Arr::get($fieldInfo, 'option.path_to_upload');
                $uploadType = ($uploadType == 'image') ? 'mediaImage' : 'mediaFile';
                $pathToUpload = ($pathToUpload) ? $pathToUpload : '';
                if(Arr::get($gridConfig, 'format_as') && $gridConfig['format_as'] != 'image'){
                    $fileClsAttr = ",'attr' => ['class' => '$attrCls' ,'href' =>'/storage/'.\$this->model->".$fieldInfo['field']." ,'target'=>'_blank']";
                    $form = $addType.'"' . $fieldInfo['field'] . '" , "static", ["tag" => "a" , "label" => "' . $title . '" , "label_attr" => ["class" => "control-label ' . $labelCls . '"]' . $wrapperClsAtrr . $fileClsAttr  . '])';
                }else{
                    $form = '->add("' . $fieldInfo['field'] . '" , "' . $uploadType . '", ["label" => "' . $fieldInfo['label'] . '", "label_attr" => ["class" => "control-label  ' . $labelCls . '"],rv_media_handle_upload(request()->file("file"), 0, "' . $pathToUpload . '")' . $wrapperClsAtrr . $inputClsAttr . '])';
                }
                break;
            case 'text_date':
                $val = ($val) ? $val : ",'value' => CrudHelper::formatDate(\$this->model->" . $fieldInfo['field'] . ")";
                $form = $addType.'"' . $fieldInfo['field'] . '" , "static", ["tag" => "div" ' . $val . ', "label" => "' . $title . '" , "label_attr" => ["class" => "control-label ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . '])';
                break;
            case 'text_datetime':
                $val = ($val) ? $val : ",'value' => CrudHelper::formatDateTime(\$this->model->" . $fieldInfo['field'] . ")";
                $form = $addType.'"' . $fieldInfo['field'] . '" , "static", ["tag" => "div" ' . $val . ', "label" => "' . $title . '" , "label_attr" => ["class" => "control-label ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . '])';
                break;
            case 'tags':
                $val = ",'value' => CrudHelper::getTagValues(\$this->model,'" . $fieldInfo['field'] . "')";
                $form = $addType.'"' . $fieldInfo['field'] . '" , "static", ["tag" => "div" ' . $val . ', "label" => "' . $title . '" , "label_attr" => ["class" => "control-label ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . '])';
                break;
            case 'repeater':
                $repeaterData = Arr::get($fieldInfo, 'repeater_data');
                if ($repeaterData) {                   
                    $form = '->add("' . $fieldInfo['field'] . '" , "repeater", ' . $repeaterData . ')';
                }
                break;
            default:
                $form = $addType.'"' . $fieldInfo['field'] . '" , "static", ["tag" => "div" ' . $val . ', "label" => "' . $title . '" , "label_attr" => ["class" => "control-label ' . $labelCls . '"]' . $wrapperClsAtrr . $inputClsAttr  . '])';
                break;
        }

        $restrictTo = (Arr::get($fieldInfo, 'option.restricted_role_id.0')) ? true : false;
        $hiddenConfig = (is_array(Arr::get($fieldInfo, 'option.hidden'))) ? implode("|", array_keys(Arr::get($fieldInfo, 'option.hidden'))) : "";

        if ($restrictTo || $hiddenConfig) {
            $restictedRoleId = ($restrictTo) ? implode("|", Arr::get($fieldInfo, 'option.restricted_role_id')) : "";
            $fieldAccessType = Arr::get($fieldInfo, 'option.field_access_type');
            $form = ";\r\n\t\t\t if(CrudHelper::isFieldVisible('" . $restictedRoleId . "','" . $fieldAccessType . "','" . $hiddenConfig . "')) {\$this" . $form . ";}\r\n\t\t\t \$this";
        }
        $form .= ($skipNextLine) ? "" : "\r\n\t\t\t";
        return $form;
    }

    public function parseCrudGridData(): array
    {
        $gridConfig = $this->crudModuleConfig['grid'];
        $grid = $gridColumns = $gridColumnConfig = $columnLookup = [];
        $tableClsMapping = ['left' => "text-left", 'right' => "text-right", 'center' => "text-center"];
        $this->formConfigData = $this->getFormConfigData();
        usort($gridConfig, "self::_sort");

        foreach ($gridConfig as $key => $fieldInfo) {
            if (Arr::get($fieldInfo, 'view') || Arr::get($fieldInfo, 'field') == 'id') {
                $gridColumns[] = ($fieldInfo['alias']) ? "'" . $fieldInfo['alias'] . '.' . $fieldInfo['field'] . "'" : "'" . $fieldInfo['field'] . "'";
                $config = [];
//                $field = ($fieldInfo['alias']) ? $fieldInfo['alias'] . '.' . $fieldInfo['field'] : $fieldInfo['field'];
                $config[] = "'name' => '" . $fieldInfo['field'] . "'";
                $config[] = "'title' => '" . $fieldInfo['label'] . "'";
                $config[] = "'width' => '" . $fieldInfo['width'] . "'";
                $config[] = "'class' => '" . $tableClsMapping[$fieldInfo['align']] . "'";
                $this->gridConfigData[$fieldInfo['field']] = $fieldInfo;

                if (!Arr::get($fieldInfo, 'search')) {
                    $config[] = "'searchable' => '" . $fieldInfo['searchable'] . "'";
                }

                if (!Arr::get($fieldInfo, 'sortable')) {
                    $config[] = "'orderable' => " . $fieldInfo['sortable'];
                }

                if (!Arr::get($fieldInfo, 'download')) {
                    $config[] = "'exportable' => " . $fieldInfo['download'];
                }

                $fieldVisibility = Arr::get($fieldInfo, 'visibility');

                if (!$fieldVisibility) {
                    $config[] = "'visible' => false";
                }

                if (Arr::get($fieldInfo, 'format_as') && $fieldInfo['format_as'] == 'workflow') {
                    $config[] = "'class' => '" . $tableClsMapping[$fieldInfo['align']] . " workflow-dropdown'";
                }

                if($fieldInfo['field'] != 'id') {
                    $gridColumnConfig[] = "'" . $fieldInfo['field'] . "' => [\r\n\t\t\t" . implode(",\r\n\t\t\t", $config) . "\r\n\t\t\t]";
                }

                $lookupStr = $this->getLookupData($fieldInfo);
                if ($lookupStr) {
                    $columnLookup[] = $lookupStr;
                }
            }
        }
        $grid['columns'] = implode(",\r\n\t\t\t", $gridColumns);
        $grid['columnConfig'] = implode(",\r\n\t\t\t", $gridColumnConfig);
        $grid['columnLookup'] = implode("\r\n\t\t\t", $columnLookup);
        return $grid;
    }

    /**
     * To construct DB migration data
     */
    public function buildMigrationData($type, $fieldInfo, $skipNextLine = false, $modify = false, $after = false)
    {
        $fieldName = strtolower($fieldInfo['field']);
        $schema = '';
        $length = 191;

        if (in_array($fieldName, array('updated_at', 'created_at', 'deleted_at'))) {
            return $schema;
        }

        preg_match('#\((.*?)\)#', $type, $match);
        if (is_array($match) && count($match) > 0) {
            $type = str_replace($match[0], "", $type);
            $length = $match[1];
        }

        switch ($type) {
            case 'hidden':
                $schema = '$table->' . $fieldName . '()';
                break;
            case 'int':
                $schema = '$table->integer("' . $fieldName . '")';
                break;
            case 'varchar':
                $schema = '$table->string("' . $fieldName . '","' . $length . '")';
                break;
            case 'text':
            case 'json':
            case 'date':
            case 'boolean':
            case 'bigint':
            case 'timestamp':
                $schema = '$table->' . $type . '("' . $fieldName . '")';
                break;
            case 'tinyint':
                $schema = '$table->tinyInteger("' . $fieldName . '")';
            case 'bigint':
                $schema = '$table->bigInteger("' . $fieldName . '")';
                break;
            case 'longtext':
                $schema = '$table->longText("' . $fieldName . '")';
                break;
            case 'time':
                $schema = '$table->time("' . $fieldName . '")';
                break;
            case 'decimal':
            case 'double':
            case 'float':
                list($precision, $scale) = explode(",", $length);
                $schema = '$table->' . $type . '("' . $fieldName . '","' . $precision . '","' . $scale . '")';
                break;
            default:
                $schema = '$table->string("' . $fieldName . '")';
                break;
        }

        $modifyText = ($modify) ? "->change()" : "";
        $afterText = ($after) ? "->after('" . $after . "')" : "";
        $schema .= (isset($fieldInfo['isNullable']) && $fieldInfo['isNullable'] === 'YES') ? "->nullable()" : "";
        $schema .= (isset($fieldInfo['defaultVal']) && !is_null($fieldInfo['defaultVal']) && $fieldInfo['defaultVal'] !== '') ? "->default('" . $fieldInfo['defaultVal'] . "')" . $afterText . $modifyText . ";" : $afterText . $modifyText . ";";
        $schema .= ($skipNextLine) ? "" : "\r\n\t\t\t";
        return $schema;
    }

    public function getLookupData($fieldInfo)
    {
        $html = "";
        $formatAs = Arr::get($fieldInfo, 'format_as');
        $isEdit = Arr::get($fieldInfo, 'edit');
        $this->isInlineEdit = (!$this->isInlineEdit) ? $isEdit : $this->isInlineEdit;
        $field = $fieldInfo["field"];
        $formFieldInfo = Arr::get($this->formConfigData, $field);
        $type = Arr::get($formFieldInfo, 'type');

        if ($isEdit) {
            $choices = "";

            if ($type == 'select') {
                $lookupTable = Arr::get($formFieldInfo, 'option.lookup_table');
                $lookupKey = Arr::get($formFieldInfo, 'option.lookup_key');
                $lookupValue = Arr::get($formFieldInfo, 'option.lookup_value');
                $depedentKey = Arr::get($formFieldInfo, 'option.lookup_dependency_key');
                $whereCondn = Arr::get($formFieldInfo, 'option.where_cndn');
                $optionType = Arr::get($formFieldInfo, 'option.opt_type');
                $optionQry = Arr::get($formFieldInfo, 'option.lookup_query');
                $choices = ",'choices' => CrudHelper::getSelectOptionValues('" . $optionType . "', '" . $optionQry . "', '" . $lookupTable . "', '" . $lookupKey . "', '" . $lookupValue . "', '" . $whereCondn . "', '" . $depedentKey . "', \$this->model)";
            }

            $html = "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\t\$options = ['value' => \$item->" . $field . ",'key' => \$item->id, 'type' => '" . $type . "'" . $choices . "]; \r\n\t\t\t\treturn CrudHelper::getCustomFields('" . $field . "', \$options,\$this->repository->getModel());\r\n\t\t\t})";
        } else if ($field == 'entity_id') {
            return "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\tif(!isset(\$item->entity_type_id)) {
                return '';
            }
            return CrudHelper::formatEntityValue(\$item->entity_type_id, \$item->entity_id);\r\n\t\t\t})";
        } else if ($field == 'entity_type') {
            return "->editColumn('entity_type', function (\$item) {
                return \$item->entity_type_text;
            })";
        } else if ($formatAs) {
            $conn = (Arr::get($fieldInfo, 'conn.valid') == 1 && Arr::get($fieldInfo, 'conn.db')) ? implode(':', $fieldInfo['conn']) : null;
            $formatValue = Arr::get($fieldInfo, 'format_value');
            $html = "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\treturn CrudHelper::formatRows(\$item->" . $fieldInfo['field'] . ", '" . $formatAs . "', '" . $formatValue . "', \$item, '" . $conn . "');\r\n\t\t\t})";
        } else if ($type == 'text_date') {
            $html = "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\treturn CrudHelper::formatDate(\$item->" . $fieldInfo['field'] . ");\r\n\t\t\t})";
        } else if ($type == 'text_datetime') {
            $html = "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\treturn CrudHelper::formatDateTime(\$item->" . $fieldInfo['field'] . ");\r\n\t\t\t})";
        } else if (Arr::get($fieldInfo, 'conn.valid') == 1) {
            $conn = (Arr::get($fieldInfo, 'conn.db')) ? implode(':', $fieldInfo['conn']) : null;
            $html = "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\treturn CrudHelper::formatLookupValue(\$item->" . $fieldInfo['field'] . ", '" . $conn . "');\r\n\t\t\t})";
        } elseif ($type == 'tags') {
            $html = "->editColumn('" . $field . "', function(\$item) { \r\n\t\t\t\treturn CrudHelper::getTagValues(\$item,'" . $fieldInfo['field'] . "');\r\n\t\t\t})";
        }

        if ($this->moduleDBName != Arr::get($fieldInfo, 'alias')) {
            $formatValue = Arr::get($fieldInfo, 'format_value');
            $fields = explode("|", $formatValue);
            $fieldToShow = Arr::get($fieldInfo, 'alias') . "." . $field;
            if (count($fields) >= 2) {
                $alias = Arr::get($fieldInfo, 'alias') ? $fieldInfo['alias'] : $fields[0];
                $fieldToShow = $alias . "." . $fields[2];
            }
            $html .= "
            ->filterColumn('" . $field . "', function(\$query, \$keyword) {
                \$sql = '" . $fieldToShow . "  like ?';
				\$query->whereRaw(\$sql, [" . '"' . '%{$keyword}%' . '"' . "]);
            })";
        }

        return $html;
    }

    public function sameAsBlockHTML($blockIndex = 0)
    {
        $saveAsBlock = Arr::get($this->sameAsAboveConfig, $blockIndex);
        $html = "";

        if ($blockIndex && $saveAsBlock) {
            $field = Arr::get($saveAsBlock, 'field');
            $html = '->add("' . $field . '" , "checkbox", ["label" => "' . $saveAsBlock['label'] . '", "label_attr" => ["class" => "control-label"],'
                . '"attr" => ["class" => "control-label saveAsCopy"],"wrapper" => ["class" => "form-group pull-right"]])';
        }

        return $html;
    }

    public function reassignFormColumnIndex($newIndex)
    {
        $moduleConfig = $this->crudModuleConfig;
        $forms = $moduleConfig['forms'];
        $output = [];

        foreach ($forms as $form) {
            $colIndex = intval(Arr::get($form, 'form_group'));
            if ($newIndex <= $colIndex) {
                $form['form_group'] = $colIndex + 1;
            }
            $output[] =  $form;
        }

        return $output;
    }

    public function toBuildForm($replaceText, $isView = false)
    {
        $moduleConfig = $this->crudModuleConfig;
        $forms = $moduleConfig['forms'];
        $layout = Arr::get($moduleConfig, 'form_layout');
        if (!$layout) {
            return $this->parseCrudFormData($replaceText, $isView);
        }
        $crudForm = $mainBlockOpen = $mainBlockClose = "";
        usort($forms, "self::_sort");
        $block = $layout['column'];
        $format = $layout['format'];
        $display = $layout['display'];
        $title = explode(",", $layout['title']);
        $subformTab = [];
        $method = ($isView) ? 'viewForm' : 'buildForm';
        $restrictedTab = [];

        if ($this->isValidArray($this->subformWithNewTab)) {
            foreach ($this->subformWithNewTab as $value) {
                $fieldAfter = Arr::get($value, 'field_after');
                if ($fieldAfter) {
                    $colIndex = intval(Arr::get($this->formConfigData,  $fieldAfter . '.form_group'));
                    $colIndex += 1;
                    $block += 1;
                    $subformTab[$colIndex] = $value;
                    $forms = $this->reassignFormColumnIndex($colIndex);
                    array_splice($title, $colIndex, 0, [$value['title']]);
                } else {
                    $subformTab[$block] = $value;
                    $title[] = $value['title'];
                    $block += 1;
                }
                $restrictedRoleId = Arr::get($value, 'restricted_role_id');
                $restrictedTab[$value['title']] = ($restrictedRoleId) ? implode("|", $restrictedRoleId) : "";
            }
        }

        if ($format == 'tab') {
            $mainBlockOpen .= "<ul class='nav nav-tabs form-tab'>";

            for ($i = 0; $i < $block; $i++) {
                $active = ($i == 0 ? 'active' : '');
                $tit = (isset($title[$i]) ? $title[$i] : 'None');
                $subBlock = intval(substr($tit, strpos($tit, "|") + 1));
                $tit = ($subBlock > 0) ? str_replace("|$subBlock", "", $tit) : $tit;
                $mainBlockOpen .= "<li class='nav-item'><a href='#" . trim(str_replace(' ', '', $tit)) . "' data-toggle='tab' class='nav-link " . $active . "'>" . $tit . "</a></li>";
            }

            $mainBlockOpen .= "</ul>";
        }

        if ($format == 'tab') $mainBlockOpen .= "<div class='tab-content'>";
        if ($format == 'wizzard') $mainBlockOpen .= "<div id='wizard-step' class='wizard-circle number-tab-steps'>";
        if ($format == 'grid' || $format == "groupped") $mainBlockOpen .= "<div class='row'>";
        $crudForm .= $this->buildForm('html', ['html' => $mainBlockOpen, 'field' => 'html'], false, 'custom_html_main_open');

        for ($i = 0; $i < $block; $i++) {
            $fieldIndex = 0;
            $rowBlockOpen = $rowBlockClose = "";
            $class = Arr::has($this->blockConfig, $block) ? Arr::get($this->blockConfig, $block) : 'col-md-12';
            $tit = (isset($title[$i]) ? $title[$i] : 'None');
            $subBlock = intval(substr($tit, strpos($tit, "|") + 1));
            $tit = ($subBlock > 0) ? str_replace("|$subBlock", "", $tit) : $tit;
            preg_match("/\[(.*?)\]/", $tit, $replace);
            if (Arr::get($replace, 0)) {
                $tit = ($tit) ? str_replace($replace[0], "", $tit) : $tit;
            }

            $groupCls = Arr::get($replace, 1);
            $groupCls = ($groupCls) ? $groupCls : Str::slug($replaceText,'_');
            $restrictTo = Arr::get($restrictedTab, $tit);
            $fieldAccessType = Arr::get($restrictedTab, 'option.field_access_type');
            $crudForm .= ($restrictTo) ? ";\r\n\t\t\t if(CrudHelper::isFieldVisible('" . $restrictTo . "','" . $fieldAccessType . "')) {\r\n\t\t\t \$this" : "";
            $legend = ($tit) ? "<legend> " . $tit . "</legend>" : "";
            $groupedLegend = ($tit) ? "<legend class='grouppedLegend'> " . $tit . "</legend>" : "";
            // Grid format
            if ($format == 'grid') {
                $subBlock = 0;
                $rowBlockOpen .= "<div class='gridLayout " . $class . "'>
						<fieldset>" . $legend;
            } else if ($format == "groupped") {
                $sameAsBlock = $this->sameAsBlockHTML($i);
                if ($sameAsBlock) {
                    $rowBlockOpen .= "<div class='col-md-12 grouppedLayout $groupCls'>
                    <fieldset><legend class='grouppedLegend'> " . $tit;
                    $crudForm .= $this->buildForm('html', ['html' => $rowBlockOpen, 'field' => 'html'], false, 'custom_html_open_1' . $i);
                    $crudForm .= $sameAsBlock . "\r\n\t\t\t";
                    $rowBlockOpen = "</legend>";
                } else {
                    $rowBlockOpen .= "<div class='col-md-12 grouppedLayout $groupCls'>
                    <fieldset>" . $groupedLegend;
                }

                $rowBlockOpen .= ($subBlock > 0) ? '<div class=\'row\'>' : '';
            } else if ($format == "wizzard") {
                $rowBlockOpen .= ' <h3>' . $tit . '</h3> <section class=\'wizardLayout\'> ';
                $rowBlockOpen .= ($subBlock > 0) ? '<div class=\'row\'>' : '';
            } else if ($format == "tab") {
                $active = ($i == 0 ? 'active' : '');
                $rowBlockOpen .= "<div class='tab-pane tabLayout container m-t " . $active . "' id='" . trim(str_replace(' ', '', $tit)) . "'>
				";
                $rowBlockOpen .= ($subBlock > 0) ? '<div class=\'row\'>' : '';
            } else {
                $rowBlockOpen .= '<div>';
                $rowBlockOpen .= ($subBlock > 0) ? '<div class=\'row\'>' : '';
            }

            $crudForm .= $this->buildForm('html', ['html' => $rowBlockOpen, 'field' => 'html'], false, 'custom_html_open_' . $i);
            $subformTabContent = Arr::get($subformTab, $i);
            $specialNotes = Arr::get($moduleConfig, 'setting.special_notes');

            if (!$isView && $specialNotes && $i == 0) {
                $specialNotesHtml = "<div class='note note-success col-md-12'><p>" . $specialNotes . "</p></div>";
                $crudForm .= $this->buildForm('html', ['html' => $specialNotesHtml, 'field' => 'html'], false, 'specialNotesHtml');
            }

            if ($subformTabContent) {
                $crudForm .= $this->loadSubForm($replaceText, [$subformTabContent], false);
            } else {
                foreach ($forms as $key => $fieldInfo) {
                    if ($fieldInfo['form_group'] == $i && Arr::get($fieldInfo, 'view') &&  Arr::get($fieldInfo, 'option.opt_type') != 'sameAsAbove') {
                        $fieldIndex = ($fieldInfo['type'] == 'checkbox') ? $fieldIndex : $fieldIndex + 1;
                        $crudForm .= $this->$method($fieldInfo['type'], $fieldInfo, false, '', $display, $subBlock, $fieldIndex);
                    }
                }
                if(empty($this->subformData) && $isView){
                    $crudForm.=$this->getJoinFieldsToView($moduleConfig['grid']);
                }
            }

            // Render form fields at the end of the layout
            if ($i == ($layout['column'] - 1)) {
                $crudForm .= '->add("display_layout_type" , "html", ["html" => "<span class=\'layoutDisplayType\' data-display_type = \'' . $display . '\'></span> "])';
            }

            if ($format == 'grid') {
                $rowBlockClose .= '</fieldset></div>';
            } else if ($format == 'wizzard') {
                $rowBlockClose .= ($subBlock > 0) ? '</div>' : '';
                $rowBlockClose .= '</section>';
            } else {
                $rowBlockClose .= ($subBlock > 0) ? '</div>' : '';
                $rowBlockClose .= '</div>';
            }

            if (!$isView && $i == $block - 1) {
                $crudForm .= $this->getFormCaptchaTemplate();
            }

            $crudForm .= $this->buildForm('html', ['html' => $rowBlockClose, 'field' => 'html'], false, 'custom_html_close_' . $i);
            $crudForm .= ($restrictTo) ? ";\r\n\t\t\t } \r\n\t\t\t \$this " : "";

            // Render form fields at the end of the layout
            if ($i == $block - 1) {
                $crudForm .= $this->loadSubForm($replaceText, $this->subformData, true, $subBlock);
            }
        }

        if ($format == 'wizzard' || $format == 'tab') $mainBlockClose .= '</div>';
        if ($format == 'grid' || $format == "groupped") $mainBlockClose .= '</div>';

        $crudForm .= $this->buildForm('html', ['html' => $mainBlockClose, 'field' => 'html'], false, 'custom_html_main_close');
        return $crudForm;
    }

    /**
     * @param string plugin
     */
    protected function getSubModuleModuleActions($plugin)
    {
        $cndns = ['id' => $this->moduleId];
        $result = DB::table('cruds')->select(['id', 'module_name', 'module_actions'])->where($cndns)->orWhere(['parent_id' => $this->moduleId])->get();
        $submoduleExist = Arr::has($result, 1) ? true : false;
        $defaultAction = ['index' => 1];
        $actions = [];

        foreach ($result as $k => $val) {
            $modulePermission = json_decode($val->module_actions, 1);

            if ($this->isValidArray($modulePermission)) {
                $modulePermission = array_merge($defaultAction, $modulePermission);
                $moduleCamel = str_replace(
                    '_',
                    ' ',
                    ucfirst(Str::plural(Str::snake(str_replace('-', '_', $val->module_name))))
                );
                $module = strtolower($val->module_name);

                foreach ($modulePermission as $action => $value) {
                    if($action != 'hide_operations'){
                    if ($val->id == $this->moduleId) {
                        if ($action == 'index' && $submoduleExist) {
                            $actions[] = "[
                            'name' => '" . $moduleCamel . "',
                            'flag' => 'plugins." . $module . "'
                        ],[
                            'name' => '" . $moduleCamel . "',
                            'flag' => '" . $module . ".index',
                            'parent_flag' => 'plugins." . $module . "'
                        ]";
                        } elseif ($action == 'index' && !$submoduleExist) {
                            $actions[] = "[
                                'name' => '" . $moduleCamel . "',
                                'flag' => '" . $module . ".index'
                            ]";
                        } else {
                            $permissionStr = $this->getPermissionStr($module, $moduleCamel, $action, $value, $plugin);
                            if ($permissionStr) {
                                $actions[] = $this->getPermissionStr($module, $moduleCamel, $action, $value, $plugin);
                            }
                        }
                    } else {
                        $permissionStr = $this->getPermissionStr($module, $moduleCamel, $action, $value, $plugin);
                        if ($permissionStr) {
                            $actions[] = $this->getPermissionStr($module, $moduleCamel, $action, $value, $plugin);
                        }
                    }
                    }
                }
            }
        }

        return ($this->isValidArray($actions)) ? implode(",\r\n\t\t\t", $actions) . "\r\n\t\t\t" : "";
    }

    public function getPermissionStr($module, $moduleCamel, $action, $value, $plugin)
    {
        $str = "";
        $actionTitle = ($action == 'destroy') ? 'delete' : $action;
        if ($action == 'index') {
            $actionTitle = $moduleCamel;
        }

        $actionTitle = ucfirst($actionTitle);
        $parentFlag = ($action == 'index') ? 'plugins.' . $plugin : $module . '.index';
        if ($value) {
            $str = "[
            'name' => '" . $actionTitle . "',
            'flag' => '" . $module . "." . $action . "',
            'parent_flag' => '" . $parentFlag . "',
            ]";
        }
        return $str;
    }

    /**
     * @param string plugin
     * @param string submoduleName
     */
    protected function getSubModuleDetails($plugin, $submoduleName = null)
    {
        $submodule = $registerList = $regiserClass = $menus = $routes = $constants = $publicRoutes = $shortcode = $shortcodeFnCall = [];
        $registerExtMod = [];
        $cndns = ['parent_id' => $this->moduleId];
        if ($submoduleName) {
            $cndns['module_name'] = $submoduleName;
        }
        $result = \DB::table('cruds')->select('module_name', 'module_config', 'is_multi_lingual')->where($cndns)->get();

        if (count($result) <= 0) {
            $submodule['mainMenu'] = "#{mainmodule_menu}";
            $submodule['registerList'] = "#{register_sub_module}";
            $submodule['regiserClass'] = "#{register_submodule_class}";
            $submodule['registerExtMod'] = "#{register_external_submodule}";
            $submodule['menus'] = "#{submodule_menus}";
            $submodule['routes'] = "#{submodule_routes}";
            $submodule['moduleFlag'] = "#{module_flag}";
            $submodule['constants'] = "#{submodule_constants}";
            $submodule['pulicRoutes'] = "#{submodule_public_routes}";
            $submodule['shortcode'] = "#{render_short_code_method}";
            $submodule['shortcodeFnCall'] = "#{render_short_code_method_call}";
            return $submodule;
        }

        foreach ($result as $row) {
            if ($this->isSubModuleExist($row->module_name, $plugin)) {
                $subModuleConfig = CF_decode_json($row->module_config);
                $registerList[] = $this->getSubModuleRegisterDetails($row->module_name, $plugin);
                if ($row->is_multi_lingual) {
                    $regiserClass[] = $this->getSubModuleRegisterClass($row->module_name, $plugin);
                }
                $registerExtMod[] = $this->getExternalRegisterModule($row->module_name, $plugin);
                $menus[] = $this->getSubModuleMenus($row->module_name, $plugin, '', $subModuleConfig);
                $routes[] = $this->getSubModuleRoutes($row->module_name, $plugin);
                $constants[] = $this->getSubModuleConstants($row->module_name);
                $shortcode[] = $this->getSubModuleShortcode($row->module_name, $plugin);
                $publicRoutes[] = $this->getSubModulePublicRoutes($row->module_name, $plugin);
                $shortcodeFnCall[] = $this->getSubModuleShortcodeFnCall($row->module_name, $plugin);
            }
        }

        $submodule['mainMenu'] = "#{mainmodule_menu}";

        if ($this->isValidArray($menus)) {
            $submodule['mainMenu'] = $this->getSubModuleMenus($plugin, $plugin, '_1') . "\r\n\t\t\t";
        }

        $submodule['registerList'] = ($this->isValidArray($registerList)) ? implode("\r\n\t\t\t", $registerList) . "\r\n\t\t\t#{register_sub_module}" : "#{register_sub_module}";
        $submodule['regiserClass'] = ($this->isValidArray($regiserClass)) ? implode("\r\n\t\t\t", $regiserClass) . "\r\n\t\t\t#{register_submodule_class}" : "#{register_submodule_class}";
        $submodule['registerExtMod'] = ($this->isValidArray($registerExtMod)) ? implode("\r\n\t\t\t", $registerExtMod) . "\r\n\t\t\t#{register_external_submodule}" : "#{register_external_submodule}";
        $submodule['menus'] = ($this->isValidArray($menus)) ? implode("\r\n\t\t\t", $menus) . "\r\n\t\t\t#{submodule_menus}" : "#{submodule_menus}";
        $submodule['routes'] = ($this->isValidArray($routes)) ? implode("\r\n\t\t\t", $routes) . "\r\n\t\t\t#{submodule_routes}" : "#{submodule_routes}";
        $submodule['moduleFlag'] = (isset($moduleFlag)) ? "\r\t" . $moduleFlag  : "#{module_flag}";
        $submodule['constants'] = ($this->isValidArray($constants)) ? implode("\r\n\r\n", $constants) . "\r\n#{submodule_constants}" : "#{submodule_permission}";
        $submodule['shortcode'] = ($this->isValidArray($shortcode)) ? implode("\r\n\t\t\t", $shortcode) . "\r\n\t\t\t#{render_short_code_method}"  : "#{render_short_code_method}";
        $submodule['shortcodeFnCall'] = ($this->isValidArray($shortcodeFnCall)) ? implode("\r\n\t\t\t", $shortcodeFnCall) . "\r\n\t\t\t#{render_short_code_method_call}"  : "#{render_short_code_method_call}";
        $submodule['publicRoutes'] = ($this->isValidArray($publicRoutes)) ? implode("\r\n\t\t\t", $publicRoutes) . "\r\n\t\t\t#{submodule_public_routes}"  : "#{submodule_public_routes}";

        return $submodule;
    }

    /**
     * @param string plugin
     */
    protected function getSubformDetails($plugin, $mainModule)
    {
        $subform = $subformRelFns = $subformCreateScript = $subformUpdateScript = $validations = $validationMsg = [];

        if (!$this->isValidArray($this->subformData)) {
            return false;
        }

        foreach ($this->subformData as $row) {
            $subformRelFns[] = $this->getSubformRelFns($row, $mainModule);
            $subformCreateScript[] = $this->getSubformSaveScript($row);
            $subformUpdateScript[] = $this->getSubformSaveScript($row, true);
            $validations[] = $this->getSubformValidationsScript($row);
            $validationMsg[] = $this->getSubformValidationMsg($row);
        }

        $subform['subformRelFns'] = ($this->isValidArray($subformRelFns)) ? implode("\r\n\r\n\t", $subformRelFns) . "\r\n\r\n\t" : "";
        $subform['subformCreateScript'] = ($this->isValidArray($subformCreateScript)) ? implode("\r\n\t\t", $subformCreateScript) . "\r\n\t\t" : "";
        $subform['subformUpdateScript'] = ($this->isValidArray($subformUpdateScript)) ? implode("\r\n\t\t", $subformUpdateScript) . "\r\n\t\t" : "";
        $subform['validations'] = ($this->isValidArray($validations)) ? implode("\r\n\t\t", $validations) . "\r\n\t\t" : "";
        $subform['validationMsg'] = ($this->isValidArray($validationMsg)) ? implode("\r\n\t\t", $validationMsg) . "\r\n\t\t" : "";
        return $subform;
    }

    protected function getsubformWithNewTabProp()
    {
        if (!$this->isValidArray($this->subformData)) {
            return [];
        }

        $newTabs = array_filter($this->subformData, function ($value) {
            return (Arr::get($value, 'is_new_tab')) ? true : false;
        });

        return array_values($newTabs);
    }

    protected function isSubModuleExist($name, $plugin)
    {
        $location = plugin_path($plugin);
        $file = $location . '/src/Http/Controllers/' . ucfirst(Str::camel($name)) . 'Controller.php';
        if (File::isFile($file)) {
            return true;
        }

        return false;
    }

    public function isValidArray($inpArr)
    {
        if (isset($inpArr) && is_array($inpArr) && count($inpArr) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getSubModuleRegisterDetails($subModuleName, $plugin)
    {
        $str = '$this->app->bind(\Impiger\{Module}\Repositories\Interfaces\{Name}Interface::class, function () {
            return new \Impiger\{Module}\Repositories\Caches\{Name}CacheDecorator(
                new \Impiger\{Module}\Repositories\Eloquent\{Name}Repository(new \Impiger\{Module}\Models\{Name})
            );
        });';
        $search = array('{Module}', '{Name}');
        $replace = array(ucfirst(Str::camel($plugin)), ucfirst(Str::camel($subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getSubModuleRegisterClass($subModuleName, $plugin)
    {
        $str = '\Language::registerModule([\Impiger\{Module}\Models\{Name}::class]);';
        $search = array('{Module}', '{Name}');
        $replace = array(ucfirst(Str::camel($plugin)), ucfirst(Str::camel($subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getExternalRegisterModule($subModuleName, $plugin)
    {
        $moduleConfig = $this->crudModuleConfig;
        $isGallery = Arr::get($moduleConfig, 'setting.is_gallery_image');
        if (!$isGallery) {
            return "";
        }
        $str = 'if (defined("GALLERY_MODULE_SCREEN_NAME") && is_plugin_active("gallery")) {\Gallery::registerModule([\Impiger\{Module}\Models\{Name}::class]);}';
        $search = array('{Module}', '{Name}');
        $replace = array(ucfirst(Str::camel($plugin)), ucfirst(Str::camel($subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     * @param string suffix
     */
    protected function getSubModuleMenus($subModuleName, $plugin, $suffix = '', $subModuleConfig = [])
    {
        $menuOrder = (Arr::get($subModuleConfig, 'setting.menu_priority')) ?: 0;
        $isHide = (Arr::get($subModuleConfig, 'setting.hide_menu')) ?: 0;
        if ($isHide) {
            return "";
        }
        $suffix = ($plugin == 'master-detail') ? '-main' : $suffix;
        $str = "dashboard_menu()->registerItem([
            'id'          => 'cms-{types}-{-name}" . $suffix . "',
            'priority'    => " . $menuOrder . ",
            'parent_id'   => 'cms-{types}-{-module}',
            'name'        => '{types}/{-module}::{-name}.name',
            'icon'        => null,
            'url'         => route('{-name}.index'),
            'permissions' => ['{-name}.index'],
        ]);";
        $search = array('{types}', '{-name}', '{-module}');
        $replace = array('plugins', strtolower($subModuleName), strtolower($plugin));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getSubModuleRoutes($subModuleName, $plugin)
    {
        $moduleConfig = $this->crudModuleConfig;
        $galleryRoutes = "";

        $hideMenu = Arr::get($moduleConfig, 'setting.hide_menu');
        if ($hideMenu) {
            return false;
        }
        if (Arr::get($moduleConfig, 'setting.is_gallery_image')) {
            $galleryRoutes = "Route::get('viewgallery/{any}', [
                'uses'       => '{Name}Controller@viewGallery',
                'permission' => '{-name}.index',
            ]);";
        }
        $str = "Route::group(['prefix' => '{-names}', 'as' => '{-name}.'], function () {
            Route::resource('', '{Name}Controller')->parameters(['' => '{-name}']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => '{Name}Controller@deletes',
                'permission' => '{-name}.destroy',
            ]);
            Route::get('viewdetail/{any}', [
                'uses'       => '{Name}Controller@viewdetail',
                'permission' => '{-name}.index',
            ]);
            " . $galleryRoutes . "
             Route::post('import', [
                'as'         => 'import',
                'uses'       => '{Name}Controller@postImport',
                'permission' => '{-name}.index',
				]);
        });";
        $search = array('{-names}', '{-name}', '{Name}');
        $replace = array(Str::plural($subModuleName), strtolower($subModuleName), ucfirst(Str::camel($subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getSubModulePublicRoutes($subModuleName, $plugin)
    {
        $str = "Route::post('{-name}/postdata', [
            'as'   => 'public.{-name}.postdata',
            'uses' => '{Name}PublicController@postData',
        ]);
        Route::post('{-name}/updatedata/{any}', [
            'as'   => 'public.{-name}.updatedata',
            'uses' => '{Name}PublicController@updateData',
        ]);
        Route::get('{-name}', [
            'as'   => 'public.{-name}.index',
            'uses' => '{Name}PublicController@index',
        ]);";
        $search = array('{-name}', '{Name}');
        $replace = array(strtolower($subModuleName), ucfirst(Str::camel($subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getSubModuleShortcode($subModuleName, $plugin)
    {
        $crud = \App\Models\Crud::where('module_name', $subModuleName)->select(['id', 'shortcode_options'])->first();
        $formOptions = '';
        if ($crud && $crud->shortcode_options && Arr::has($crud->shortcode_options, 0)) {
            foreach ($crud->shortcode_options as $options) {
                if (Arr::has($options, 'form')) {
                    $formOptions = "\$choices = \App\Utils\CrudHelper::getshortcodeChoises(explode(',',\$shortCode->" . $options['form']['param_name'] . "));"
                        . "\n\t\t \$method->modify('" . $options['form']['field_name'] . "','customSelect',['label_attr' => ['class' => 'control-label required '],'attr' => ['class' => 'select-full','data-field_index' => '0'],'choices'    => \$choices,'empty_value' => 'Select', 'rules' => 'required']);";
                }
            }
        }
        $str =
            "public function {name}BuildForm(\$shortCode)
        {
            \$this->loadFormAssets();
            if (\$shortCode->id) {
                \${name}Repository = new \Impiger\{Module}\Repositories\Eloquent\{Name}Repository(new \Impiger\{Module}\Models\{Name});
                \${name} = \${name}Repository->findOrFail(\$shortCode->id);
                \$method = \$this->formBuilder->create(\Impiger\{Module}\Forms\{Name}Form::class, ['model' => \${name}])
                    ->setFormOption('url', '{-name}/updatedata/' . \$shortCode->id);
            } else {
                \$method = \$this->formBuilder->create(\Impiger\{Module}\Forms\{Name}Form::class)
                    ->setFormOption('url', '{-name}/postdata');

            }" . $formOptions . "
            return \$method
                    ->setFormOption('template', 'theme-form.form-no-wrap')
                    ->setActionButtons(view('module.shortcodeactionbtn')->render())
                    ->renderForm();
        }

        public function {name}BuildTable(\$shortCode)
        {
            \$this->loadTableAssets();
            return \$this->table->setView('core/table::base-table')
            ->setOptions(['shortcode' => true])
            ->setHasFilter(false)
            ->setTableConfig(\$shortCode)
            ->setAjaxUrl(route('public.{name}.index'))
            ->renderTable();;
        }";
        $search = array('{-name}', '{Name}', '{Module}', '{name}');
        $replace = array(strtolower($subModuleName), ucfirst(Str::camel($subModuleName)), ucfirst(Str::camel($plugin)), Str::snake(str_replace('-', '_', $subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param string subModuleName
     * @param string plugin
     */
    protected function getSubModuleShortcodeFnCall($subModuleName, $plugin)
    {

        $formStr = "add_shortcode('{-name}-form', '{Name} Form', 'Add {Name} Form', [\$this, '{name}BuildForm']);";
        $listStr = "add_shortcode('{-name}-table', '{Name} Form', 'List {Name} Form', [\$this, '{name}BuildTable']);";
        $str = $formStr . "\n\t\t" . $listStr;
        $crud = \App\Models\Crud::where('module_name', $subModuleName)->select(['id', 'shortcode_options'])->first();

        if ($crud && $crud->shortcode_options && Arr::has($crud->shortcode_options, 0)) {
            foreach ($crud->shortcode_options as $options) {
                if (Arr::has($options, 'form')) {
                    $formStr .= "\n\t\t shortcode()->setAdminConfig('{-name}-form',view('partials.shortcodes-{name}-admin-config')->render());";
                    $str = $formStr . "\n\t\t" . $listStr;
                    $formOptions = "\$choices = \App\Utils\CrudHelper::getshortcodeChoises(explode(',',\$shortCode->" . $options['form']['param_name'] . "));"
                        . "\n\t\t \$method->modify('" . $options['form']['field_name'] . "','customSelect',['label_attr' => ['class' => 'control-label required '],'attr' => ['class' => 'select-full','data-field_index' => '0'],'choices'    => \$choices,'empty_value' => 'Select', 'rules' => 'required']);";
                    $shortcode['options'] = $formOptions;
                }
                if (Arr::has($options, 'list')) {
                    $listStr .= "\n\t\t shortcode()->setAdminConfig('{-name}-table',view('partials.shortcodes-{name}-admin-config')->render());";
                    $str = $formStr . "\n\t\t" . $listStr;
                }
            }
        }

        $search = array('{-name}', '{Name}', '{name}');
        $replace = array(strtolower($subModuleName), ucfirst(Str::camel($subModuleName)), Str::snake(str_replace('-', '_', $subModuleName)));
        $str = str_replace($search, $replace, $str);
        return $str;
    }
    /**
     * @param string moduleName
     * @param string plugin
     */
    protected function getModuleShortcodeFnCall($moduleName)
    {
        $shortcode = [];
        $formStr = "add_shortcode('{-name}-form-sc', '{Name} Form', 'Add {Name} Form', [\$this, '{name}BuildForm']);";
        $listStr = "add_shortcode('{-name}-list-sc', '{Name} Table', 'List {Name}', [\$this, '{name}BuildTable']);";
        $str = $formStr . "\n\t\t" . $listStr;
        $crud = \App\Models\Crud::where('module_name', $moduleName)->select(['id', 'shortcode_options'])->first();

        if ($crud && $crud->shortcode_options && Arr::has($crud->shortcode_options, 0)) {
            foreach ($crud->shortcode_options as $options) {
                if (Arr::has($options, 'form')) {
                    $formStr .= "\n\t\t shortcode()->setAdminConfig('{-name}-form-sc',view('partials.shortcodes-{name}-admin-config')->render());";
                    $str = $formStr . "\n\t\t" . $listStr;
                    $formOptions = "\$choices = \App\Utils\CrudHelper::getshortcodeChoises(explode(',',\$shortCode->" . $options['form']['param_name'] . "));"
                        . "\n\t\t \$method->modify('" . $options['form']['field_name'] . "','customSelect',['label_attr' => ['class' => 'control-label required '],'attr' => ['class' => 'select-full','data-field_index' => '0'],'choices'    => \$choices,'empty_value' => 'Select', 'rules' => 'required']);";
                    $shortcode['options'] = $formOptions;
                }
                if (Arr::has($options, 'list')) {
                    $listStr .= "\n\t\t shortcode()->setAdminConfig('{-name}-list-sc',view('partials.shortcodes-{name}-admin-config')->render());";
                    $str = $formStr . "\n\t\t" . $listStr;
                }
            }
        }

        $search = array('{-name}', '{Name}', '{name}');
        $replace = array(strtolower($moduleName), ucfirst(Str::camel($moduleName)), Str::snake(str_replace('-', '_', $moduleName)));
        $str = str_replace($search, $replace, $str);
        $shortcode['shortcode'] = $str;
        return $shortcode;
    }

    /**
     * @param string subModuleName
     */
    protected function getSubModuleConstants($subModuleName)
    {
        $str = "if(!defined('{NAME}_MODULE_SCREEN_NAME')) {
    define('{NAME}_MODULE_SCREEN_NAME', '{-name}');
}";

        $search = array('{NAME}', '{-name}');
        $replace = array(strtoupper(Str::snake(str_replace('-', '_', $subModuleName))), strtolower($subModuleName));
        $str = str_replace($search, $replace, $str);
        return $str;
    }

    /**
     * @param array row
     */
    protected function getSubformRelFns($row, $mainModule)
    {
        $subform = Arr::get($row, 'module');
        $master = Str::camel(Arr::get($row, 'master'));
        $key = Arr::get($row, 'key');
        $subFormWith_ = Str::plural(Str::snake(str_replace('-', '_', $subform)));
        $subformCls = ucfirst(Str::camel($subform));
        $plugin = ucfirst(Str::camel(strtolower($mainModule)));
        $methodStr = (Arr::get($row, 'module_relation') == 'many') ? "hasMany" : "hasOne";
        $localKey = Arr::get($row, 'master_key') ? Arr::get($row, 'master_key') : 'id';
        $customGroupKey = Arr::get($row, 'custom_group_key') ? Arr::get($row, 'custom_group_key') : '';


        if (Arr::get($row, 'module_relation') == 'custom_single') {
            $str = "public function " . $subFormWith_ . "() {
                return \$this->hasOne('Impiger\\" . $plugin . "\Models\\" . $subformCls . "', '" . $key . "', '" . $localKey . "')
                ->select(\DB::raw('CONCAT(" . '"["' . ",group_concat($customGroupKey), " . '"]"' . ") as $customGroupKey,$key'))
                ->groupBy('$key');
            }";

            $str .= "\r\n\t\t\t
            public function " . $subFormWith_ . "_multiple() {
                return \$this->hasMany('Impiger\\" . $plugin . "\Models\\" . $subformCls . "', '" . $key . "', '" . $localKey . "');
            }";
        } else {
            $str = "public function " . $subFormWith_ . "() {
                return \$this->$methodStr('Impiger\\" . $plugin . "\Models\\" . $subformCls . "', '" . $key . "', '" . $localKey . "');
            }";
        }

        return $str;
    }

    /**
     * @param array row
     * @param boolean isEdit
     */
    protected function getSubformSaveScript($row, $isEdit = false)
    {
        $subform = Arr::get($row, 'module');
        $master = str::camel(Arr::get($row, 'master'));
        $subFormWith_ = Str::plural(Str::snake(str_replace('-', '_', $subform)));
        if (Arr::get($row, 'module_relation') == 'custom_single') {
            $customGroupKey = Arr::get($row, 'custom_group_key');
            $str = "CrudHelper::createUpdateSubformAsMultiSelect(\$request, \$" . $master . ", '" . $subFormWith_ . "', '" . $customGroupKey . "');";
        } else {
            $methodStr = (Arr::get($row, 'module_relation') == 'many') ? "createUpdateSubformsHasMany" : "createUpdateSubforms";
            $isPreventDelete = (Arr::get($row, 'prevent_delete'));
            $preventDelete = '';
            if ($isPreventDelete) {
                $preventDelete = Arr::get($isPreventDelete, 'back_end') ? 'back_end' : 'front_end';
                if (Arr::get($isPreventDelete, 'back_end') && Arr::get($isPreventDelete, 'front_end')) {
                    $preventDelete = 'back_end|front_end';
                }
            }
            $str = ($isEdit) ? "CrudHelper::" . $methodStr . "(\$request, \$" . $master . ", '" . $subFormWith_ . "',\$id,'" . $preventDelete . "');" : "CrudHelper::" . $methodStr . "(\$request, \$" . $master . ", '" . $subFormWith_ . "',false,'" . $preventDelete . "');";
        }
        return $str;
    }

    /**
     * @param array row
     * @param boolean isEdit
     */
    protected function getSubformValidationsScript($row, $isEdit = false)
    {
        $mandatoryFields = '';
        $res = \DB::table('cruds')->where('module_name', $row['module'])->get()->first();

        if ($res->id) {
            $moduleConfig = CF_decode_json($res->module_config);
            $subFormWith_ = Str::plural(Str::snake(str_replace('-', '_', $row['module'])));
            $hasManyStr = (Arr::get($row, 'module_relation') == 'many') ? "*." : "";

            foreach ($moduleConfig['forms'] as $key => $fieldInfo) {
                if (!empty($fieldInfo['required'])) {
                    if ($hasManyStr) {
                        for ($i = 0; $i < 10; $i++) {
                            $fieldInfo['required'] = str_replace(".*.", ".$i.", $fieldInfo['required']);
                            $mandatoryFields .= "'" . $subFormWith_ . "." . $i . "." . $fieldInfo['field'] . "'=>'sometimes|" . $fieldInfo['required'] . "',";
                        }
                    } else {
                        $mandatoryFields .= "'" . $subFormWith_ . "." . $hasManyStr . $fieldInfo['field'] . "'=>'" . $fieldInfo['required'] . "',";
                    }
                    $mandatoryFields .= "\r\n\t\t";
                }
            }
        }

        return $mandatoryFields;
    }

    /**
     * @param array row
     * @param boolean isEdit
     */
    protected function getSubformValidationMsg($row, $isEdit = false)
    {
        $validationMsg = '';
        $res = \DB::table('cruds')->where('module_name', $row['module'])->get()->first();

        if ($res->id) {
            $moduleConfig = CF_decode_json($res->module_config);
            $subFormWith_ = Str::plural(Str::snake(str_replace('-', '_', $row['module'])));

            foreach ($moduleConfig['forms'] as $key => $fieldInfo) {
                if (!empty($fieldInfo['required'])) {

                    $msg = Arr::get($fieldInfo, 'option.validation_msg');
                    $hasManyStr = (Arr::get($row, 'module_relation') == 'many') ? "*." : "";
                    if ($msg) {
                        $msgList = explode("|", $msg);

                        foreach ($msgList as $val) {
                            $val = explode(":", $val);
                            if (Arr::has($val, '0') && Arr::has($val, '1')) {
                                $validationMsg .= "'" . $subFormWith_ . "." . $hasManyStr . $fieldInfo['field'] . "." . Arr::get($val, '0') . "'=>'" . Arr::get($val, '1') . "',";
                                $validationMsg .= "\r\n\t";
                            }
                        }
                    }
                }
            }
        }

        return $validationMsg;
    }

    /**
     * @param string moduleName
     */
    protected function loadSubForm($moduleName, $data, $onlyWithinTab = true, $subBlockCnt = 0)
    {
        if (!$this->isValidArray($data)) {
            return false;
        }

        $subForm = [];
        $moduleName = ucfirst(Str::camel($moduleName));
        $wrapperCls = ($subBlockCnt > 0) ? "col-md-12" : "";
        $plugin = Arr::get($this->argument(), 'plugin') ? ucfirst(Str::camel(strtolower(Arr::get($this->argument(), 'plugin')))) : ucfirst(Str::camel(strtolower($moduleName)));

        foreach ($data as $sub) {
            if (!$onlyWithinTab || ($onlyWithinTab && !Arr::get($sub, 'is_new_tab'))) {
                $subModule = ucfirst(Str::camel($sub['module']));
                $subFormWith_ = Str::plural(Str::snake(str_replace('-', '_', $sub['module'])));
                $method = 'add';
                $fieldAfter = '';
                $showLabel = Arr::get($sub, 'hide_header_label') ? 'false' : "'" . Arr::get($sub, 'title') . "'";
                $wrapperCls = Arr::get($sub, 'wrapper_cls') ? $wrapperCls . ' ' . Arr::get($sub, 'wrapper_cls') : $wrapperCls;
                if (Arr::get($sub, 'field_after') && !Arr::get($sub, 'is_new_tab')) {
                    $method = 'addAfter';
                    $fieldAfter = "'" . Arr::get($sub, 'field_after') . "',";
                }


                $subFormHtml = "";

                if (Arr::get($sub, 'module_relation') == 'many') {
                    $subFormHtml = "->$method($fieldAfter'" . $subFormWith_ . "', 'collection', [
                        'type' => 'form',
                        'label' => $showLabel,
                        'options' => [
                            'class' => 'Impiger\\" . $plugin . "\Forms\\" . $subModule . "Form',
                            'label' => false,
                            ],
                            'wrapper' => [
                                'class' => 'subFormRepeater " . $wrapperCls . "'
                            ]
                        ])";
                } else {
                    $subFormHtml = "->$method($fieldAfter'" . $subFormWith_ . "', 'form', [
                        'class' => 'Impiger\\" . $plugin . "\Forms\\" . $subModule . "Form',
                        'label' => $showLabel,
                        'wrapper' => [
                            'class' => 'form-group " . $wrapperCls . "'
                        ]
                    ])";
                }
                $restrictTo = (Arr::get($sub, 'restricted_role_id.0')) ? true : false;
                if ($restrictTo) {
                    $restictedRoleId = implode("|", Arr::get($sub, 'restricted_role_id'));
                    $subFormHtml = ";\r\n\t\t\t if(CrudHelper::isFieldVisible('" . $restictedRoleId . "')) {\r\n\t\t\t \$this" . $subFormHtml . ";}\r\n\t\t\t \$this";
                }

                $subForm[] = $subFormHtml;
            }
        }

        return ($this->isValidArray($subForm)) ? implode("\r\n\t\t\t", $subForm) . "\r\n\t\t\t" : "";
    }

    public static function _sort($a, $b)
    {
        if ($a['sortlist'] == $a['sortlist']) {
            return strnatcmp($a['sortlist'], $b['sortlist']);
        }
        return strnatcmp($a['sortlist'], $b['sortlist']);
    }

    protected function getCheckAndCheckBoxes($options)
    {
        $choices = [];

        if (!$options) {
            return $choices;
        }

        $optionList = explode("|", $options);

        foreach ($optionList as $opt) {
            list($key, $value) = explode(":", $opt);
            $choices[$key] = $value;
        }
        return $choices;
    }

    protected function getFillableFields($moduleConfig)
    {
        $fieldsInfo = $moduleConfig['forms'];
        $fillableFields = [];
        foreach ($fieldsInfo as $fieldInfo) {
            if ($fieldInfo['view'] == 1) {
                if (!in_array($fieldInfo['field'], array('created_at', 'updated_at', 'deleted_at', 'id'))) {
                    $fillableFields[] = $fieldInfo['field'];
                }
            }
        }

        if (in_array('entity_id', $fillableFields)) {
            array_push($fillableFields, 'entity_type');
        }

        if(Schema::hasColumn($this->moduleDBName, 'is_enabled') && !in_array('is_enabled', $fillableFields)) {
            array_push($fillableFields, 'is_enabled');
        }

        $fillable = "'" . implode("','", $fillableFields) . "'";

        return $fillable;
    }

    protected function getCastingFields($moduleConfig)
    {
        $casting = [];
        foreach ($moduleConfig['forms'] as $fieldInfo) {
            if (Arr::get($fieldInfo, 'casting')) {
                $casting[] = "'" . $fieldInfo['field'] . "' =>'" . $fieldInfo['casting'] . "'";
            }
        }

        $html = implode(",", $casting);
        return $html;
    }

    protected function getBulkUploadDetails($plugin)
    {
        $upload = [];
        $upload['table_view'] = ($this->isBulkUpload) ? "plugins/crud::import.list" : "core/table::table";
        $upload['upload_buttons'] = ($this->isBulkUpload) ? "$" . "buttons['bulk-upload'] = ['link' => '#','text' => view('plugins/crud::import.import')->render()];" : '';
        $uploadRoute = "['uploadRoute' => '{-name}.import','template'=>'{-name}']";
        $search = array('{-name}');
        $replace = array(strtolower($plugin));
        $uploadRoute = str_replace($search, $replace, $uploadRoute);
        $upload['upload_route'] = ($this->isBulkUpload) ? $uploadRoute : "";
        return $upload;
    }

    protected function getModulePermissionFlag($plugin)
    {
        $flag = [];
        $flag['parentFlag'] = "'parent_flag' => 'plugins.$plugin'";
        $moduleFlag = "[
                    'name' => '{++Names}',
                    'flag' => 'plugins." . $plugin . "',
                ],";
        $search = array('{++Names}');
        $replace = array(str_replace(
            '_',
            ' ',
            ucfirst(Str::plural(Str::snake(str_replace('-', '_', $plugin))))
        ));
        $moduleFlag = str_replace($search, $replace, $moduleFlag);
        $flag['moduleFlag'] = $moduleFlag;
        return $flag;
    }

    protected function gridDefaultActionBtns($moduleName)
    {
        $actionButtons = [];
        //        $cndns = ['id' => $this->moduleId];
        $cndns = ['module_name' => $moduleName];
        $module = DB::table('cruds')->select(['id', 'module_name', 'module_actions'])->where($cndns)->first();
        if (!empty($module)) {
            $modulePermissions = json_decode($module->module_actions, 1);
            foreach ($modulePermissions as $action => $value) {
                $this->isRowActivation = ($action == 'enable_disable') ? TRUE : $this->isRowActivation;
                $this->subscription = ($action == 'subscribe' || $action == 'is_master') ? TRUE : $this->subscription;
                $this->isCreate = ($action == 'create') ? TRUE : $this->isCreate;
                $this->isEdit = ($action == 'edit') ? TRUE : $this->isEdit;
                $this->isDelete = ($action == 'destroy') ? TRUE : $this->isDelete;
            }
        }
        $create = "\$this->addCreateButton(route('{-name}.create'), '{-name}.create')";
        $edit = "{-name}.edit";
        $delete = "{-name}.destroy";

        $actionButtons['create'] = ($this->isCreate) ? str_replace('{-name}', strtolower($moduleName), $create) : "[]";
        $actionButtons['edit'] = ($this->isEdit) ? str_replace('{-name}', strtolower($moduleName), $edit) : "";
        $actionButtons['delete'] = ($this->isDelete) ? str_replace('{-name}', strtolower($moduleName), $delete) : "";
        $actionButtons['has_permission'] = (Arr::get($modulePermissions, 'hide_operations')) ? "\$this->hasOperations = false; " : "";
        return $actionButtons;
    }

    protected function getSchedulerDetails($moduleName, $subModuleName = null)
    {
        $schedulerDetails = [];
        $moduleConfig = $this->crudModuleConfig;
        $module = ucfirst(Str::camel($moduleName));
        $name = Str::camel($moduleName);
        $crudModule = $moduleName;
        if ($subModuleName) {
            $name = ucfirst(Str::camel($subModuleName));
            $crudModule = $subModuleName;
        }
        $schedulerDetails['callSchedulerCommand'] = "\App\Utils\CrudHelper::callSchedulerCommandClass('" . $moduleName . "');";
        $schedulerDetails['schedulerCommandClass'] = "\App\Utils\CrudHelper::getSchedulerCommandClass('" . $moduleName . "');";
        if (Arr::get($moduleConfig, 'scheduler')) {
            $scheduler = json_encode($moduleConfig['scheduler'], true);
            $schedulerDetails['registerCommandServiceProvider'] = '$this->app->register(CommandServiceProvider::class);';
            $schedulerDetails['schedules'] = "\App\Utils\CrudHelper::getSchedulerConfig('" . $crudModule . "','" . $scheduler . "');";
        } else {
            $schedulerDetails['registerCommandServiceProvider'] = '#{register_command_service}';
            $schedulerDetails['schedules'] = '#{scheduler}';
        }

        return $schedulerDetails;
    }

    protected function getSubModuleSchedulerDetails($moduleName, $subModuleName = null)
    {
        $schedulerDetails = [];
        $moduleConfig = $this->crudModuleConfig;
        $module = ucfirst(Str::camel($moduleName));
        $name = ucfirst(Str::camel($moduleName));
        if ($subModuleName) {
            $name = ucfirst(Str::camel($subModuleName));
        }
        if (Arr::get($moduleConfig, 'scheduler')) {
            $scheduler = json_encode($moduleConfig['scheduler'], true);
            $schedulerDetails['registerCommandServiceProvider'] = '$this->app->register(CommandServiceProvider::class);';
            $schedulerDetails['schedules'] = "\App\Utils\CrudHelper::getSchedulerConfig('" . $subModuleName . "','" . $scheduler . "');\n\t#{scheduler}";
        } else {
            $schedulerDetails['schedules'] = '#{scheduler}';
        }
        return $schedulerDetails;
    }

    protected function getEmailConfigs($moduleName)
    {
        $emailConfigMethod = [];
        $moduleConfig = $this->crudModuleConfig;
        $module = ucfirst(Str::camel($moduleName));
        $name = Str::camel($moduleName);
        if (Arr::get($moduleConfig, 'email_config')) {
            $emailConfig = json_encode($moduleConfig['email_config'], true);
            if ($moduleConfig['email_config']['create']) {
                $emailConfigMethod['createEmail'] = "CrudHelper::sendEmailConfig('" . $moduleName . "','" . $emailConfig . "',$" . $name . ");";
            }
            if ($moduleConfig['email_config']['edit']) {
                $emailConfigMethod['editEmail'] = "CrudHelper::sendEmailConfig('" . $moduleName . "','" . $emailConfig . "',$" . $name . ");";
            }
        }
        return $emailConfigMethod;
    }

    public function getHideActionSettings()
    {
        $hideActions = [];
        $moduleConfig = $this->crudModuleConfig;
        if (Arr::get($moduleConfig, 'setting.hide_module_actions')) {
            $hideSettings = $moduleConfig['setting']['hide_module_actions'];
            $settings = explode("|", $hideSettings);
            $field = [];
            foreach ($settings as $setting) {
                if (Str::contains($setting, ':')) {
                    $field = explode(":", $setting);
                }
                if ($setting == 'edit') {
                    $hideActions[$setting] = "if(\$item->$field[0] == $field[1]){\$editPermissions = '';}";
                    $hideActions[$setting . '_name'] = "if(\$item->$field[0] == $field[1]){\$isEdit = false;}";
                }
                if ($setting == 'delete') {
                    $hideActions[$setting] = "if(\$item->$field[0] == $field[1]){\$deletePermissions = '';}";
                }
                if ($setting == 'enable_disable') {
                    $hideActions[$setting] = (!empty($field)) ? implode(":", $field) : "";
                }
            }
        }
        return $hideActions;
    }

    public function getRevisionHistorySettings() {
        $revisionHistory = [];
        $moduleConfig = $this->crudModuleConfig;
        if (Arr::get($moduleConfig, 'setting.revision_history')) {
            $revisionHistorySettings = $moduleConfig['setting']['revision_history'];
            $settings = json_decode($revisionHistorySettings);
            if (!empty($settings)) {
                $limitRevision = (isset($settings->history_limit)) ? $settings->history_limit : 20;
                $keepRevision = (isset($settings->keep_revision)) ? $settings->keep_revision : $this->getFillableFields($moduleConfig);
                $revisionHistory['revision_trait'] = "use RevisionableTrait;";
                $revisionHistory['revision_trait_path'] = "use Impiger\Revision\RevisionableTrait;";
                $revisionHistory['revision_properties'] = "protected \$revisionEnabled = true;"
                        . "\n protected \$revisionCleanup = true;"
                        . "\n protected \$historyLimit =" . $limitRevision . ";"
                        . "\n protected \$keepRevisionOf = [" . $keepRevision . "];";
            }
        }
        return $revisionHistory;
    }
    public function getWorkflowSettings() {
        $workflowSettings = [];
       
        if ($this->isWorkflow) {
               $workflowSettings['workflow_trait_path'] =  'use Impiger\Workflows\Traits\WorkflowProperty;';
               $workflowSettings['workflow_trait'] =  'use WorkflowProperty;';
               $workflowSettings['workflow_support'] =  "\$isWorkflowSupport = \$this->isWorkflowSupport(\$this->model->getTable());"
                        . "\n \$this->setFormOption('isWorkflowSupport', \$isWorkflowSupport);"
                ;
        }
        return $workflowSettings;
    }

}
