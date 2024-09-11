<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\QualificationsRequest;
use Impiger\MasterDetail\Repositories\Interfaces\QualificationsInterface;
use Impiger\MasterDetail\Tables\QualificationsTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class QualificationsPublicController extends Controller
{
    /**
     * @var QualificationsInterface
     */
    protected $qualificationsRepository;

    /**
     * @param QualificationsInterface $qualificationsRepository
     */
    public function __construct(QualificationsInterface $qualificationsRepository)
    {
        $this->qualificationsRepository = $qualificationsRepository;
    }

    /**
     * @param QualificationsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(QualificationsTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param QualificationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(QualificationsRequest $request, BaseHttpResponse $response)
    {
        try {
            $qualifications = $this->qualificationsRepository->getModel();
            $table = $qualifications->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $qualifications->fillable(array_merge($qualifications->getFillable(),["is_enabled"]));
                $qualifications->is_enabled = 0;
            }
            $qualifications->fill($request->input());
            $this->qualificationsRepository->createOrUpdate($qualifications);
            
            CrudHelper::uploadFiles($request, $qualifications);
            event(new CreatedContentEvent(QUALIFICATIONS_MODULE_SCREEN_NAME, $request, $qualifications));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=qualifications'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/qualifications::failed_msg'));
        }
    }

    /**
     * @param QualificationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, QualificationsRequest $request, BaseHttpResponse $response)
    {
        try {
            $qualifications = $this->qualificationsRepository->findOrFail($id);
            
            $qualifications->fill($request->input());
            $this->qualificationsRepository->createOrUpdate($qualifications);
            event(new UpdatedContentEvent(QUALIFICATIONS_MODULE_SCREEN_NAME, $request, $qualifications));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=qualifications'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/qualifications::failed_msg'));
        }
    }
}
