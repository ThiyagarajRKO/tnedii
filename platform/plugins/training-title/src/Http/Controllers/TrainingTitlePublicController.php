<?php

namespace Impiger\TrainingTitle\Http\Controllers;

use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\TrainingTitle\Http\Requests\TrainingTitleRequest;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;
use Impiger\TrainingTitle\Tables\TrainingTitleTable;
use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use App\Utils\CrudHelper;
use Illuminate\Http\Request;

class TrainingTitlePublicController extends Controller
{
    /**
     * @var TrainingTitleInterface
     */
    protected $trainingTitleRepository;

    /**
     * @param TrainingTitleInterface $trainingTitleRepository
     */
    public function __construct(TrainingTitleInterface $trainingTitleRepository)
    {
        $this->trainingTitleRepository = $trainingTitleRepository;
    }

    /**
     * @param TrainingTitleTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(TrainingTitleTable $table)
    {
        return $table->setOptions(['shortcode' => true])->renderTable();
    }

    /**
     * @param TrainingTitleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function postData(TrainingTitleRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainingTitle = $this->trainingTitleRepository->getModel();
            $table = $trainingTitle->getTable();
            if (Schema::hasColumn($table, 'is_enabled')) {
                $trainingTitle->fillable(array_merge($trainingTitle->getFillable(), ["is_enabled"]));
                $trainingTitle->is_enabled = 0;
            }
            $trainingTitle->fill($request->input());
            $this->trainingTitleRepository->createOrUpdate($trainingTitle);

            CrudHelper::uploadFiles($request, $trainingTitle);
            event(new CreatedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));

            return $response
                ->setPreviousUrl(url('/form-response?form=training title'))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainingTitle::failed_msg'));
        }
    }

    /**
     * @param TrainingTitleRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Throwable
     */
    public function updateData($id, TrainingTitleRequest $request, BaseHttpResponse $response)
    {
        try {
            $trainingTitle = $this->trainingTitleRepository->findOrFail($id);

            $trainingTitle->fill($request->input());
            $this->trainingTitleRepository->createOrUpdate($trainingTitle);
            event(new UpdatedContentEvent(TRAINING_TITLE_MODULE_SCREEN_NAME, $request, $trainingTitle));

            return $response
                ->setPreviousUrl(url('/form-response?form=training title'))
                ->setMessage(trans('core/base::notices.create_success_message'));
        } catch (Exception $exception) {
            info($exception->getMessage());
            return $response
                ->setError()
                ->setMessage(trans('plugins/trainingTitle::failed_msg'));
        }
    }

    public function trainingTitleData(Request $request, BaseHttpResponse $response)
    {
        try {
            $input_data = $request->input();
            $limit = isset($input_data['length']) && $input_data['length'] != "" ? $input_data['length'] : 10; // Rows display per page
            $page = isset($input_data['start']) && $input_data['start'] != "" ? $input_data['start'] : 0;
            $columnIndex = isset($input_data['order']) && isset($input_data['order'][0]) && isset($input_data['order'][0]['column']) ? $input_data['order'][0]['column'] : ""; // Column index
            $columnName = $columnIndex !== "" && isset($input_data['columns']) && isset($input_data['columns'][$columnIndex]) && isset($input_data['columns'][$columnIndex]['data']) ? $input_data['columns'][$columnIndex]['data'] : "created_at"; // Column name
            $columnSortOrder = isset($input_data['order']) && isset($input_data['order'][0]) && isset($input_data['order'][0]['dir']) ? $input_data['order'][0]['dir'] : "DESC"; // asc or desc

            $selected_year = (isset($input_data['selected_year']) && $input_data['selected_year'] != "" ? $input_data['selected_year'] : date("Y"));
            $selected_month = (isset($input_data['selected_month']) && $input_data['selected_month'] != "" ? $input_data['selected_month'] : date("m"));
            $selected_month_year = $selected_year . "-" . $selected_month;

            $data = $this->trainingTitleRepository->getModel()->select([
                'training_title.id',
                'training_title.name',
                'training_title.code',
                'training_title.venue',
                'training_title.fee_paid',
                'training_title.fee_amount',
                'training_title.private_workshop',
                'training_title.training_background_image_name',
                'training_title.training_gallery_url_en',
                'training_title.training_gallery_url_ta',
                'training_title.training_start_date',
                'training_title.training_end_date',
                'training_title.email',
                'training_title.phone',
                'training_title.created_at',
                \DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`, '%b') AS month"),
                \DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`, '%d') AS day"),
                \DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`, '%Y') AS year")
            ])
                ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id')
                ->where('training_title.training_start_date', 'LIKE', $selected_month_year . "%");

            $total_count = $data->count();
            $data = $data
                ->orderBy($columnName, $columnSortOrder)
                ->limit($limit)
                ->offset($page);

            $displaying_record_count = $data->count();

            $data = $data->get();

            $return_data = [
                "draw" => isset($input_data['draw']) && $input_data['draw'] != "" ? $input_data['draw'] : 1,
                "recordsTotal" => $total_count,
                "recordsFiltered" => $total_count,
                "data" => $data,
            ];
            return response()->json($return_data);


        } catch (Exception $exception) {
            info($exception->getMessage());
            $return_data = [
                'code' => 400,
                'message' => $exception->getMessage(),
            ];
            return response()->json($return_data);
        }
    }

    public function getTrainingTitles(Request $request, BaseHttpResponse $response)
    {
        try {
            $input_data = $request->input();
            $limit = isset($input_data['length']) && $input_data['length'] != "" ? $input_data['length'] : 25; // Rows display per page
            $page = isset($input_data['start']) && $input_data['start'] != "" ? $input_data['start'] : 0;
            $search = $input_data['search'] ?? '';

            // Query
            $data = $this->trainingTitleRepository->getModel()->select([
                'training_title.id',
                'training_title.name',
                'training_title.code',
            ])
                ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id')
                ->join('financial_year', function ($join) {
                    $join->on('financial_year.id', '=', 'training_title.financial_year_id')
                        ->where('financial_year.is_running', '=', '1')
                        ->where('financial_year.is_enabled', '=', '1');
                })
                ->when($search, function ($query) use ($search) {
                    return $query->where('training_title.name', 'LIKE', "%{$search}%");
                });

            // Getting count
            $total_count = $data->count();

            // Pagination
            $data = $data
                ->orderBy("training_title.created_at", "desc")
                ->limit($limit)
                ->offset($page);

            // Getting Rows
            $data = $data->get();

            $return_data = [
                "count" => $total_count,
                "rows" => $data,
            ];
            return response()->json($return_data);


        } catch (Exception $exception) {
            $return_data = [
                'code' => 400,
                'message' => $exception->getMessage(),
            ];
            return response()->json($return_data);
        }
    }
}
