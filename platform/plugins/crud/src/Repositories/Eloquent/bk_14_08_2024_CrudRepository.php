<?php

namespace Impiger\Crud\Repositories\Eloquent;

use Impiger\Base\Enums\BaseStatusEnum;
use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\Crud\Repositories\Interfaces\CrudInterface;
use Impiger\Institution\Repositories\Interfaces\InstitutionInterface;
use Impiger\TrainingTitle\Repositories\Interfaces\TrainingTitleInterface;


class CrudRepository extends RepositoriesAbstract implements CrudInterface
{
    /**
     * {@inheritDoc}
     */
    /*
    public function getInstitutionLists($limit) {
        if (is_plugin_active('institution')) {
            $Institution_model = app(InstitutionInterface::class)->getModel();
            $data = $Institution_model->whereRaw('(institutions.is_license_expired != 1 OR institutions.is_license_expired IS NULL)')
                    ->where('institutions.is_enabled', IS_ENABLED)
                    ->select([
                        'institutions.id',
                        'institutions.name',
                        'institutions.image',
                        'multidomains.name as domain_url',
                        'district.name as district_name',
                        'institutions.coordinates',
                        'institutions.created_at',
                        'countries.country_name'
                    ])
                    ->leftJoin('district', 'district.id', '=', 'institutions.district')
                    ->leftJoin('multidomains', 'multidomains.id', '=', 'institutions.domain_id')
                    ->leftJoin('countries', 'countries.id', '=', 'institutions.country_id')
                    ->orderBy('institutions.created_at', 'desc')
//                ->limit($limit)
            ;

            if (request()->get('char')) {
                $data->where('institutions.name', 'LIKE', request()->get('char') . "%");
            }

            if (request()->get('type')) {
                $data->where('institutions.institute_type', '=', request()->get('type'));
            }

            if (request()->get('institute')) {
                $data->where('institutions.name', 'LIKE', "%".request()->get('institute') . "%");
            }

            return ($limit) ?  $this->applyBeforeExecuteQuery($data)->paginate($limit) : $this->applyBeforeExecuteQuery($data);
        } else {
            return [];
        }
    }
    */
    public function getTrainingTitleLists($limit) {
        // \Log::info("getTrainingTitleLists");
        // \Log::info($limit);
        if (is_plugin_active('training-title')) {
            // \Log::info("getTrainingTitleLists");
            \Log::info(is_plugin_active('training-title'));
            $TrainingTitle_model = app(TrainingTitleInterface::class)->getModel();
            $data = $TrainingTitle_model->select([
                'training_title.id',
                'annual_action_plan.name',
                'training_title.venue',
                'training_title.training_start_date',
                'training_title.training_end_date',
                'training_title.created_at'
            ])       
            ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id')
            ->orderBy('training_title.created_at', 'desc')
            ->limit($limit);
            // dd($data->dd());
            // if (request()->get('char')) {
            //     $data->where('training_title.name', 'LIKE', request()->get('char') . "%");
            // }

            // if (request()->get('type')) {
            //     $data->where('training_title.institute_type', '=', request()->get('type'));
            // }

            // if (request()->get('institute')) {
            //     $data->where('training_title.name', 'LIKE', "%".request()->get('institute') . "%");
            // }

            
            $data = $data->where('training_title.training_start_date', '>=', '2024-06-10');
            

            return ($limit) ?  $this->applyBeforeExecuteQuery($data)->paginate($limit) : $this->applyBeforeExecuteQuery($data);
        } else {
            return [];
        }
    }
    
    public function getRecentTrainingTitleLists() {
         if (is_plugin_active('training-title')) {
        $TrainingTitle_model = app(TrainingTitleInterface::class)->getModel();
        $data = $TrainingTitle_model->select([
            'training_title.id',
            'annual_action_plan.name',
            'training_title.venue',
            'training_title.training_start_date',
            'training_title.training_end_date',
            'training_title.created_at',
            \DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`, '%b') AS month"),
            \DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`, '%d') AS day"),
            \DB::Raw("DATE_FORMAT(`training_title`.`training_start_date`, '%Y') AS year")
        ])       
        ->leftJoin('annual_action_plan', 'annual_action_plan.id', '=', 'training_title.annual_action_plan_id')
        ->orderBy('training_title.created_at', 'desc')
        ->limit(10);
        
        $data = $data->where('training_title.training_start_date', '>=', '2016-06-10');
        

        return $this->applyBeforeExecuteQuery($data)->get();
         } else {
            return [];
        }
    }

    

}
