<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\ParishRequest;
use Impiger\MasterDetail\Repositories\Interfaces\ParishInterface;
use Impiger\MasterDetail\Tables\ParishTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class ParishPublicController extends Controller
{
    /**
     * @var ParishInterface
     */
    protected $parishRepository;

    /**
     * @param ParishInterface $parishRepository
     */
    public function __construct(ParishInterface $parishRepository)
    {
        $this->parishRepository = $parishRepository;
    }

    /**
     * @param ParishTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(ParishTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param ParishRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(ParishRequest $request, BaseHttpResponse $response)
    {
        try {
            $parish = $this->parishRepository->getModel();
            $table = $parish->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $parish->fillable(array_merge($parish->getFillable(),["is_enabled"]));
                $parish->is_enabled = 0;
            }
            $parish->fill($request->input());
            $this->parishRepository->createOrUpdate($parish);
            
            CrudHelper::uploadFiles($request, $parish);
            event(new CreatedContentEvent(PARISH_MODULE_SCREEN_NAME, $request, $parish));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=parish'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/parish::failed_msg'));
        }
    }

    /**
     * @param ParishRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, ParishRequest $request, BaseHttpResponse $response)
    {
        try {
            $parish = $this->parishRepository->findOrFail($id);
            
            $parish->fill($request->input());
            $this->parishRepository->createOrUpdate($parish);
            event(new UpdatedContentEvent(PARISH_MODULE_SCREEN_NAME, $request, $parish));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=parish'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/parish::failed_msg'));
        }
    }
}
