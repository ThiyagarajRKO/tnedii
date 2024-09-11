<?php

namespace Impiger\PluginGenerator\Commands;

use Impiger\DevTool\Commands\Abstracts\BaseMakeCommand;
use File;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use Arr;
use Impiger\PluginGenerator\Commands\PluginModuleCreateCommand;
use DB;

class PluginModuleMakeCrudCommand extends PluginModuleCreateCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'cms:plugin:module:make:crud {plugin : The plugin name} {name : CRUD name}  {is_edit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a CRUD inside a plugin using CRUD builder module';

    /**
     * The console command description.
     *
     * @var int
     */
    protected $moduleId = 0;
    /**
     * The console command description.
     *
     * @var array
     */
    protected $moduleDBName = '';

    /**
     * The console command description.
     *
     * @var array
     */
    protected $crudModuleConfig = array();

    /**
     * The console command description.
     *
     * @var string
     */
    protected $moduleQry = "";
    protected $moduleTitle = "";
    /**
     * The console command description.
     *
     * @var boolean
     */
    protected $isBulkUpload = false;
    protected $isMultiLingual = false;
    protected $isWorkflow = false;

    /**
     * The modify db migration cofig.
     *
     * @var array
     */
    protected $alterScriptData = [];


    /**
     * The subform data.
     *
     * @var array
     */
    protected $subformData = [];

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
        if (!preg_match('/^[a-z0-9\-]+$/i', $this->argument('plugin')) || !preg_match(
            '/^[a-z0-9\-]+$/i',
            $this->argument('name')
        )) {
            $this->error('Only alphabetic characters are allowed.');
            return 1;
        }

        $plugin = strtolower($this->argument('plugin'));
        $isEdit = strtolower($this->argument('is_edit'));
        $name = strtolower($this->argument('name'));
        $location = plugin_path($plugin);

        $row = \DB::table('cruds')->where('module_name', $name)->get()->first();
        if (!$row) {
            $this->error('Submodule name not exists in DB.');
            return 1;
        }

        if (isset($row->is_bulkupload) && $row->is_bulkupload) {
            $this->isBulkUpload = true;
        }
        if (isset($row->is_multi_lingual) && $row->is_multi_lingual) {
            $this->isMultiLingual = true;
        }

        if (isset($row->alter_script_data) && $row->alter_script_data) {
            $this->alterScriptData = $this->setAlterScriptData($row->alter_script_data);
        }
        if (isset($row->is_workflow_support) && $row->is_workflow_support) {
            $this->isWorkflow = true;
        }
        $submoduleExists = $this->isSubModuleExist($name, $plugin);
        if ($isEdit && !$submoduleExists) {
            $this->error('Submodule name not exists in application.');
            return 1;
        } else if (!$isEdit && $submoduleExists) {
            $this->error('Submodule name already exists in application.');
            return 1;
        } else {
            // $this->removeMigrationFiles($location, $plugin);
        }

        $this->timestampSuffix = str::random(12);
        $this->moduleId = $row->parent_id;
        $this->moduleDBName = $row->module_db;
        $this->crudModuleConfig = CF_decode_json($row->module_config);
        $this->subformData = Arr::get($this->crudModuleConfig, 'subgrid');
        $this->moduleQry = $row->module_queries;
        $this->moduleTitle = ($row->module_alias) ? $row->module_alias : $row->module_name;

        if (!File::isDirectory($location)) {
            $this->error('Plugin named [' . $plugin . '] does not exists.');
            return 1;
        }

        $this->publishStubs($this->getStub(), $location);

        if ($isEdit) {
            File::delete($location . '/database/migrations/{migrate_date}_{module}_create_{name}_table.stub');

            if (!$this->isValidArray($this->alterScriptData)) {
                File::delete($location . '/database/migrations/{migrate_date}_{module}_modify_{name}_table{timestampSuffix}.stub');
            }
        }

        /* Remove Scheduler related files*/
        if(!Arr::get($this->crudModuleConfig,'scheduler')){
            $files = File::files($location . '/src/Commands/');
            $fileCount = count($files);
            if($fileCount<=1){
                File::delete($location . '/src/Providers/CommandServiceProvider.stub');
                File::deleteDirectory($location . '/src/Commands');
            }else{
                File::delete($location . '/src/Commands/{Name}SchedulerCommand.stub');
            }
        }

        $this->removeUnusedFiles($location);
        $this->renameFiles($name, $location);
        $this->searchAndReplaceInFiles($name, $location);
        $this->resetAlterScriptData($name);
        $this->line('------------------');
        $this->line('<info>The CRUD for plugin </info> <comment>' . $plugin . '</comment> <info>was created in</info> <comment>' . $location . '</comment><info>, customize it!</info>');
        $this->line('------------------');
        $this->call('cache:clear');
        return 0;
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
        $files = [
            'composer.json',
            // 'config/permissions.stub',
            'helpers/constants.stub',
            'routes/web.stub',
            'src/Providers/{Module}ServiceProvider.stub',
        ];

        foreach ($files as $file) {
            File::delete($location . '/' . $file);
        }
    }


    /**
     * @param string $location
     */
    protected function removeMigrationFiles(string $location, $plugin)
    {
        // Delete Data from migrations table
        $name = strtolower($this->argument('name'));
        $endStr = '_' . Str::snake(str_replace('-', '_', $plugin)) . '_create_' . Str::snake(str_replace('-', '_', $name)) . '_table';
        DB::table('migrations')->whereRaw("migration LIKE '%" . $endStr . "'")->delete();

        // Delete files from migrations directory
        $files = glob($location . '/database/migrations/*'.$endStr.'.php');
        foreach ($files as $file) {
            if (File::isFile($file))
                File::delete($file);
        }
    }

    /**
     * @param string $file
     * @param null $content
     * @return string
     */
    protected function replacementSubModule(string $file = null, $content = null): string
    {
        $name = strtolower($this->argument('name'));

        if ($file && empty($content)) {
            $content = file_get_contents($this->getStub() . '/../crud-sub-module/' . $file);
        }

        $replace = $this->getReplacements($name) + $this->baseReplacements($name);

        return str_replace(array_keys($replace), $replace, $content);
    }

    /**
     * {@inheritDoc}
     */
    public function getReplacements(string $replaceText): array
    {
        $grid = $this->parseCrudGridData();
        $moduleQry = $this->moduleQry;
        $gridColumns = (isset($grid['columns'])) ? $grid['columns'] : "";
        $mandatoryFields = (isset($grid['mandatoryFields'])) ? $grid['mandatoryFields'] : "";
        $gridColumnConfig = (isset($grid['columnConfig'])) ? $grid['columnConfig'] : "";
        $gridColumnLookup = (isset($grid['columnLookup'])) ? $grid['columnLookup'] : "";
        $module = strtolower($this->argument('plugin'));
        $submenu = $this->getSubModuleDetails($module, $replaceText);
        $subform = $this->getSubformDetails($replaceText, $module);
        $bulkUpload = $this->getBulkUploadDetails($replaceText);
        $registerLang = ($this->isMultiLingual) ? '\Language::registerModule(['.ucfirst(Str::camel($replaceText)).'::class]);' : '';
        $gridDefaultActions = $this->gridDefaultActionBtns($replaceText);
        $insertUserBeforeCode = $this->getInsertUserBefore();
        $validation = $this->parseMandatoryData();
        $scheduler = $this->getSchedulerDetails($module, $replaceText);
        $emailConfig = $this->getEmailConfigs($replaceText);
        $moduleShortCode = $this->getModuleShortCodeFnCall($module);
        $belongsTo = $this->getBelongsToModule($module,$replaceText);

        if (Arr::get($this->crudModuleConfig, 'sql_select')) {
            $qryArray = explode("->", $this->moduleQry);

            if(Arr::has($qryArray, 0)) {
                $columnStr = str_replace("select(", "", $qryArray[0]);
                if($columnStr) {
                $columnStr = substr($columnStr, 0, -1);
                $gridColumns = stripslashes($columnStr);
                unset($qryArray[0]);
                $moduleQry = "->".implode("->", $qryArray);
                $moduleQry = str_replace("\n", "\r\n\t\t\t", $moduleQry);
                }
            }
        } else {
            $moduleQry = "";
        }
        $viewGalleryForm = $this->getViewGalleryFormMethod($replaceText);
        $prevUrl = ($this->insertUserBefore) ? "'users.profile.view',\$user->user_id.'?user_navigation=true'" : "'".strtolower($replaceText).".index'";
        $joinFields = $this->applyJoinByQueryintoModel($moduleQry);
        $dashboardStats = $this->getDashboardStatsFilter($replaceText, $module);
        $revisionHistory = $this->getRevisionHistorySettings();
        $workflowTraits = $this->getWorkflowSettings();
        
        $replacements = [
            '{type}'     => 'plugin',
            '{types}'    => 'plugins',
            '{++Titles}'    => str_replace('_', ' ',
                ucfirst(Str::plural(Str::snake(str_replace('-', '_', $this->moduleTitle))))),
            '{++title}'    => str_replace('_', ' ', Str::snake(str_replace('-', '_', $this->moduleTitle))),
            '{-module}'  => strtolower($module),
            '{module}'   => Str::snake(str_replace('-', '_', $module)),
            '{+module}'  => Str::camel($module),
            '{modules}'  => Str::plural(Str::snake(str_replace('-', '_', $module))),
            '{Modules}'  => ucfirst(Str::plural(Str::snake(str_replace('-', '_', $module)))),
            '{-modules}' => Str::plural($module),
            '{MODULE}'   => strtoupper(Str::snake(str_replace('-', '_', $module))),
            '{Module}'   => ucfirst(Str::camel($module)),
            '{crud_schema}' => $this->parseCrudDBData(),
            '{alter_crud_schema}' => $this->getAlterScriptData(),
            '{crud_form}' => $this->toBuildForm($replaceText),
            '{view_crud_form}' => $this->toBuildForm($replaceText, true),
            '{fillable_fields}' => $this->getFillableFields($this->crudModuleConfig),
            '{casting_fields}' => $this->getCastingFields($this->crudModuleConfig),
            '{grid_columns_field_name}' => $gridColumns,
            '{grid_columns_config}' => $gridColumnConfig,
            '{names}' => $this->moduleDBName,
            '{namePlural}' => Str::plural(Str::snake(str_replace('-', '_', $replaceText))),
            '{crud_config_table}' => 'cruds',
            '{crud_module_name}' => 'module_name',
            '{crud_mandatory_field}' => Arr::get($validation, 'mandatoryFields'),
            '{crud_validation_msg}' => Arr::get($validation, 'validationMsg'),
            '{grid_column_lookups}' => $gridColumnLookup,
            '{load_jquery_steps_assets}' => $this->loadJqueryAssets($replaceText, $module),
            '{set_grid_column_row_class}' => $this->getGridColumnRowClassScript(),
            '{apply_subscription_filter_cndn}' => $this->applySubscriptionFilterCndn(),
            '{isSubscription}' => $this->isSubscriptionParam(),
            '{query_builder}' => $moduleQry,
            '{subform_relationship_fns}' => Arr::get($subform, 'subformRelFns'),
            '{subform_create_script}' => Arr::get($subform, 'subformCreateScript'),
            '{subform_update_script}' => Arr::get($subform, 'subformUpdateScript'),
            '{subform_validation_script}' => Arr::get($subform, 'validations'),
            '{subform_validation_msg}' => Arr::get($subform, 'validationMsg'),
            '{table_view}' => '"'.Arr::get($bulkUpload, 'table_view').'"',
            '{import_button}' => Arr::get($bulkUpload, 'upload_buttons'),
            '{upload_route}' => Arr::get($bulkUpload, 'upload_route'),
            '{viewgallery_controller_method}' =>  $this->getViewGalleryControllerMethod($replaceText),
            '{viewgallery_form_method}' =>  Arr::get($viewGalleryForm, 'methodStr'),
            '{viewgallery_form_declr}' =>  Arr::get($viewGalleryForm, 'declrStr'),
            '{register_lang_module}' => $registerLang,
            '{register_external_module}' => $this->getExternalRegisterModule($module, $module),
            '{grid_action_create_button}' => Arr::get($gridDefaultActions, 'create'),
            '{grid_action_edit_button}' => Arr::get($gridDefaultActions, 'edit'),
            '{grid_action_delete_button}' => Arr::get($gridDefaultActions, 'delete'),
            '{grid_action_permission}' => Arr::get($gridDefaultActions, 'has_permission'),
            '{grid_action_extra_buttons}' => $this->getGridActionBtns($replaceText,true),
            '{form_display_template}' => $this->getFormDisplayTemplate($replaceText),
            '{build_form_template}' => $this->getBuildFormTemplate($replaceText),
            '{module_allowed_actions}' => $this->getSubModuleModuleActions($module),
            '{entity_method}' => ($this->isEntityBased) ? "CrudHelper::isEntityCheck(\$this);" : " ",
            '{inline_edit_button}' => $this->getInlineEditBtn($replaceText),
            '{captcha_required}' => $this->getFormCaptchaRequired(),
            '{user_create_repo_instance}' => Arr::get($insertUserBeforeCode, 'userCreateRepoInstance'),
            '{user_create_declaration}' => Arr::get($insertUserBeforeCode, 'userCreateDeclaration'),
            '{user_update_repo_instance}' => Arr::get($insertUserBeforeCode, 'userUpdateRepoInstance'),
            '{user_update_declaration}' => Arr::get($insertUserBeforeCode, 'userUpdateDeclaration'),
            '{user_delete_repo_instance}' => Arr::get($insertUserBeforeCode, 'userDelete'),
            '{edit-profile-route}' => $this->getChangeProfileRouteScript($replaceText),
            '{check_profile_edit_permission}' => $this->getCheckEditProfilePermissionScript(),
            '#{register_external_submodule}' => Arr::get($submenu, 'registerExtMod'),
            '{set_prev_url}' => $prevUrl,
            '{timestampSuffix}' => $this->timestampSuffix,
            '#{register_command_service}' => Arr::get($scheduler, 'registerCommandServiceProvider'),
            '#{call_scheduler_command}' => Arr::get($scheduler, 'callSchedulerCommand'),
            '#{scheduler_command_class}' => Arr::get($scheduler, 'schedulerCommandClass'),
            '#{scheduler}' => Arr::get($scheduler, 'schedules'),
            '#{callEmailMethodOnCreate}' => Arr::get($emailConfig, 'createEmail'),
            '#{callEmailMethodOnEdit}' => Arr::get($emailConfig, 'editEmail'),
            '#{moduleCustomJs}' => $this->getCustomJs($replaceText, $module),
            '#{moduleDomainMapping}' => $this->getDomainMapping($replaceText, $module),
            '#{render_module_short_code_method}' => Arr::get($moduleShortCode, 'shortcode'),
            '#{render_module_short_code_options}' => Arr::get($moduleShortCode, 'options'),
            '#{belongsToFn}' => ($belongsTo)?:"",
            '{inline_edit_permission}' => ($this->isInlineEdit) ? "&& !Auth::user()->hasPermission('".$replaceText.".inline_edit')" : "",
            '{join_fields}' => ($joinFields) ? $joinFields : "",
            '{add_dashboard_stats_filter}' => Arr::get($dashboardStats, 'fn_declare'),
            '{callback_dashboard_stats_filter}' => Arr::get($dashboardStats, 'fn_callback'),
            '{merge_created_by_fields}' => (Arr::get($this->formConfigData, 'created_by')) ? "\$request->merge(['created_by' => \Auth::id()]);" : "",
            '{revision_trait_path}' => Arr::get($revisionHistory, 'revision_trait_path'),
            '{revision_trait}' => Arr::get($revisionHistory, 'revision_trait'),
            '{revision_properties}' => Arr::get($revisionHistory, 'revision_properties'),
           '{workflow_trait_path}' => Arr::get($workflowTraits, 'workflow_trait_path'),
            '{workflow_trait}' => Arr::get($workflowTraits, 'workflow_trait'),
            '{workflow_support}' => Arr::get($workflowTraits, 'workflow_support'),
        ];

        if (!$this->argument('is_edit')) {
            $replacements['#{register_sub_module}'] = Arr::get($submenu, 'registerList');
            $replacements['#{register_submodule_class}'] = Arr::get($submenu, 'regiserClass');
            $replacements['#{register_external_submodule}'] = Arr::get($submenu, 'registerExtMod');
            $replacements['#{submodule_menus}'] = Arr::get($submenu, 'menus');
            $replacements['#{mainmodule_menu}'] = Arr::get($submenu, 'mainMenu');
            $replacements['#{submodule_routes}'] = Arr::get($submenu, 'routes');
            $replacements['#{module_flag}'] = Arr::get($submenu, 'moduleFlag');
            $replacements['#{submodule_constants}'] = Arr::get($submenu, 'constants');
            $replacements['#{submodule_public_routes}'] = Arr::get($submenu, 'publicRoutes');
            $replacements['#{render_short_code_method}'] = Arr::get($submenu, 'shortcode');
            $replacements['#{render_short_code_method_call}'] = Arr::get($submenu, 'shortcodeFnCall');
        } else {
            $moduleConfig = $this->crudModuleConfig;
            $hideMenu = Arr::get($moduleConfig, 'setting.hide_menu');
            $id = 'cms-plugins-'.strtolower($replaceText);
            $parentId = 'cms-plugins-'.strtolower($module);
            $replacements["dashboard_menu()->removeItem('".$id."','".$parentId."');"] = "";

            if($hideMenu) {
                $replacements['#{removed_submenu_items}'] = "dashboard_menu()->removeItem('".$id."','".$parentId."');\r\n\t\t\t#{removed_submenu_items}";
            }
        }

        return $replacements;
    }

    public function getBelongsToModule($module,$replaceText){
        $row = $this->crudModuleConfig;
        $parentModule = DB::table('cruds')->where('module_name',$module)->first();
        $parentConfig = CF_decode_json($parentModule->module_config);
        $subForms = Arr::get($parentConfig, 'subgrid');
        $currentSubForm = [];
        if(!empty($subForms)){
            foreach ($subForms as $subForm) {
                if (Arr::get($subForm, 'module') == $replaceText) {
                    $currentSubForm = $subForm;
                }
            }
        }
        if(empty($currentSubForm)){
            return $str="";
        }
        $moduleFormWith_ = Str::plural(Str::snake(str_replace('-', '_', $module)));
        $module = ucfirst(Str::camel($module));
        $moduleCls = ucfirst(Str::camel($module));
        $key = Arr::get($currentSubForm, 'key');
        $localKey = Arr::get($currentSubForm, 'master_key') ? Arr::get($currentSubForm, 'master_key') : 'id';

        $str = "public function " . $moduleFormWith_ . "() {
                return \$this->belongsTo('Impiger\\" . $module . "\Models\\" . $moduleCls . "', '" . $key . "');
            }";

        return $str;
    }

}
