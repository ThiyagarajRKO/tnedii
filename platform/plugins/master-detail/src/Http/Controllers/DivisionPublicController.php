<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\DivisionRequest;
use Impiger\MasterDetail\Repositories\Interfaces\DivisionInterface;
use Impiger\MasterDetail\Tables\DivisionTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class DivisionPublicController extends Controller
{
    /**
     * @var DivisionInterface
     */
    protected $divisionRepository;

    /**
     * @param DivisionInterface $divisionRepository
     */
    public function __construct(DivisionInterface $divisionRepository)
    {
        $this->divisionRepository = $divisionRepository;
    }

    /**
     * @param DivisionTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(DivisionTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param DivisionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(DivisionRequest $request, BaseHttpResponse $response)
    {
        try {
            $division = $this->divisionRepository->getModel();
            $table = $division->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $division->fillable(array_merge($division->getFillable(),["is_enabled"]));
                $division->is_enabled = 0;
            }
            $division->fill($request->input());
            $this->divisionRepository->createOrUpdate($division);
            
            CrudHelper::uploadFiles($request, $division);
            event(new CreatedContentEvent(DIVISION_MODULE_SCREEN_NAME, $request, $division));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=division'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/division::failed_msg'));
        }
    }

    /**
     * @param DivisionRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, DivisionRequest $request, BaseHttpResponse $response)
    {
        try {
            $division = $this->divisionRepository->findOrFail($id);
            
            $division->fill($request->input());
            $this->divisionRepository->createOrUpdate($division);
            event(new UpdatedContentEvent(DIVISION_MODULE_SCREEN_NAME, $request, $division));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=division'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/division::failed_msg'));
        }
    }
}
