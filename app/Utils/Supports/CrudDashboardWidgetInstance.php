<?php

namespace App\Utils\Supports;

use Impiger\Dashboard\Repositories\Interfaces\DashboardWidgetInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Impiger\Dashboard\Supports\DashboardWidgetInstance;
use Throwable;

class CrudDashboardWidgetInstance extends DashboardWidgetInstance
{

    /**
     * @var bool
     */
    public $hasSubStats = false;

    /**
     * @var array
     */
    public $subStats = [];

    /**
     * @var array
     */
    public $statsConfig = [];

    /**
     * @var string
     */
    public $statsDisplayType = "";

    /**
     * @var string
     */
    public $module = "";

    /**
     * @var string
     */
    public $subModule = "";
    public $isCustomView = false;
    /**
     * @var string
     */
    public $order;
    /**
     * @return bool
     */
    public function isHasSubStats(): int
    {
        return $this->hasSubStats;
    }

    /**
     * @param bool $hasSubStats
     * @return DashboardWidgetInstance
     */
    public function setHasSubStats(bool $hasSubStats): self
    {
        $this->hasSubStats = $hasSubStats;
        return $this;
    }

    /**
     * @param bool $customView
     * @return DashboardWidgetInstance
     */
    public function setCustomView(bool $customView): self
    {
        $this->isCustomView = $customView;
        return $this;
    }

    /**
     * @return bool
     */
    public function getSubStats(): array
    {
        return $this->subStats;
    }

    /**
     * @param array $subStats
     * @return DashboardWidgetInstance
     */
    public function setSubStats(array $subStats): self
    {
        $this->subStats = $subStats;
        return $this;
    }

    /**
     * @return bool
     */
    public function getStatsDisplayType(): string
    {
        return $this->statsDisplayType;
    }

    /**
     * @param string $statsDisplayType
     * @return DashboardWidgetInstance
     */
    public function setStatsDisplayType(string $statsDisplayType): self
    {
        $this->statsDisplayType = $statsDisplayType;
        return $this;
    }

    /**
     * @param string $module
     * @return DashboardWidgetInstance
     */
    public function setModule(string $module): self
    {
        $this->module = $module;
        return $this;
    }

    /**
     * @param string $subModule
     * @return DashboardWidgetInstance
     */
    public function setSubModule(string $subModule): self
    {
        $this->subModule = $subModule;
        return $this;
    }

    /**
     * @param array $statsConfig
     * @return DashboardWidgetInstance
     */
    public function setStatsConfig(array $statsConfig): self
    {
        $this->statsConfig = $statsConfig;
        return $this;
    }
    /**
     * @return string
     */
    public function getStatsOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $key
     * @return DashboardWidgetInstance
     */
    public function setStatsOrder(string $order): self
    {
        $this->order = $order;

        return $this;
    }
    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function init($widgets, $widgetSettings)
    {
        if (!Auth::user()->hasPermission($this->permission)) {
            return $widgets;
        }

        $widget = $widgetSettings->where('name', $this->key)->first();
        $widgetSetting = $widget ? $widget->settings->first() : null;

        if (!$widget) {
            $widget = app(DashboardWidgetInterface::class)
                ->firstOrCreate(['name' => $this->key]);
        }

        $widget->title = $this->title;
        $widget->icon = $this->icon;
        $widget->color = $this->color;
        $widget->route = $this->route;
        $widget->key = $this->key;
        $widget->subStats = $this->subStats;
        $widget->statsDisplayType = $this->statsDisplayType;
        $widget->module = $this->module;
        $widget->subModule = $this->subModule;
        $widget->statsConfig = $this->statsConfig;
        if ($this->type === 'widget') {
            $widget->bodyClass = $this->bodyClass;
            $widget->column = $this->column;

            $data = [
                'id'   => $widget->id,
                'type' => $this->type,
                'view' => view('widgets.custom-base', compact('widget', 'widgetSetting'))->render(),
            ];

            if (empty($widgetSetting) || array_key_exists($widgetSetting->order, $widgets)) {
               if($this->order){
                   $widgets[$this->order] = $data;
               }else{
                   $widgets[] = $data;
               }
                
            } else {
                $widgets[$widgetSetting->order] = $data;
            }
            return $widgets;
        }

        $widget->statsTotal = $this->statsTotal;
        $view = ($this->hasSubStats) ? "widgets.custom-stats" : "core/dashboard::widgets.stats";
        $view = ($this->isCustomView) ? "widgets.website-custom-stats" : $view;
        if($this->order){
           $order = (\Arr::has($widgets,$this->order)) ?  $this->order+1 : $this->order;
           $widgets[$this->order] = [
            'id'   => $widget->id,
            'type' => $this->type,
            'view' => view($view, compact('widget', 'widgetSetting'))->render(),
            ];
        }
        else{
            $widgets[$this->key] = [
            'id'   => $widget->id,
            'type' => $this->type,
            'view' => view($view, compact('widget', 'widgetSetting'))->render(),
            ];
        }


        return $widgets;
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function renderDashboardWidget($widget)
    {
        $view = "widgets.website-stats";
        $widgetSetting = null;
        return view($view, compact('widget', 'widgetSetting'))->render();
    }
}
