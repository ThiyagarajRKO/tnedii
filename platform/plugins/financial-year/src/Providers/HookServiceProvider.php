<?php

namespace Impiger\FinancialYear\Providers;

use Illuminate\Support\ServiceProvider;
use Impiger\Base\Forms\FormBuilder;
use Theme;
use App\Utils\CrudHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Str;
use DB;
use Carbon\Carbon;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Form Builder Utils
     */
    protected $formBuilder;

    /**
     * @throws \Throwable
     */
    public function boot(FormBuilder $formBuilder)
    {
        
        $this->app->booted(function () use ($formBuilder) {
            
            #{register_external_submodule}
            if (function_exists('add_shortcode')) {
                $this->formBuilder = $formBuilder;
                $that = $this;
                CrudHelper::declareShortCode($that, 'financial-year');
            }
            $this->extendValidatorMethod();
        });
    }

    

    public function buildFormUsingShortcode($shortCode)
    {
        $slug = CrudHelper::getCrudModuleSlugUsingShortcode($shortCode, $this->shortcodeMap);

        if(!$slug) {
            return false;
        }

        $this->loadFormAssets();
        if ($shortCode->id) {
            $repository = app()->make('Impiger\\'.$slug['parentModule'].'\Repositories\Interfaces\\'.$slug['moduleUpper'].'Interface');
            $model = $repository->findOrFail($shortCode->id);
            $method = $this->formBuilder->create('Impiger\\'.$slug['parentModule'].'\Forms\\'.$slug['moduleUpper'].'Form', ['model' => $model])
                ->setFormOption('url', $slug['moduleLower'].'/updatedata/' . $shortCode->id);
        } else {
            $method = $this->formBuilder->create('Impiger\\'.$slug['parentModule'].'\Forms\\'.$slug['moduleUpper'].'Form')
                ->setFormOption('url', $slug['moduleLower'].'/postdata');
        }

        return $method
               ->setFormOption('template', 'plugins/crud::theme-form.form-no-wrap')
               ->setActionButtons(view('plugins/crud::module.shortcodeactionbtn')->render())
               ->renderForm();
    }

    public function buildTableUsingShortcode($shortCode)
    {
        $slug = CrudHelper::getCrudModuleSlugUsingShortcode($shortCode, $this->shortcodeMap);

        if(!$slug) {
            return false;
        }

        $table = app()->make('Impiger\\'.$slug['parentModule'].'\Tables\\'.$slug['moduleUpper'].'Table');
        $this->loadTableAssets();
        return $table->setView('core/table::base-table')
            ->setOptions(['shortcode' => true])
            ->setHasFilter(false)
            ->setTableConfig($shortCode)
            ->setAjaxUrl(route('public.'.$slug['moduleLower'].'.index'))
            ->renderTable();
    }

    public function loadFormAssets()
    {
        if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
            $this->app->booted(function () {
                
            });
        }
    }

    public function loadTableAssets()
    {
        if (defined('THEME_OPTIONS_MODULE_SCREEN_NAME')) {
            $this->app->booted(function () {
                CrudHelper::loadTableAssets();
            });
        }
    }

    /**
     * Customized by Ubaidur Rahman.S *
     * @return string
     */
    public function extendValidatorMethod()
    {
        Validator::extend('valid_financial_year', function ($attribute, $value, $parameters, $validator) {
            if (!$value) {
                return true;
            }
            $startDate = Arr::get($validator->getData(), $parameters[0], null);
            $endYear = Carbon::createFromFormat('M-Y', $value);
            $startYear = Carbon::createFromFormat('M-Y', $startDate);
            $diffInMonths = ($endYear->diffInMonths($startYear)) + 1;

            return $diffInMonths >= 11 && $diffInMonths <= 12;
        }, 'Academic Year duration should be greater than 10 months and less than or equal to 12 months');

        Validator::extend('valid_running_year', function ($attribute, $value, $parameters, $validator) {
            if (!$value) {
                return true;
            }
            $id = Arr::get($parameters, 0, null);
            $query = DB::table('financial_year')->where('is_running', 1)->whereNull('deleted_at');
            if ($id) {
                $query = $query->whereNotIn('id', [$id]);
            }
            $academicYears = $query->first();
            $isRunning = true;
            if ($academicYears) {
                $isRunning = false;
            }
            return $isRunning;
        }, 'Another financial years is in active state');
        
        Validator::extend('valid_between_financial_year', function ($attribute, $value, $parameters, $validator) {
            if (!$value) {
                return true;
            }
            $id = Arr::get($parameters, 0, null);
            $query = DB::table('financial_year')->whereNull('deleted_at');
            if ($id) {
                $query = $query->whereNotIn('id', [$id]);
            }
            $academicYears = $query->get();
            if (empty($academicYears)) {
                return true;
            }
            foreach ($academicYears as $academicYear) {
                $endYear = Carbon::createFromFormat('M-Y', $academicYear->session_start);
                $startYear = Carbon::createFromFormat('M-Y', $academicYear->session_end);
                $currentYear = Carbon::createFromFormat('M-Y', $value);
                $between = $currentYear->between($startYear, $endYear);
                if ($between) {
                    return false;
                }
            }
            return true;
        }, 'Financial Year already exist');
    }
}
