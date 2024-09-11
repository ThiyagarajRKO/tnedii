<?php

namespace Impiger\Vendor\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Vendor\Http\Requests\VendorRequest;
use Impiger\Vendor\Repositories\Interfaces\VendorInterface;
use Impiger\Vendor\Tables\VendorTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class VendorPublicController extends Controller
{
    /**
     * @var VendorInterface
     */
    protected $vendorRepository;

    /**
     * @param VendorInterface $vendorRepository
     */
    public function __construct(VendorInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * @param VendorTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(VendorTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param VendorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(VendorRequest $request, BaseHttpResponse $response)
    {
        try {
            $vendor = $this->vendorRepository->getModel();
            $table = $vendor->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $vendor->fillable(array_merge($vendor->getFillable(),["is_enabled"]));
                $vendor->is_enabled = 0;
            }
            $vendor->fill($request->input());
            $this->vendorRepository->createOrUpdate($vendor);
            
            CrudHelper::uploadFiles($request, $vendor);
            event(new CreatedContentEvent(VENDOR_MODULE_SCREEN_NAME, $request, $vendor));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=vendor'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/vendor::failed_msg'));
        }
    }

    /**
     * @param VendorRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, VendorRequest $request, BaseHttpResponse $response)
    {
        try {
            $vendor = $this->vendorRepository->findOrFail($id);
            
            $vendor->fill($request->input());
            $this->vendorRepository->createOrUpdate($vendor);
            event(new UpdatedContentEvent(VENDOR_MODULE_SCREEN_NAME, $request, $vendor));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=vendor'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/vendor::failed_msg'));
        }
    }
}
