<?php

namespace Impiger\Workflows\Traits;

use Arr;
use Workflow;

trait WorkflowProperty
{

    /**
     * @var workflowSupported
     */
    protected $workflowSupported = false;

    /**
     * @var moduleProperty
     */
    protected $moduleProperty = "";

    public function __construct()
    {
        $this->workflowSupported = $this->isWorkflowSupport();
        parent::__construct();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setAttribute($key, $val)
    {
        if ($this->id) {
            if ($this->workflowSupported && $this->getWorkflowModuleProperty() == $key) {
                $wfTransitions = config('plugins.workflows.workflow.' . $this->table . '.transitions');
                foreach ($wfTransitions as $k => $trans) {
                    if ($val == Arr::get($trans, 'from') && Arr::get($trans, 'metadata.action') == 'stateChangeOnUpdate') {
                        $input = $this;
                        $workflow = Workflow::get($input);
                        if ($workflow->can($this, $trans['name'])) {
                            // Apply a transition
                            $workflow->apply($input, $trans['name'], ["Updated By " . \Auth::id()]);
                            $val = $trans['to'];
                            return parent::setAttribute($key, $trans['to']);
                        } else {
                            \Log::info("Un Aouthorized Access.");
                        }
                        break;
                    }
                }
            }
            return parent::setAttribute($key, $val);
        }

        if ($this->workflowSupported && $this->getWorkflowModuleProperty() == $key) {
            $val = config('plugins.workflows.workflow.' . $this->table . '.initial_places', $val);
        }

        return parent::setAttribute($key, $val);
    }

    protected function isWorkflowSupport($table = null)
    {
        $this->table = ($table) ? $table : $this->table;
        if (!$this->workflowSupported && in_array($this->table, config('plugins.workflows.general.supported_module_tables', []))) {
            $this->workflowSupported = true;
        }

        return $this->workflowSupported;
    }

    public function getWorkflowModuleProperty()
    {
        if ($this->moduleProperty) {
            return $this->moduleProperty;
        }
        $this->moduleProperty = config('plugins.workflows.workflow.' . $this->table . '.marking_property', "");
        return $this->moduleProperty;
    }
}
