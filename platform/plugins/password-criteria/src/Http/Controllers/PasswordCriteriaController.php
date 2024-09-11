<?php

namespace Impiger\PasswordCriteria\Http\Controllers;

use Assets;
use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\PasswordCriteria\Http\Requests\PasswordCriteriaRequest;
use Impiger\PasswordCriteria\Repositories\Interfaces\PasswordCriteriaInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\PasswordCriteria\Tables\PasswordCriteriaTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\PasswordCriteria\Forms\PasswordCriteriaForm;
use Impiger\Base\Forms\FormBuilder;
use Impiger\PasswordCriteria\Models\PasswordCriteria;

class PasswordCriteriaController extends BaseController
{
    /**
     * @var PasswordCriteriaInterface
     */
    protected $passwordCriteriaRepository;

    /**
     * @param PasswordCriteriaInterface $passwordCriteriaRepository
     */
    public function __construct(PasswordCriteriaInterface $passwordCriteriaRepository)
    {
        $this->passwordCriteriaRepository = $passwordCriteriaRepository;
        Assets::addStylesDirectly(['vendor/core/core/base/libraries/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.css', 
                  'vendor/core/core/base/libraries/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.css',
                  'vendor/core/plugins/crud/css/jquery.toast.css' , 
                  'vendor/core/core/base/libraries/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
                  'vendor/core/plugins/crud/css/module_custom_styles.css'
            ]);
          
          Assets::addScriptsDirectly(['vendor/core/core/base/libraries/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js', 
                'vendor/core/core/base/libraries/bootstrap-switch/dist/js/bootstrap-switch.js',
                'vendor/core/core/base/libraries/bootstrap-switch/dist/js/bootstrap-switch.init.js',  
                'vendor/core/core/base/libraries/bootstrap-timepicker/js/bootstrap-timepicker.min.js',  
                'vendor/core/core/base/libraries/bootstrap-timepicker/js/bootstrap-timepicker.init.js',  
                'vendor/core/core/base/libraries/jquery-validation/jquery.validate.min.js',  
                'vendor/core/core/base/libraries/jquery-validation/jquery-validation.init.js',  
                'vendor/core/core/base/js/common_utils.js', 
                'vendor/core/plugins/password-criteria/js/save_criteria.js']); 
    }

    /**
     * @param PasswordCriteriaTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index()
    {
        page_title()->setTitle(trans('plugins/password-criteria::password-criteria.name'));
   
          $data['min_number_count'] = MIN_PWD_NUMBER_COUNT;
          $data['allowed_special_char'] = unserialize(PWD_ALLOWED_SPECIAL_CHAR);
        return view('plugins/password-criteria::index',compact('data'));
    }

   

    /**
     * @param PasswordCriteriaRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function saveCriteria(PasswordCriteriaRequest $request, BaseHttpResponse $response)
    {
        
        $criteria=$request->input();
        if($request->input('id')){
            $criteria = $this->passwordCriteriaRepository->findOrFail($request->input('id'));
            $criteria->fill($request->input());
        }
           
        $passwordCriteria = $this->passwordCriteriaRepository->createOrUpdate($criteria);
        \Session::put('criteria',$passwordCriteria);
        event(new CreatedContentEvent(PASSWORD_CRITERIA_MODULE_SCREEN_NAME, $request, $passwordCriteria));

        return $response
            ->setPreviousUrl(route('password-criteria.index'))
            ->setNextUrl(route('password-criteria.edit', $passwordCriteria->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function get_pwd_criteria()
    {
        $passworCriteria = PasswordCriteria::first();
        $criteria = ($passworCriteria) ? $passworCriteria : [];
        
        return $criteria;
    }

    
}
