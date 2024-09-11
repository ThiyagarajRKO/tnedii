<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\MasterDetailRequest;
use Impiger\MasterDetail\Repositories\Interfaces\MasterDetailInterface;
use Impiger\MasterDetail\Tables\MasterDetailTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class MasterDetailPublicController extends Controller
{
    /**
     * @var MasterDetailInterface
     */
    protected $masterDetailRepository;

    /**
     * @param MasterDetailInterface $masterDetailRepository
     */
    public function __construct(MasterDetailInterface $masterDetailRepository)
    {
        $this->masterDetailRepository = $masterDetailRepository;
    }

    /**
     * @param MasterDetailTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(MasterDetailTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param MasterDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(MasterDetailRequest $request, BaseHttpResponse $response)
    {
        try {
            $masterDetail = $this->masterDetailRepository->getModel();
            $table = $masterDetail->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $masterDetail->fillable(array_merge($masterDetail->getFillable(),["is_enabled"]));
                $masterDetail->is_enabled = 0;
            }
            $masterDetail->fill($request->input());
            $this->masterDetailRepository->createOrUpdate($masterDetail);
            
            event(new CreatedContentEvent(MASTER_DETAIL_MODULE_SCREEN_NAME, $request, $masterDetail));

            return $response
                    ->setPreviousUrl(url('/form-response?form=masterDetail'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/masterDetail::failed_msg'));
        }
    }

    /**
     * @param MasterDetailRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, MasterDetailRequest $request, BaseHttpResponse $response)
    {
        try {
            $masterDetail = $this->masterDetailRepository->findOrFail($id);
            
            $masterDetail->fill($request->input());
            $this->masterDetailRepository->createOrUpdate($masterDetail);
            event(new UpdatedContentEvent(MASTER_DETAIL_MODULE_SCREEN_NAME, $request, $masterDetail));

            return $response->setMessage(trans('core/base::notices.update_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/masterDetail::failed_msg'));
        }
    }
}
