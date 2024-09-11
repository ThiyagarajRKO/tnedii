<?php

namespace Impiger\KnowledgePartner\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Base\Forms\FormBuilder;
use Impiger\Base\Http\Controllers\BaseController;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Traits\HasDeleteManyItemsTrait;
use Impiger\KnowledgePartner\Forms\KnowledgePartnerForm;
use Impiger\KnowledgePartner\Tables\KnowledgePartnerTable;
use Impiger\KnowledgePartner\Repositories\Interfaces\KnowledgePartnerInterface;
use Impiger\Setting\Supports\SettingStore;
use EmailHandler;
use Exception;
use Illuminate\Http\Request;
use Assets;

class KnowledgePartnerController extends BaseController
{
    use HasDeleteManyItemsTrait;

    /**
     * @var KnowledgePartnerInterface
     */
    protected $knowledgePartnerRepository;

    /**
     * @param KnowledgePartnerInterface $knowledgePartnerRepository
     */
    public function __construct(KnowledgePartnerInterface $knowledgePartnerRepository)
    {
        $this->knowledgePartnerRepository = $knowledgePartnerRepository;
        
        Assets::addScriptsDirectly([
            'vendor/core/plugins/crud/js/custom_save_storage.js',
            'vendor/core/plugins/crud/js/crud_utils.js',
            'vendor/core/plugins/knowledge-partner/js/knowledge-partner.js'
            
        ])
        ->addStylesDirectly([
            'vendor/core/plugins/crud/css/module_custom_styles.css'
            
        ]);
    }

    /**
     * @param KnowledgePartnerTable $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(KnowledgePartnerTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/knowledge-partner::knowledge-partner.menu'));

        return $dataTable->renderTable();
    }

    /**
     * @param $id
     * @param FormBuilder $formBuilder
     * @param Request $request
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        page_title()->setTitle(trans('plugins/knowledge-partner::knowledge-partner.edit'));

        $knowledge_partner = $this->knowledgePartnerRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $knowledge_partner));

        return $formBuilder->create(KnowledgePartnerForm::class, ['model' => $knowledge_partner])->renderForm();
    }
    
    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $knowledge_partner = $this->knowledgePartnerRepository->findOrFail($id);
        $name_of_the_institution = ($knowledge_partner->name_of_the_institution) ? ' "' . $knowledge_partner->name_of_the_institution . '"' : "";
        page_title()->setTitle(trans('plugins/knowledge-partner::knowledge-partner.view') . $name_of_the_institution);

        return $formBuilder->create(KnowledgePartnerForm::class, ['model' => $knowledge_partner, 'isView' => true])->renderForm();
    }

    /**
     * @param $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, EditKnowledgePartnerRequest $request, BaseHttpResponse $response)
    {
        $knowledge_partner = $this->knowledgePartnerRepository->findOrFail($id);

        $knowledge_partner->fill($request->input());

        $this->knowledgePartnerRepository->createOrUpdate($knowledge_partner);

        return $response
            ->setPreviousUrl(route('knowledge-partners.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        try {
            $knowledge_partner = $this->knowledgePartnerRepository->findOrFail($id);
            
            $knowledge_partner->fill(['deleted_at' => date("Y-m-d H:i:s")]);

            $this->knowledgePartnerRepository->createOrUpdate($knowledge_partner);
            //$this->knowledgePartnerRepository->delete($knowledge_partner);

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
