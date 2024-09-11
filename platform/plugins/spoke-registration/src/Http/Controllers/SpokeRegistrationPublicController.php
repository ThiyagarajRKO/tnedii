<?php

namespace Impiger\SpokeRegistration\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\SpokeRegistration\Http\Requests\SpokeRegistrationRequest;
use Impiger\SpokeRegistration\Repositories\Interfaces\SpokeRegistrationInterface;
use Impiger\SpokeRegistration\Tables\SpokeRegistrationTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class SpokeRegistrationPublicController extends Controller
{
    /**
     * @var SpokeRegistrationInterface
     */
    protected $spokeRegistrationRepository;

    /**
     * @param SpokeRegistrationInterface $spokeRegistrationRepository
     */
    public function __construct(SpokeRegistrationInterface $spokeRegistrationRepository)
    {
        $this->spokeRegistrationRepository = $spokeRegistrationRepository;
    }

    /**
     * @param SpokeRegistrationTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SpokeRegistrationTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param SpokeRegistrationRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(SpokeRegistrationRequest $request, BaseHttpResponse $response, \Impiger\ACL\Repositories\Interfaces\UserInterface $coreUserRepository, \Impiger\ACL\Services\ActivateUserService $activateUserService)
    {
        try {
            $spokeRegistration = $this->spokeRegistrationRepository->getModel();
            $table = $spokeRegistration->getTable();
          
            $spokeRegistration->fill($request->input());
            $this->spokeRegistrationRepository->createOrUpdate($spokeRegistration);
            $coreuser = CrudHelper::createImpigerUser($request,$spokeRegistration,$coreUserRepository,$activateUserService,SPOKE_ROLE_SLUG,false);
            CrudHelper::uploadFiles($request, $spokeRegistration);
            event(new CreatedContentEvent(SPOKE_REGISTRATION_MODULE_SCREEN_NAME, $request, $spokeRegistration));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=spoke registration'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/spokeRegistration::failed_msg'));
        }
    }

    /**
     * @param SpokeRegistrationRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, SpokeRegistrationRequest $request, BaseHttpResponse $response)
    {
        try {
            $spokeRegistration = $this->spokeRegistrationRepository->findOrFail($id);
            
            $spokeRegistration->fill($request->input());
            $this->spokeRegistrationRepository->createOrUpdate($spokeRegistration);
            event(new UpdatedContentEvent(SPOKE_REGISTRATION_MODULE_SCREEN_NAME, $request, $spokeRegistration));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=spoke registration'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/spokeRegistration::failed_msg'));
        }
    }
}
