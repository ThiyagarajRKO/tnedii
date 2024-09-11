<?php

namespace Impiger\User\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\User\Http\Requests\UserAddressRequest;
use Impiger\User\Repositories\Interfaces\UserAddressInterface;
use Impiger\User\Tables\UserAddressTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class UserAddressPublicController extends Controller
{
    /**
     * @var UserAddressInterface
     */
    protected $userAddressRepository;

    /**
     * @param UserAddressInterface $userAddressRepository
     */
    public function __construct(UserAddressInterface $userAddressRepository)
    {
        $this->userAddressRepository = $userAddressRepository;
    }

    /**
     * @param UserAddressTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(UserAddressTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param UserAddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(UserAddressRequest $request, BaseHttpResponse $response)
    {
        try {
            $userAddress = $this->userAddressRepository->getModel();
            $table = $userAddress->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $userAddress->fillable(array_merge($userAddress->getFillable(),["is_enabled"]));
                $userAddress->is_enabled = 0;
            }
            $userAddress->fill($request->input());
            $this->userAddressRepository->createOrUpdate($userAddress);
            
            CrudHelper::uploadFiles($request, $userAddress);
            event(new CreatedContentEvent(USER_ADDRESS_MODULE_SCREEN_NAME, $request, $userAddress));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=user address'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/userAddress::failed_msg'));
        }
    }

    /**
     * @param UserAddressRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, UserAddressRequest $request, BaseHttpResponse $response)
    {
        try {
            $userAddress = $this->userAddressRepository->findOrFail($id);
            
            $userAddress->fill($request->input());
            $this->userAddressRepository->createOrUpdate($userAddress);
            event(new UpdatedContentEvent(USER_ADDRESS_MODULE_SCREEN_NAME, $request, $userAddress));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=user address'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/userAddress::failed_msg'));
        }
    }
}
