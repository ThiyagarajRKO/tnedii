<?php

namespace Impiger\TnsiStartup\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\TnsiStartup\Http\Requests\TnsiStartupRequest;
use Impiger\TnsiStartup\Repositories\Interfaces\TnsiStartupInterface;
use Impiger\TnsiStartup\Tables\TnsiStartupTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class TnsiStartupPublicController extends Controller
{
    /**
     * @var TnsiStartupInterface
     */
    protected $tnsiStartupRepository;

    /**
     * @param TnsiStartupInterface $tnsiStartupRepository
     */
    public function __construct(TnsiStartupInterface $tnsiStartupRepository)
    {
        $this->tnsiStartupRepository = $tnsiStartupRepository;
    }

    /**
     * @param TnsiStartupTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TnsiStartupTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param TnsiStartupRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(TnsiStartupRequest $request, BaseHttpResponse $response)
    {
        try {
            $tnsiStartup = $this->tnsiStartupRepository->getModel();
            $table = $tnsiStartup->getTable();
            
            $tnsiStartup->fill($request->input());
            $this->tnsiStartupRepository->createOrUpdate($tnsiStartup);
            
            CrudHelper::uploadFiles($request, $tnsiStartup);
            event(new CreatedContentEvent(TNSI_STARTUP_MODULE_SCREEN_NAME, $request, $tnsiStartup));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=tnsi startup'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/tnsiStartup::failed_msg'));
        }
    }

    /**
     * @param TnsiStartupRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, TnsiStartupRequest $request, BaseHttpResponse $response)
    {
        try {
            $tnsiStartup = $this->tnsiStartupRepository->findOrFail($id);
            
            $tnsiStartup->fill($request->input());
            $this->tnsiStartupRepository->createOrUpdate($tnsiStartup);
            event(new UpdatedContentEvent(TNSI_STARTUP_MODULE_SCREEN_NAME, $request, $tnsiStartup));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=tnsi startup'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/tnsiStartup::failed_msg'));
        }
    }
}
