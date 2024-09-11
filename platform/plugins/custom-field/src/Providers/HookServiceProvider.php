<?php

namespace Impiger\CustomField\Providers;

use Assets;
use Impiger\Blog\Models\Post;
use Impiger\Page\Models\Page;
use CustomField;
use Eloquent;
use Illuminate\Support\Facades\Auth;
use Impiger\ACL\Repositories\Interfaces\RoleInterface;
use Impiger\Blog\Repositories\Interfaces\PostInterface;
use Impiger\CustomField\Facades\CustomFieldSupportFacade;
use Illuminate\Support\ServiceProvider;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'handle'], 125, 2);
        /*is this required*/
        add_filter(BASE_ACTION_META_BOXES_USING_CRUD, [$this, 'renderRepeater'], 125, 2);
    }

    /**
     * @param string $priority
     * @param Eloquent $object
     * @throws Throwable
     */
    public function handle($priority, $object = null)
    {
        $reference = get_class($object);
        if (CustomField::isSupportedModule($reference) && $priority == 'advanced') {
            add_custom_fields_rules_to_check([
                $reference   => isset($object->id) ? $object->id : null,
                'model_name' => $reference,
            ]);

            /**
             * Every models will have these rules by default
             */
            if (Auth::check()) {
                add_custom_fields_rules_to_check([
                    'logged_in_user'          => Auth::id(),
                    'logged_in_user_has_role' => $this->app->make(RoleInterface::class)->pluck('id'),
                ]);
            }

            if (defined('PAGE_MODULE_SCREEN_NAME')) {
                switch ($reference) {
                    case Page::class:
                        add_custom_fields_rules_to_check([
                            'page_template' => isset($object->template) ? $object->template : '',
                        ]);
                        break;
                }
            }

            if (defined('POST_MODULE_SCREEN_NAME')) {
                switch ($reference) {
                    case Post::class:
                        if ($object) {
                            $relatedCategoryIds = $this->app->make(PostInterface::class)->getRelatedCategoryIds($object);
                            add_custom_fields_rules_to_check([
                                $reference . '_post_with_related_category' => $relatedCategoryIds,
                                $reference . '_post_format'                => $object->format_type,
                            ]);
                        }
                        break;
                }
            }

            echo $this->render($reference, isset($object->id) ? $object->id : null);
        }
    }

    /**
     * @param string $reference
     * @param string $id
     * @throws Throwable
     */
    protected function render($reference, $id)
    {
        $customFieldBoxes = get_custom_field_boxes($reference, $id);

        if (!$customFieldBoxes) {
            return null;
        }

        Assets::addStylesDirectly([
            'vendor/core/plugins/custom-field/css/custom-field.css',
        ])
            ->addScriptsDirectly(config('core.base.general.editor.ckeditor.js'))
            ->addScriptsDirectly([
                'vendor/core/plugins/custom-field/js/use-custom-fields.js',
            ])
            ->addScripts(['jquery-ui']);

        CustomFieldSupportFacade::renderAssets();
        return CustomFieldSupportFacade::renderCustomFieldBoxes($customFieldBoxes);
    }

    /**
     * @param string $priority
     * @param Eloquent $object
     * @throws Throwable
     */
    public function renderRepeater($object = null, $model)
    {
        $reference = get_class($object);
        if ($this->isSupportedCrudModule($reference)) {
            add_custom_fields_rules_to_check([
                $reference   => isset($object->id) ? $object->id : null,
                'model_name' => $reference,
            ]);

            /**
             * Every models will have these rules by default
             */
            if (Auth::check()) {
                add_custom_fields_rules_to_check([
                    'logged_in_user'          => Auth::user()->getKey(),
                    'logged_in_user_has_role' => $this->app->make(RoleInterface::class)->pluck('id'),
                ]);
            }

            return $this->renderCustom($reference, isset($model->id) ? $model->id : null);
        }
    }

    public function isSupportedCrudModule($reference)
    {
        # code...
        $slug = \Str::slug(str_replace('\\', '_', $reference), '_');
        $data = \Impiger\CustomField\Models\FieldGroup::where('rules', 'LIKE', "%{$slug}%")->get();
        return (count($data) > 0) ? true : false;
    }

    /**
     * @param string $reference
     * @param string $id
     * @throws Throwable
     */
    protected function renderCustom($reference, $id)
    {
        $customFieldBoxes = get_custom_field_boxes($reference, $id);

        if (!$customFieldBoxes) {
            return null;
        }

        Assets::addStylesDirectly([
            'vendor/core/plugins/custom-field/css/custom-field.css',
        ])
            ->addScriptsDirectly(config('core.base.general.editor.ckeditor.js'))
            ->addScriptsDirectly([
                'vendor/core/plugins/custom-field/js/use-custom-fields.js',
            ])
            ->addScripts(['jquery-ui']);

        CustomFieldSupportFacade::renderAssets();
        return $customFieldBoxes;
    }
}
