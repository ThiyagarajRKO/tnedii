<?php

namespace Impiger\BackendMenu\Forms;

use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\BackendMenu\Http\Requests\BackendMenuRequest;
use Impiger\BackendMenu\Models\BackendMenu;
use DB;
use App\Utils\CrudHelper;
use Assets;

class BackendMenuForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {   
        Assets::addScriptsDirectly([
            'vendor/core/packages/menu/libraries/jquery-nestable/jquery.nestable.js',
            'vendor/core/packages/menu/js/menu.js',
        ])
        ->addStylesDirectly([
            'vendor/core/packages/menu/libraries/jquery-nestable/jquery.nestable.css',
            'vendor/core/packages/menu/css/menu.css',
        ]);
        

        $this   
            ->setupModel(new BackendMenu)
            ->setFormOption('class', 'form-save-menu')
            ->setValidatorClass(BackendMenuRequest::class)
            ->withCustomFields()
            ->setActionButtons(view('plugins/backend-menu::actions', ['object' => $this->getModel()])->render())
            ->addMetaBoxes([
                'structure' => [
                    'wrap'    => false,
                    'content' => view('plugins/backend-menu::menu-structure', [
                        'menuNodes'  => $this->getModel(),
                        'appMenu' => dashboard_menu()->getAll()
                    ])->render(),
                ],
            ]);
             
    }
    
}
