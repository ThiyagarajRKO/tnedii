<?php

namespace Impiger\Attendance\Repositories\Eloquent;

use Impiger\Support\Repositories\Eloquent\RepositoriesAbstract;
use Impiger\Attendance\Repositories\Interfaces\AttendanceInterface;

class AttendanceRepository extends RepositoriesAbstract implements AttendanceInterface
{
    function getAttendanceByCandidate($candidateId = null)
    {
        $candidateId = ($candidateId) ? $candidateId : getCandidateId(\Auth::id());
        $trainingTitle = \Impiger\Entrepreneur\Models\Trainee::leftJoin('financial_year', 'trainees.financial_year_id', '=', 'financial_year.id')
            ->join('training_title AS TT', function($join) {
                $join = $join->on('TT.id','=','trainees.training_title_id')
                        ;
            })
            ->where(['financial_year.is_running' => 1, 'trainees.entrepreneur_id' => $candidateId])
                    //->whereRaw('DATE(TT.training_start_date) >= CURDATE() AND DATE(TT.training_end_date) <= CURDATE()')
                    ->first();
//        dd($trainingTitle);
            if(!$trainingTitle || !$trainingTitle->training_title_id) {
            $result['totalDays'] = 0;
            $result['totalPresentDays'] = 0;
            $result['totalAbsentDays'] = 0;
            return $result;
        }
        $totalDays = $totalPresentDays = 0;

        $totalDays = \Impiger\Attendance\Models\Attendance::select([
            \DB::raw('IFNULL(COUNT(DISTINCT(attendance_date)),0) AS totalDays')
        ])->where([
            'training_title_id' => $trainingTitle->training_title_id,
            'financial_year_id' => $trainingTitle->financial_year_id,
            'entrepreneur_id' => $candidateId,
            'deleted_at' => NULL
        ])->groupBy('entrepreneur_id')->first();

        if ($totalDays) {
            $totalDays = $totalDays->totalDays;
        }
        $totalPresentDays = $this->getAttendancePresentCnt($trainingTitle, $candidateId);
        $result = [];
        $result['totalDays'] = $totalDays;
        $result['totalPresentDays'] = $totalPresentDays;
        $result['totalAbsentDays'] = ($totalDays - $totalPresentDays);
        
        return $result;
    }
    
    function getAttendancePresentCnt($trainingTitle, $candidateId)
    {
        $totalPresentDays = 0;
        $presentDays = \Impiger\Attendance\Models\Attendance::select([
            \DB::raw('SUM(present) AS presentDayCnt')
        ])->where([
           'training_title_id' => $trainingTitle->training_title_id,
            'financial_year_id' => $trainingTitle->financial_year_id,
            'entrepreneur_id' => $candidateId,
            'deleted_at' => NULL
        ])->groupBy('entrepreneur_id','attendance_date')->get()->toArray();

        if ($presentDays) {
            foreach($presentDays as $total){
                $totalPresentDays = $totalPresentDays + $total['presentDayCnt'];
            }
        }

        return $totalPresentDays;
    }
}
