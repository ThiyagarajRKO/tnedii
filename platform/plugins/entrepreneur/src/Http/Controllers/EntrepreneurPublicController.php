<?php

namespace Impiger\Entrepreneur\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Entrepreneur\Http\Requests\EntrepreneurRequest;
use Impiger\Entrepreneur\Repositories\Interfaces\EntrepreneurInterface;
use Impiger\Entrepreneur\Tables\EntrepreneurTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class EntrepreneurPublicController extends Controller
{
    /**
     * @var EntrepreneurInterface
     */
    protected $entrepreneurRepository;

    /**
     * @param EntrepreneurInterface $entrepreneurRepository
     */
    public function __construct(EntrepreneurInterface $entrepreneurRepository)
    {
        $this->entrepreneurRepository = $entrepreneurRepository;
    }

    /**
     * @param EntrepreneurTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(EntrepreneurTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param EntrepreneurRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(EntrepreneurRequest $request, BaseHttpResponse $response, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        try {
            $roleSlug = CANDIDATE_ROLE_SLUG;
            if($request->has('candidate_type_id') && $request->has('spoke_registration_id') && $request->has('hub_institution_id')) {
                $roleSlug = SPOKE_STUDENT_ROLE_SLUG;
            }
            $coreUserRepository = app(\Impiger\ACL\Repositories\Interfaces\UserInterface::class);
            $activateUserService = app(\Impiger\ACL\Services\ActivateUserService::class);
            $coreUserExists = $coreUserRepository->getFirstBy(['email'=>$request['email']]);
            if(!$coreUserExists){
                $request['username'] = $request['email'];
                $user = CrudHelper::createCoreUserAndAssignRoleAndPermission($request, $coreUserRepository, $activateUserService,$roleSlug,true);
            }else{
                $user = $coreUserExists;
            }
            $request['user_id'] = $user->id;

            $entrepreneur = $this->entrepreneurRepository->getModel();
           
            $entrepreneur->fill($request->input());
            $this->entrepreneurRepository->createOrUpdate($entrepreneur);
            
            CrudHelper::uploadFiles($request, $entrepreneur);
            event(new CreatedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=entrepreneur'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/entrepreneur::failed_msg'));
        }
    }

    /**
     * @param EntrepreneurRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, EntrepreneurRequest $request, BaseHttpResponse $response)
    {
        try {
            $entrepreneur = $this->entrepreneurRepository->findOrFail($id);
            
            $entrepreneur->fill($request->input());
            $this->entrepreneurRepository->createOrUpdate($entrepreneur);
            event(new UpdatedContentEvent(ENTREPRENEUR_MODULE_SCREEN_NAME, $request, $entrepreneur));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=entrepreneur'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/entrepreneur::failed_msg'));
        }
    }
}
