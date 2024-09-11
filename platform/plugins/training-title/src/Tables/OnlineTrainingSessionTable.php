<?php

namespace Impiger\TrainingTitle\Tables;

use Auth;
use BaseHelper;
use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\TrainingTitle\Repositories\Interfaces\OnlineTrainingSessionInterface;
use Impiger\Table\Abstracts\TableAbstract;
use Illuminate\Contracts\Routing\UrlGenerator;
use Yajra\DataTables\DataTables;
use Impiger\TrainingTitle\Models\OnlineTrainingSession;
use Html;
use DB;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;


class OnlineTrainingSessionTable extends TableAbstract
{

    /**
     * @var bool
     */
    protected $hasActions = true;

    /**
     * @var bool
     */
    protected $hasFilter = true;

    /**
     * @var string
     */
    protected $view = "plugins/crud::import.list";
    // protected $view = "plugins/training-title::online-session";
    protected $editPermissions = "online-training-session.edit";
    protected $deletePermissions = "online-training-session.destroy";
    /* @customized by Sabari Shankar.Parthiban*/
    protected $printPreview = 'base.print';
    /**
     * OnlineTrainingSessionTable constructor.
     * @param DataTables $table
     * @param UrlGenerator $urlGenerator
     * @param OnlineTrainingSessionInterface $onlineTrainingSessionRepository
     */
    public function __construct(DataTables $table, UrlGenerator $urlGenerator, OnlineTrainingSessionInterface $onlineTrainingSessionRepository)
    {
        $this->repository = $onlineTrainingSessionRepository;
        $this->setOption('id', 'plugins-online-training-session-table');
        
        if(getCandidateId(\Auth::id())) {
            $this->view = "plugins/training-title::online-session";
        }
        parent::__construct($table, $urlGenerator);

        
    }

    /**
     * {@inheritDoc}
     */
    public function ajax()
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('name', function ($item) {
                $this->checkDefault($item);
                $isEdit =  (!empty($this->editPermissions));
                $isPublic =  $this->getOption('shortcode');

                #{hideOnEditLink}
                return CrudHelper::getNameFieldLink($item, 'online-training-session', $isEdit, $isPublic);
            })
            ->editColumn('checkbox', function ($item) {
                return $this->getCheckbox($item->id);
            })
            ->editColumn('header', function($item) { 
				return CrudHelper::formatRows($item->header, 'database', 'attribute_options|id|name', $item, '');
			})
			->editColumn('type', function($item) { 
				return CrudHelper::formatRows($item->type, 'database', 'attribute_options|id|name', $item, '');
			})
            
            ->editColumn('created_at', function ($item) {
                return BaseHelper::formatDate($item->created_at);
            })
            ->addColumn('operations', function ($item) {
                $editPermissions = $this->editPermissions;$deletePermissions = $this->deletePermissions;
                $this->checkDefault($item);
                #{hideOnEditAction}
                #{hideOnDeleteAction}
                return $this->getOperations($this->editPermissions, $this->deletePermissions, $item, "<a  href='online-training-sessions/viewdetail/$item->id'  class='btn btn-icon btn-sm btn-primary' data-toggle='tooltip' data-original-title='View'><i class='fa fa-eye'></i></a>");
            });

            return $this->toJson($data);
    }

    /**
     * {@inheritDoc}
     */
    public function query()
    {
        $model = $this->repository->getModel();
        $select = [
            'online_training_sessions.id',
			'online_training_sessions.header',
			'online_training_sessions.title',
			'online_training_sessions.sub_title',
			'online_training_sessions.url',
			'online_training_sessions.type'
        ];

        $query = $model->select($select);

        return $this->applyScopes(apply_filters(BASE_FILTER_TABLE_QUERY, $query, $model, $select));
    }

    /**
     * {@inheritDoc}
     */
    public function columns()
    {
        return [
            'header' => [
			'name' => 'header',
			'title' => 'Header',
			'width' => '100',
			'class' => 'text-left'
			],
			'title' => [
			'name' => 'title',
			'title' => 'Title',
			'width' => '100',
			'class' => 'text-left'
			],
			'sub_title' => [
			'name' => 'sub_title',
			'title' => 'Sub Title',
			'width' => '100',
			'class' => 'text-left'
			],
			'url' => [
			'name' => 'url',
			'title' => 'Url',
			'width' => '100',
			'class' => 'text-left'
			],
			'type' => [
			'name' => 'type',
			'title' => 'Type',
			'width' => '100',
			'class' => 'text-left'
			]
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function buttons()
    {
        if (!$this->hasActions) {
            return [];
        }

        $buttons = $this->addCreateButton(route('online-training-session.create'), 'online-training-session.create');
        $buttons['bulk-upload'] = ['link' => '#','text' => view('plugins/crud::import.import')->render()];
        
        return apply_filters(BASE_FILTER_TABLE_BUTTONS, $buttons, OnlineTrainingSession::class);
    }

    /**
     * {@inheritDoc}
     */
    public function bulkActions(): array
    {
        if (Auth::user() && !Auth::user()->hasPermission('online-training-session.edit') ) {
                    return [];
                }
        return $this->addDeleteAction(route('online-training-session.deletes'), 'online-training-session.destroy', parent::bulkActions());
    }

    /**
     * {@inheritDoc}
     */
    public function getBulkChanges($isFilter = false): array
    {
        return CrudHelper::getBulkChanges('online-training-session', $isFilter, );
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->getBulkChanges(true);
    }

    /**
     * @return array
     */
    public function getDefaultButtons(): array
    {
        $defaultBtns = parent::getDefaultButtons();

        if (!$this->hasActions) {
            return $defaultBtns;
        }

        if (Auth::user() && Auth::user()->hasPermission('online-training-session.export')) {
            $defaultBtns = array_merge($defaultBtns, ['export']);
        }

        if (Auth::user() && Auth::user()->hasPermission('online-training-session.print')) {
            $defaultBtns = array_merge($defaultBtns, ['print']);
        }

        return $defaultBtns;
    }

    protected function checkDefault($item){
        if( isset($item->is_default) && $item->is_default){
            if(Auth::user() && (Auth::user()->is_admin || Auth::user()->isSuperUser())){
                $this->editPermissions = 'online-training-session.edit';
            }else{
                $this->editPermissions = '';
            }
            $this->deletePermissions = '';
        }
    }

    public function setTableConfig($config): self
    {
        $this->hasActions = (isset($config->hasActions)) ? $config->hasActions : false;
        $this->hasOperations = (isset($config->hasOperations)) ? $config->hasOperations : false;
        $this->hasCheckbox = (isset($config->hasCheckbox)) ? $config->hasCheckbox : false;
        $this->pageLength = (isset($config->pageLength)) ? $config->pageLength : $this->pageLength;
        return $this;
    }

    

}
