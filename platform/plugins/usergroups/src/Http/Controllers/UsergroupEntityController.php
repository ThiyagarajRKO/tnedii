<?php

namespace Impiger\Usergroups\Http\Controllers;

use Impiger\Base\Events\BeforeEditContentEvent;
use Impiger\Usergroups\Http\Requests\UsergroupEntityRequest;
use Impiger\Usergroups\Repositories\Interfaces\UsergroupEntityInterface;
use Impiger\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Exception;
use Impiger\Usergroups\Tables\UsergroupEntityTable;
use Impiger\Base\Events\CreatedContentEvent;
use Impiger\Base\Events\DeletedContentEvent;
use Impiger\Base\Events\UpdatedContentEvent;
use Impiger\Base\Http\Responses\BaseHttpResponse;
use Impiger\Usergroups\Forms\UsergroupEntityForm;
use Impiger\Base\Forms\FormBuilder;
use Impiger\Usergroups\Models\UsergroupEntity;

class UsergroupEntityController extends BaseController
{

    /**
     * @var UsergroupEntityInterface
     */
    protected $usergroupEntityRepository;

    /**
     * @param UsergroupEntityInterface $usergroupEntityRepository
     */
    public function __construct(UsergroupEntityInterface $usergroupEntityRepository)
    {
        $this->usergroupEntityRepository = $usergroupEntityRepository;
    }

    
    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function index(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/usergroups::usergroups.entity_mapping.name'));
        
        return $formBuilder->create(UsergroupEntityForm::class,['url'=>route('usergroupsentity.create')])->renderForm();
    }

    /**
     * @param UsergroupEntityRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(UsergroupEntityRequest $request, BaseHttpResponse $response)
    {
        $usergroups = $request->input('usergroup_id');
        foreach($usergroups  as $key => $value){
            $data['crud_id'] = $key;
            $data['usergroup_id'] = $value;
            $usergroupEntity = UsergroupEntity::where('crud_id',$key)->first();
            $message = trans('core/base::notices.create_success_message');
            if(!empty($usergroupEntity)){
                $usergroupEntity->fill($data);
                $usergroupEntity->save();
                $message = trans('core/base::notices.update_success_message');
            }else{
                $usergroupEntity = $this->usergroupEntityRepository->createOrUpdate($data);
            }
        }
        

        return $response
            ->setPreviousUrl(route('usergroups.index'))
            ->setNextUrl(route('usergroupsentity.index'))
            ->setMessage($message);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder, Request $request)
    {
        $usergroupEntity = $this->usergroupEntityRepository->findOrFail($id);

        event(new BeforeEditContentEvent($request, $usergroupEntity));

        page_title()->setTitle(trans('plugins/crud::usergroupsentity.edit') . ' "' . $usergroupEntity->name . '"');

        return $formBuilder->create(UsergroupEntityForm::class, ['model' => $usergroupEntity])->renderForm();
    }

    /**
     * @param int $id
     * @param UsergroupEntityRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, UsergroupEntityRequest $request, BaseHttpResponse $response)
    {
        $usergroupEntity = $this->usergroupEntityRepository->findOrFail($id);

        if ($request->input('is_default')) {
            $this->usergroupEntityRepository->getModel()->where('id', '!=', $id)->update(['is_default' => 0]);
        }

        $usergroupEntity->fill($request->input());

        $this->usergroupEntityRepository->createOrUpdate($usergroupEntity);

        event(new UpdatedContentEvent(CATEGORY_MODULE_SCREEN_NAME, $request, $usergroupEntity));

        return $response
            ->setPreviousUrl(route('usergroupsentity.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $usergroupEntity = $this->usergroupEntityRepository->findOrFail($id);

            if (!$usergroupEntity->is_default) {
                $this->usergroupEntityRepository->delete($usergroupEntity);
                event(new DeletedContentEvent(CATEGORY_MODULE_SCREEN_NAME, $request, $usergroupEntity));
            }

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
            return $response->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $usergroupEntity = $this->usergroupEntityRepository->findOrFail($id);
            if (!$usergroupEntity->is_default) {
                $this->usergroupEntityRepository->delete($usergroupEntity);

                event(new DeletedContentEvent(CATEGORY_MODULE_SCREEN_NAME, $request, $usergroupEntity));
            }
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
