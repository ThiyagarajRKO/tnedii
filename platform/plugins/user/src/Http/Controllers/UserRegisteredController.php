<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\User\Http\Requests\UserRequest;
use Impiger\User\Repositories\Interfaces\UserInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\User\Tables\UserRegisteredTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\User\Forms\UserForm;
use Impiger\Base\Forms\FormBuilder;
use Assets;
use App\Utils\CrudHelper;
use Impiger\Crud\Imports\BulkImport;
use Impiger\Crud\Imports\BulkErrorExport;
use Excel;
use Illuminate\Support\Str;

class UserRegisteredController extends BaseController
{
    /**
     * @var UserInterface
     */
    protected $userRepository;

    /**
     * @param UserInterface $userRepository
     */
    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;

        Assets::addStylesDirectly([
                'vendor/core/core/base/libraries/jquery-steps/css/jquery.steps.css',
                'vendor/core/plugins/crud/css/module_custom_styles.css'
                
                ])
                ->addScriptsDirectly([
                    'vendor/core/core/base/libraries/jquery-steps/js/jquery.steps.js',
                    'vendor/core/plugins/crud/js/custom_save_storage.js',
                    'vendor/core/plugins/crud/js/crud_utils.js'
                    ,'vendor/core/plugins/user/js/user.js'
                    
                ])
                ->addScriptsDirectlyToHeader([
                    'vendor/core/plugins/crud/js/jquery_steps_init.js'
            ]);
    }

    /**
     * @param UserTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(UserRegisteredTable $table)
    {
        page_title()->setTitle(trans('plugins/user::user-registration.name'));

        return $table->renderTable();
    }
    
   
    
    
    
}
