<?php

namespace Impiger\Table\Http\Controllers;

use App\Http\Controllers\Controller;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Table\Http\Requests\BulkChangeRequest;
use Impiger\Table\Http\Requests\FilterRequest;
use Impiger\Table\TableBuilder;
use Exception;
use Form;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class TableController extends Controller
{

    /**
     * @var TableBuilder
     */
    protected $tableBuilder;

    /**
     * TableController constructor.
     * @param TableBuilder $tableBuilder
     */
    public function __construct(TableBuilder $tableBuilder)
    {
        $this->tableBuilder = $tableBuilder;
    }

    /**
     * @param BulkChangeRequest $request
     * @return array|mixed
     * @throws Throwable
     */
    public function getDataForBulkChanges(BulkChangeRequest $request)
    {
        $object = $this->tableBuilder->create($request->input('class'));

        $data = $object->getValueInput(null, null, 'text');
        if (!$request->input('key')) {
            return $data;
        }

        $column = Arr::get($object->getBulkChanges(), $request->input('key'));
        if (empty($column)) {
            return $data;
        }

        $labelClass = 'control-label';
        if (!empty($column) && Str::contains(Arr::get($column, 'validate'), 'required')) {
            $labelClass .= ' required';
        }

        $label = '';
        if (!empty($column['title'])) {
            $label = Form::label($column['title'], null, ['class' => $labelClass])->toHtml();
        }

        if (isset($column['callback']) && method_exists($object, $column['callback'])) {
            $data = $object->getValueInput(
                $column['title'],
                null,
                $column['type'],
                call_user_func([$object, $column['callback']])
            );
        } else {
            $data = $object->getValueInput($column['title'], null, $column['type'], Arr::get($column, 'choices', []));
        }

        $data['html'] = $label . $data['html'];

        return $data;
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws BindingResolutionException
     */
    public function postSaveBulkChange(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/table::table.please_select_record'));
        }

        $inputKey = $request->input('key');
        $inputValue = $request->input('value');

        $object = $this->tableBuilder->create($request->input('class'));
        $columns = $object->getBulkChanges();

        if (!empty($columns[$inputKey]['validate'])) {
            $validator = Validator::make($request->input(), [
                'value' => $columns[$inputKey]['validate'],
            ]);

            if ($validator->fails()) {
                return $response
                    ->setError()
                    ->setMessage($validator->messages()->first());
            }
        }

        try {
            $object->saveBulkChanges($ids, $inputKey, $inputValue);
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $response->setMessage(trans('core/table::table.save_bulk_change_success'));
    }

    /**
     * @param FilterRequest $request
     * @return array|mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function getFilterInput(FilterRequest $request)
    {
        $object = $this->tableBuilder->create($request->input('class'));

        $data = $object->getValueInput(null, null, 'text');
        if (!$request->input('key')) {
            return $data;
        }
        
        /*Customized by Sabari Shankar Parthian start*/
        if(property_exists($object,'isRequest') && !Arr::get($object->getFilters(), $request->input('key'))){
            $object->isRequest = false;
        }
        /*Customized by Sabari Shankar Parthian end*/
        $column = Arr::get($object->getFilters(), $request->input('key'));
        if (empty($column)) {
            return $data;
        }

        if (isset($column['callback']) && method_exists($object, $column['callback'])) {
            return $object->getValueInput(
                null,
                $request->input('value'),
                $column['type'],
                call_user_func([$object, $column['callback']])
            );
        }

        return $object->getValueInput(
            null,
            $request->input('value'),
            $column['type'],
            Arr::get($column, 'choices', [])
        );
    }

    /**
     * @customized By Ramesh Esakki
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws BindingResolutionException
     */
    public function postSaveInlineBulkChange(Request $request, BaseHttpResponse $response)
    {
        $data = $request->input('data');

        if (empty($data) || !is_array($data) || !$request->input('class')) {
            return $response
                            ->setError()
                            ->setMessage(trans('core/table::table.please_select_record'));
        }

        $object = $this->tableBuilder->create($request->input('class'));
        $columns = $object->getBulkChanges();
        $repository = $object->getRepository();
        \DB::beginTransaction();
        $tableName = $repository->getTable();
        $insertItem = $insertData = [];
        foreach ($data as $id => $row) {
            if (Arr::get($row, 'update') || is_int($id)) {
                $item = $repository->findById($id);
            } else {
                $item = [];
                Arr::forget($data[$id], 'id');
            }
            foreach ($row as $key => $val) {
                $validateKey = $tableName . '.' . $key;
                if (isset($columns[$validateKey]) && !empty($columns[$validateKey]['validate'])) {
                    $validator = Validator::make($data[$id], [
                                $key => $columns[$validateKey]['validate']
                    ]);
                }

                if (strpos($key, '.') !== -1) {
                    $key = Arr::last(explode('.', $key));
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn($tableName, $key)) {
                    if ($item) {
                        $item->$key = $val;
                    } else {
                            $insertItem[$key] = $val;
                    }
                }
            }
            $insertData[] = $insertItem;
            if (isset($validator) && $validator->fails()) {
                \DB::rollback();
                return $response
                                ->setError()
                                ->setMessage($validator->messages());
            }

            if ($item) {
                $saveItem = $repository->createOrUpdate($item);
                event(new \Impiger\Base\Events\UpdatedContentEvent($repository->getModel(), request(), $saveItem));
            }
        }
        if ($insertData) {
            foreach($insertData as $rowData){
                $saveItem = $repository->createOrUpdate($rowData);
            }
            event(new \Impiger\Base\Events\CreatedContentEvent($repository->getModel(), request(), $saveItem));
        }
        \DB::commit();
        return $response->setMessage(trans('core/table::table.save_bulk_change_success'));
    }
    
}
