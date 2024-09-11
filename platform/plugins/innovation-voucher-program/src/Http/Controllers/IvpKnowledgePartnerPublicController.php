<?php

namespace Impiger\InnovationVoucherProgram\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\InnovationVoucherProgram\Http\Requests\IvpKnowledgePartnerRequest;
use Impiger\InnovationVoucherProgram\Repositories\Interfaces\IvpKnowledgePartnerInterface;
use Impiger\InnovationVoucherProgram\Tables\IvpKnowledgePartnerTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class IvpKnowledgePartnerPublicController extends Controller
{
    /**
     * @var IvpKnowledgePartnerInterface
     */
    protected $ivpKnowledgePartnerRepository;

    /**
     * @param IvpKnowledgePartnerInterface $ivpKnowledgePartnerRepository
     */
    public function __construct(IvpKnowledgePartnerInterface $ivpKnowledgePartnerRepository)
    {
        $this->ivpKnowledgePartnerRepository = $ivpKnowledgePartnerRepository;
    }

    /**
     * @param IvpKnowledgePartnerTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(IvpKnowledgePartnerTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param IvpKnowledgePartnerRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(IvpKnowledgePartnerRequest $request, BaseHttpResponse $response)
    {
        try {
            $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->getModel();
            $table = $ivpKnowledgePartner->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $ivpKnowledgePartner->fillable(array_merge($ivpKnowledgePartner->getFillable(),["is_enabled"]));
                $ivpKnowledgePartner->is_enabled = 0;
            }
            $ivpKnowledgePartner->fill($request->input());
            $this->ivpKnowledgePartnerRepository->createOrUpdate($ivpKnowledgePartner);
            
            CrudHelper::uploadFiles($request, $ivpKnowledgePartner);
            event(new CreatedContentEvent(IVP_KNOWLEDGE_PARTNER_MODULE_SCREEN_NAME, $request, $ivpKnowledgePartner));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=ivp knowledge partner'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/ivpKnowledgePartner::failed_msg'));
        }
    }

    /**
     * @param IvpKnowledgePartnerRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, IvpKnowledgePartnerRequest $request, BaseHttpResponse $response)
    {
        try {
            $ivpKnowledgePartner = $this->ivpKnowledgePartnerRepository->findOrFail($id);
            
            $ivpKnowledgePartner->fill($request->input());
            $this->ivpKnowledgePartnerRepository->createOrUpdate($ivpKnowledgePartner);
            event(new UpdatedContentEvent(IVP_KNOWLEDGE_PARTNER_MODULE_SCREEN_NAME, $request, $ivpKnowledgePartner));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=ivp knowledge partner'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/ivpKnowledgePartner::failed_msg'));
        }
    }
}
