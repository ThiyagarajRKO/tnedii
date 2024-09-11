<?php

namespace Impiger\AuditLog\Repositories\Eloquent;

use Impiger\AuditLog\Repositories\Interfaces\AuditLogInterface;
use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;

/**
 * @since 16/09/2016 10:55 AM
 */
class AuditLogRepository extends RepositoriesAbstract implements AuditLogInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUnread($select = ['*'])
    {        
        $date = \Carbon\Carbon::now()->subDays(NOTIFICATION_DURATION_DAYS);
        $select[] = \DB::raw('(select id from cruds where cruds.module_name =audit_histories.module) as crud_id');
        $data = $this->model
                ->whereNotIn('audit_histories.module',NOTIFICATION_EXCLUDE_MODULES)
                ->where('audit_histories.created_at', '>=', $date->format('Y-m-d H:m:s'))
                ->whereRaw('(audit_histories.action = "created" OR audit_histories.type ="workflow")')
                ->whereRaw('not exists(select audit_histories_id from notification_history  WHERE user_id = '.\Auth::id().'  and audit_histories_id = audit_histories.id and reference_id = audit_histories.reference_id)')
                ->select($select)
                ->orderBy('audit_histories.id','desc');
        $data = apply_filters(BASE_FILTER_TABLE_QUERY, $data, $this->model, $select);
        if(isVendorUser()){
            $data=$data->whereNotIn('audit_histories.action',['drafted','updated']);
        }
        $data = $data->get(); 
        $this->resetModel();
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function countUnread()
    {
        $data = $this->model->whereIn('audit_histories.module',NOTIFICATION_MODULES)
                ->where('audit_histories.created_at', '>=', $date->format('Y-m-d H:m:s'))
                ->whereRaw('audit_histories.action = "created" OR audit_histories.type ="workflow"')
                ->whereRaw('not exists(select audit_histories_id from notification_history  WHERE user_id = '.\Auth::id().'  and audit_histories_id = audit_histories.id and reference_id = audit_histories.reference_id)')
                ->select($select)
                ->orderBy('audit_histories.id','desc');
               ;
        $data = apply_filters(BASE_FILTER_TABLE_QUERY, $data, $this->model, $select);
        $data = $data->count();
        $this->resetModel();
        return $data;
    }
}
