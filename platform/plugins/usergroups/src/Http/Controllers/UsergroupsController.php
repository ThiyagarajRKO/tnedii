<?php

namespace Impiger\Usergroups\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Usergroups\Http\Requests\UsergroupsRequest;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupsInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Usergroups\Tables\UsergroupsTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Usergroups\Forms\UsergroupsForm;
use Impiger\Base\Forms\FormBuilder;
use App\Utils\CrudHelper;

class UsergroupsController extends BaseController
{
    /**
     * @var UsergroupsInterface
     */
    protected $usergroupsRepository;

    /**
     * @param UsergroupsInterface $usergroupsRepository
     */
    public function __construct(UsergroupsInterface $usergroupsRepository)
    {
        $this->usergroupsRepository = $usergroupsRepository;
    }

    /**
     * @param UsergroupsTable $table
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(UsergroupsTable $table)
    {
        page_title()->setTitle(trans('plugins/usergroups::usergroups.name'));

        return $table->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/usergroups::usergroups.create'));

        return $formBuilder->create(UsergroupsForm::class)->renderForm();
    }

    /**
     * @param UsergroupsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(UsergroupsRequest $request, BaseHttpResponse $response)
    {
        $input =array_merge($request->input(),['slug'=>\Str::slug($request->input('name'))]);
        $usergroups = $this->usergroupsRepository->createOrUpdate($input);

        event(new CreatedContentEvent(USERGROUPS_MODULE_SCREEN_NAME, $request, $usergroups));

        return $response
            ->setPreviousUrl(route('usergroupsentity.index'))
            ->setNextUrl(route('usergroupsentity.index'))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $usergroups = $this->usergroupsRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $usergroups));

        page_title()->setTitle(trans('plugins/usergroups::usergroups.edit') . ' "' . $usergroups->name . '"');

        return $formBuilder->create(UsergroupsForm::class, ['model' => $usergroups])->renderForm();
    }

    /**
     * @param int $id
     * @param UsergroupsRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, UsergroupsRequest $request, BaseHttpResponse $response)
    {
        $usergroups = $this->usergroupsRepository->findOrFail($id);

        $inputs = $request->input();
        if(!$request->has('roles')){
            $inputs['roles'] = [];
        }
        $usergroups->fill($inputs);

        $this->usergroupsRepository->createOrUpdate($usergroups);

        event(new UpdatedContentEvent(USERGROUPS_MODULE_SCREEN_NAME, $request, $usergroups));

        return $response
            ->setPreviousUrl(route('usergroups.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function viewdetail($id, FormBuilder $formBuilder, Request $request)
    {
        $userGroup = $this->usergroupsRepository->findOrFail($id);
        $name = ($userGroup->name) ? ' "' . $userGroup->name . '"' : "";
        page_title()->setTitle(trans('plugins/usergroups::usergroups.view') . $name);

        return $formBuilder->create(UsergroupsForm::class, ['model' => $userGroup, 'isView' => true])->renderForm();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $usergroups = $this->usergroupsRepository->findOrFail($id);

            $dataExist = CrudHelper::isDependentDataExistCore('usergroups',DEPENDANT_MODULE_IN_USERGROUPS, array($id));

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }
            $this->usergroupsRepository->delete($usergroups);

            event(new DeletedContentEvent(USERGROUPS_MODULE_SCREEN_NAME, $request, $usergroups));

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        $dataExist = CrudHelper::isDependentDataExistCore('usergroups',DEPENDANT_MODULE_IN_USERGROUPS, $ids);

            if($dataExist) {
                return $response
                    ->setError()
                    ->setMessage($dataExist);
            }

        foreach ($ids as $id) {
            $usergroups = $this->usergroupsRepository->findOrFail($id);
            $this->usergroupsRepository->delete($usergroups);
            event(new DeletedContentEvent(USERGROUPS_MODULE_SCREEN_NAME, $request, $usergroups));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
