<?php

namespace Impiger\MasterDetail\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\MasterDetail\Http\Requests\SubcountyRequest;
use Impiger\MasterDetail\Repositories\Interfaces\SubcountyInterface;
use Impiger\MasterDetail\Tables\SubcountyTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class SubcountyPublicController extends Controller
{
    /**
     * @var SubcountyInterface
     */
    protected $subcountyRepository;

    /**
     * @param SubcountyInterface $subcountyRepository
     */
    public function __construct(SubcountyInterface $subcountyRepository)
    {
        $this->subcountyRepository = $subcountyRepository;
    }

    /**
     * @param SubcountyTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(SubcountyTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param SubcountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(SubcountyRequest $request, BaseHttpResponse $response)
    {
        try {
            $subcounty = $this->subcountyRepository->getModel();
            $table = $subcounty->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $subcounty->fillable(array_merge($subcounty->getFillable(),["is_enabled"]));
                $subcounty->is_enabled = 0;
            }
            $subcounty->fill($request->input());
            $this->subcountyRepository->createOrUpdate($subcounty);
            
            CrudHelper::uploadFiles($request, $subcounty);
            event(new CreatedContentEvent(SUBCOUNTY_MODULE_SCREEN_NAME, $request, $subcounty));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=subcounty'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/subcounty::failed_msg'));
        }
    }

    /**
     * @param SubcountyRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, SubcountyRequest $request, BaseHttpResponse $response)
    {
        try {
            $subcounty = $this->subcountyRepository->findOrFail($id);
            
            $subcounty->fill($request->input());
            $this->subcountyRepository->createOrUpdate($subcounty);
            event(new UpdatedContentEvent(SUBCOUNTY_MODULE_SCREEN_NAME, $request, $subcounty));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=subcounty'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/subcounty::failed_msg'));
        }
    }
}
