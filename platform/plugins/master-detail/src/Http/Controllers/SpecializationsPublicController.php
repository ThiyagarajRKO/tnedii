<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\SpecializationsRequest;
use Impiger\MasterDetail\Repositories\Interfaces\SpecializationsInterface;
use Impiger\MasterDetail\Tables\SpecializationsTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class SpecializationsPublicController extends Controller
{
    /**
     * @var SpecializationsInterface
     */
    protected $specializationsRepository;

    /**
     * @param SpecializationsInterface $specializationsRepository
     */
    public function __construct(SpecializationsInterface $specializationsRepository)
    {
        $this->specializationsRepository = $specializationsRepository;
    }

    /**
     * @param SpecializationsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SpecializationsTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param SpecializationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(SpecializationsRequest $request, BaseHttpResponse $response)
    {
        try {
            $specializations = $this->specializationsRepository->getModel();
            $table = $specializations->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $specializations->fillable(array_merge($specializations->getFillable(),["is_enabled"]));
                $specializations->is_enabled = 0;
            }
            $specializations->fill($request->input());
            $this->specializationsRepository->createOrUpdate($specializations);
            
            CrudHelper::uploadFiles($request, $specializations);
            event(new CreatedContentEvent(SPECIALIZATIONS_MODULE_SCREEN_NAME, $request, $specializations));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=specializations'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/specializations::failed_msg'));
        }
    }

    /**
     * @param SpecializationsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, SpecializationsRequest $request, BaseHttpResponse $response)
    {
        try {
            $specializations = $this->specializationsRepository->findOrFail($id);
            
            $specializations->fill($request->input());
            $this->specializationsRepository->createOrUpdate($specializations);
            event(new UpdatedContentEvent(SPECIALIZATIONS_MODULE_SCREEN_NAME, $request, $specializations));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=specializations'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/specializations::failed_msg'));
        }
    }
}
