<?php

namespace Impiger\Usergroups\Forms;

use Assets;
use Impiger\Base\Forms\FormAbstract;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Usergroups\Http\Requests\UsergroupsRequest;
use Impiger\Usergroups\Models\UsergroupEntity;
use Impiger\Usergroups\Models\Usergroups;
use App\Models\Crud;
use Illuminate\Support\Arr;

class UsergroupEntityForm extends FormAbstract
{

    /**
     * {@inheritDoc}
     */
    public function buildForm()
    {
        Assets::addStyles(['jquery-ui'])
            ->addScripts(['jquery-ui'])
            ->addStylesDirectly('vendor/core/core/acl/css/custom-style.css');
        
        $entities = Crud::where("is_entity",1)->get();
        $userGroups = Usergroups::get();
        $rawCondition = get_common_condition('usergroups');
        if(!empty($rawCondition)){
            $userGroups = Usergroups::whereRaw($rawCondition)->get();
        }
        $mappedEntities = $this->getMappedEtities();
        $this
            ->setFormOption('template', 'plugins/crud::module.form-template')    
            ->setupModel(new Usergroups)
            ->setValidatorClass(UsergroupsRequest::class)
            ->withCustomFields()
             ->addMetaBoxes([
                'Entity Mapping' => [
                    'title'   => trans('plugins/usergroups::usergroups.form.entity_mapping'),
                    'content' => view('plugins/usergroups::usergroups-entity-lists',compact('entities','userGroups','mappedEntities'))->render(),
                ],
            ])
           ->setActionButtons(view('plugins/crud::module.form-actions')->render());  
    }
    protected function getMappedEtities(){
        $availableEntities=[];
        $userGroupEntities = UsergroupEntity::get();
        foreach($userGroupEntities as $userGroup){
            $availableEntities[$userGroup->crud_id][] = $userGroup->usergroup_id;
        }
        
        return $availableEntities;
    }
}
