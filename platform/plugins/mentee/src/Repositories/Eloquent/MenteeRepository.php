<?php

namespace Impiger\Mentee\Repositories\Eloquent;

use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\Mentee\Repositories\Interfaces\MenteeInterface;

class MenteeRepository extends RepositoriesAbstract implements MenteeInterface
{
    function getMenteesCountByRegionWise() {
         /* 
        SELECT `regions`.`name`, COUNT(ME.id) FROM `regions` 
        LEFT JOIN `district` D ON D.region_id = `regions`.id 
        LEFT JOIN `entrepreneurs` E ON E.district_id = D.id
        LEFT JOIN `mentees` ME ON ME.entrepreneur_id = E.id
        GROUP BY `regions`.id;
        */
        $regions = \Impiger\MasterDetail\Models\Region::pluck('name');
        $result['menteesTotalCount'] = 0;
        foreach ($regions as $region) {
            $result[strtolower($region)] = 0;
        }
        
        $select = [
            \DB::raw('IFNULL(COUNT(ME.id),0) AS cnt')
        ];
        $data1 = \Impiger\MasterDetail\Models\Region::select($select)->leftJoin('district AS D', 'D.region_id', '=', 'regions.id')
        ->leftJoin('entrepreneurs AS E', function($join){
            $join = $join->on('E.district_id','=','D.id');
        })->leftJoin('mentees AS ME', function($join){
            $join = $join->on('ME.entrepreneur_id','=','E.id');
        })->first();

        if($data1) {
            $result['menteesTotalCount'] = $data1->cnt;
        }

        $select[] = \DB::raw("regions.name AS title");

        $data2 = \Impiger\MasterDetail\Models\Region::select($select)->leftJoin('district AS D', 'D.region_id', '=', 'regions.id')
        ->leftJoin('entrepreneurs AS E', function($join){
            $join = $join->on('E.district_id','=','D.id');
        })->leftJoin('mentees AS ME', function($join){
            $join = $join->on('ME.entrepreneur_id','=','E.id');
        })->groupBy('regions.id')->get()->toArray();

        if($data2) {
            foreach ($data2 as $item) {
                $result[strtolower($item['title'])] = $item['cnt'];
            }
        }
        
        return $result;
        
    }
}
