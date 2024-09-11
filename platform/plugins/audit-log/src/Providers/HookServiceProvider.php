<?php

namespace Impiger\AuditLog\Providers;

use Assets;
use AuditLog;
use Impiger\ACL\Models\User;
use Illuminate\Support\Facades\Auth;
use Impiger\Dashboard\Supports\DashboardWidgetInstance;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Impiger\AuditLog\Events\AuditHandlerEvent;
use Illuminate\Http\Request;
use stdClass;
use Throwable;
use Impiger\AuditLog\Repositories\Interfaces\AuditLogInterface;

class HookServiceProvider extends ServiceProvider
{
    /**
     * @var Collection
     */
    protected $unreadAudits = [];
    
    public function boot()
    {
        add_action(AUTH_ACTION_AFTER_LOGOUT_SYSTEM, [$this, 'handleLogout'], 45, 2);

        add_action(USER_ACTION_AFTER_UPDATE_PASSWORD, [$this, 'handleUpdatePassword'], 45, 3);
        add_action(USER_ACTION_AFTER_UPDATE_PASSWORD, [$this, 'handleUpdateProfile'], 45, 3);

        if (defined('BACKUP_ACTION_AFTER_BACKUP')) {
            add_action(BACKUP_ACTION_AFTER_BACKUP, [$this, 'handleBackup'], 45);
            add_action(BACKUP_ACTION_AFTER_RESTORE, [$this, 'handleRestore'], 45);
        }

        add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 28, 2);

        add_action(USER_ACTION_CRUD_MANAGEMENT, [$this, 'handleCrudModule'], 45, 5);
        
        $this->app->booted(function () {
            // add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 120);
        });
    }

    /**
     * @param Request $request
     * @param User $data
     */
    public function handleLogin(Request $request, $data)
    {
        event(new AuditHandlerEvent(
            'to the system',
            'logged in',
            $data->id,
            $data->name,
            'info'
        ));
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param User $data
     */
    public function handleLogout(Request $request, $data)
    {
        event(new AuditHandlerEvent(
            'of the system',
            'logged out',
            $data->id,
            $data->name,
            'info'
        ));
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param stdClass $data
     */
    public function handleUpdateProfile($screen, Request $request, $data)
    {
        event(new AuditHandlerEvent(
            $screen,
            'updated profile',
            $data->id,
            AuditLog::getReferenceName($screen, $data),
            'info'
        ));
    }

    /**
     * @param string $screen
     * @param Request $request
     * @param stdClass $data
     */
    public function handleUpdatePassword($screen, Request $request, $data)
    {
        event(new AuditHandlerEvent(
            $screen,
            'changed password',
            $data->id,
            AuditLog::getReferenceName($screen, $data),
            'danger'
        ));
    }

    /**
     * @param string $screen
     */
    public function handleBackup($screen)
    {
        event(new AuditHandlerEvent($screen, 'created', 0, '', 'info'));
    }

    /**
     * @param string $screen
     */
    public function handleRestore($screen)
    {
        event(new AuditHandlerEvent($screen, 'restored', 0, '', 'info'));
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function registerDashboardWidgets($widgets, $widgetSettings)
    {
        if (!Auth::user()->hasPermission('audit-log.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly('vendor/core/plugins/audit-log/js/audit-log.js');

        return (new DashboardWidgetInstance)
            ->setPermission('audit-log.index')
            ->setKey('widget_audit_logs')
            ->setTitle(trans('plugins/audit-log::history.widget_audit_logs'))
            ->setIcon('fas fa-history')
            ->setColor('#44b6ae')
            ->setRoute(route('audit-log.widget.activities'))
            ->setBodyClass('scroll-table')
            ->setColumn('col-md-12 col-sm-12')
            ->init($widgets, $widgetSettings);
    }

    /**
     * @Customized By Ramesh Esakki
     * @param string $screen
     * @param Request $request
     * @param stdClass $data
     */
    public function handleCrudModule($screen, $msg, $id, $moduleName, $type = 'info')
    {
        event(new AuditHandlerEvent(
            $screen,
            $msg,
            $id,
            $moduleName,
            'info'
        ));
    }
    
    /**
     * @param string $options
     * @return string
     *
     * @throws \Throwable
     */
    public function registerTopHeaderNotification($options)
    {
        if (Auth::user()->hasPermission('audit-log.index')) {
            $auditHistories = $this->setUnreadRequests();
            
            if ($auditHistories->count() == 0) {
                return $options;
            }

            return $options . view('plugins/audit-log::partials.notification', compact('auditHistories'))->render();
        }

        return $options;
    }
    
     /**
     * @param int $number
     * @param string $menuId
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getUnreadCount($number)
    {
      
            $unread = count($this->setUnreadRequests());

            if ($unread > 0) {
                return Html::tag('span', (string)$unread, ['class' => 'badge badge-success'])->toHtml();
            }

        return $number;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUnreadRequests(): Collection
    {
        if (!$this->unreadAudits) {
            $this->unreadAudits = $this->app->make(AuditLogInterface::class)
                 ->getUnread(); 
        }

        return $this->unreadAudits;
    }
}
