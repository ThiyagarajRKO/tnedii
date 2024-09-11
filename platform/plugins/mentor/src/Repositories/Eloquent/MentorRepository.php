<?php

namespace Impiger\Mentor\Repositories\Eloquent;

use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\Mentor\Repositories\Interfaces\MentorInterface;

class MentorRepository extends RepositoriesAbstract implements MentorInterface
{
    function getMentorsCountByRegionWise() {
        /* 
       SELECT `regions`.`name`, COUNT(MR.id) FROM `regions` 
       LEFT JOIN `district` D ON D.region_id = `regions`.id 
       LEFT JOIN `entrepreneurs` E ON E.district_id = D.id
       LEFT JOIN `mentors` MR ON MR.entrepreneur_id = E.id
       GROUP BY `regions`.id;
       */
       $regions = \Impiger\MasterDetail\Models\Region::pluck('name');
       $result['mentorsTotalCount'] = 0;
       foreach ($regions as $region) {
           $result[strtolower($region)] = 0;
       }
       
       $select = [
           \DB::raw('IFNULL(COUNT(MR.id),0) AS cnt')
       ];
       $data1 = \Impiger\MasterDetail\Models\Region::select($select)->leftJoin('district AS D', 'D.region_id', '=', 'regions.id')
       ->leftJoin('entrepreneurs AS E', function($join){
           $join = $join->on('E.district_id','=','D.id');
       })->leftJoin('mentors AS MR', function($join){
           $join = $join->on('MR.entrepreneur_id','=','E.id');
       })->first();

       if($data1) {
           $result['mentorsTotalCount'] = $data1->cnt;
       }

       $select[] = \DB::raw("regions.name AS title");

       $data2 = \Impiger\MasterDetail\Models\Region::select($select)->leftJoin('district AS D', 'D.region_id', '=', 'regions.id')
       ->leftJoin('entrepreneurs AS E', function($join){
           $join = $join->on('E.district_id','=','D.id');
       })->leftJoin('mentors AS MR', function($join){
           $join = $join->on('MR.entrepreneur_id','=','E.id');
       })->groupBy('regions.id')->get()->toArray();

       if($data2) {
           foreach ($data2 as $item) {
               $result[strtolower($item['title'])] = $item['cnt'];
           }
       }
       
       return $result;
       
   }
}
