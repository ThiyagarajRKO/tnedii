<?php

namespace Impiger\InnovationVoucherProgram\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\InnovationVoucherProgram\Http\Requests\InnovationVoucherProgramRequest;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\InnovationVoucherProgramInterface;
use Impiger\InnovationVoucherProgram\Tables\InnovationVoucherProgramTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class InnovationVoucherProgramPublicController extends Controller
{
    /**
     * @var InnovationVoucherProgramInterface
     */
    protected $innovationVoucherProgramRepository;

    /**
     * @param InnovationVoucherProgramInterface $innovationVoucherProgramRepository
     */
    public function __construct(InnovationVoucherProgramInterface $innovationVoucherProgramRepository)
    {
        $this->innovationVoucherProgramRepository = $innovationVoucherProgramRepository;
    }

    /**
     * @param InnovationVoucherProgramTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(InnovationVoucherProgramTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param InnovationVoucherProgramRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(InnovationVoucherProgramRequest $request, BaseHttpResponse $response)
    {
        try {
            $innovationVoucherProgram = $this->innovationVoucherProgramRepository->getModel();
            $table = $innovationVoucherProgram->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $innovationVoucherProgram->fillable(array_merge($innovationVoucherProgram->getFillable(),["is_enabled"]));
                $innovationVoucherProgram->is_enabled = 0;
            }
            $innovationVoucherProgram->fill($request->input());
            $this->innovationVoucherProgramRepository->createOrUpdate($innovationVoucherProgram);
            CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_company_details',false,'');
		CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_knowledge_partners',false,'');
		
            CrudHelper::uploadFiles($request, $innovationVoucherProgram);
            event(new CreatedContentEvent(INNOVATION_VOUCHER_PROGRAM_MODULE_SCREEN_NAME, $request, $innovationVoucherProgram));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=innovation voucher program'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/innovationVoucherProgram::failed_msg'));
        }
    }

    /**
     * @param InnovationVoucherProgramRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, InnovationVoucherProgramRequest $request, BaseHttpResponse $response)
    {
        try {
            $innovationVoucherProgram = $this->innovationVoucherProgramRepository->findOrFail($id);
            CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_company_details',$id,'');
		CrudHelper::createUpdateSubforms($request, $innovationVoucherProgram, 'ivp_knowledge_partners',$id,'');
		
            $innovationVoucherProgram->fill($request->input());
            $this->innovationVoucherProgramRepository->createOrUpdate($innovationVoucherProgram);
            event(new UpdatedContentEvent(INNOVATION_VOUCHER_PROGRAM_MODULE_SCREEN_NAME, $request, $innovationVoucherProgram));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=innovation voucher program'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/innovationVoucherProgram::failed_msg'));
        }
    }
}
