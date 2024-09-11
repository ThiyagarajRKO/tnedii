<?php

namespace Impiger\TrainingTitle\Repositories\Eloquent;

use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;

class TrainingTitleRepository extends RepositoriesAbstract implements TrainingTitleInterface
{
    
    public function getTrainingTitleListGalleryView($limit) {
        // \Log::info("getTrainingTitleLists");
        // \Log::info($limit);
        if (is_plugin_active('training-title')) {
            // \Log::info("getTrainingTitleLists");
            // \Log::info(is_plugin_active('training-title'));
            // Impiger\Support\Repositories\Eloquent\TrainingTitleRepository
            $TrainingTitle_model = app(TrainingTitleInterface::class)->getModel();
            $data = $TrainingTitle_model->select([
                'training_title.id',
                'training_title.name',
                'training_title.venue',
                'training_title.training_start_date',
                'training_title.training_end_date',
                // 'DATE_FORMAT(training_title.training_start_date,"%c/%e/%Y") AS ttsd_js',
                // 'DATE_FORMAT(training_title.training_end_date,"%c/%e/%Y") AS TTed_js',
                'training_title.created_at'
            ])       
            ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id')
            ->orderBy('training_title.created_at', 'desc');
            // ->limit($limit);
            // dd($data->dd());
            if (request()->get('char')) {
                $data->where('training_title.name', 'LIKE', request()->get('char') . "%");
            }

            if (request()->get('type')) {
                $data->where('training_title.institute_type', '=', request()->get('type'));
            }

            // if (request()->get('institute')) {
            //     $data->where('training_title.name', 'LIKE', "%".request()->get('institute') . "%");
            // }

            $data = $data->where('training_title.training_start_date', '>=', date('Y-m-d'));
            
            return ($limit) ?  $this->applyBeforeExecuteQuery($data)->paginate($limit) : $this->applyBeforeExecuteQuery($data);
        } else {
            return [];
        }
    }

    
}
