<?php

namespace Impiger\FinancialYear\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\FinancialYear\Http\Requests\FinancialYearRequest;
use Impiger\FinancialYear\Repositories\Interfaces\FinancialYearInterface;
use Impiger\FinancialYear\Tables\FinancialYearTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;

class FinancialYearPublicController extends Controller
{
    /**
     * @var FinancialYearInterface
     */
    protected $financialYearRepository;

    /**
     * @param FinancialYearInterface $financialYearRepository
     */
    public function __construct(FinancialYearInterface $financialYearRepository)
    {
        $this->financialYearRepository = $financialYearRepository;
    }

    /**
     * @param FinancialYearTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(FinancialYearTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param FinancialYearRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(FinancialYearRequest $request, BaseHttpResponse $response)
    {
        try {
            $financialYear = $this->financialYearRepository->getModel();
            $table = $financialYear->getTable();
            if(Schema::hasColumn($table,'is_enabled')){
                $financialYear->fillable(array_merge($financialYear->getFillable(),["is_enabled"]));
                $financialYear->is_enabled = 0;
            }
            $financialYear->fill($request->input());
            $this->financialYearRepository->createOrUpdate($financialYear);
            
            CrudHelper::uploadFiles($request, $financialYear);
            event(new CreatedContentEvent(FINANCIAL_YEAR_MODULE_SCREEN_NAME, $request, $financialYear));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=financial year'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/financialYear::failed_msg'));
        }
    }

    /**
     * @param FinancialYearRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, FinancialYearRequest $request, BaseHttpResponse $response)
    {
        try {
            $financialYear = $this->financialYearRepository->findOrFail($id);
            
            $financialYear->fill($request->input());
            $this->financialYearRepository->createOrUpdate($financialYear);
            event(new UpdatedContentEvent(FINANCIAL_YEAR_MODULE_SCREEN_NAME, $request, $financialYear));
            
            return $response
                    ->setPreviousUrl(url('/form-response?form=financial year'))
                    ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/financialYear::failed_msg'));
        }
    }
}
