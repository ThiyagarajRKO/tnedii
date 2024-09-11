<?php

namespace Impiger\Attendance\Repositories\Interfaces;

use Impiger\Support\Repositories\Interfaces\RepositoryInterface;

interface AttendanceInterface extends RepositoryInterface
{
    public function getAttendanceByCandidate($student = null);
    function getAttendancePresentCnt($trainingTitle, $candidateId);
}
