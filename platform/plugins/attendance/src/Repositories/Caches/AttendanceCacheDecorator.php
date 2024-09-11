<?php

namespace Impiger\Attendance\Repositories\Caches;

use Impiger\Support\Repositories\Caches\CacheAbstractDecorator;
use Impiger\Attendance\Repositories\Interfaces\AttendanceInterface;

class AttendanceCacheDecorator extends CacheAbstractDecorator implements AttendanceInterface
{
     /**
     * {@inheritDoc}
     */
    public function getAttendanceByCandidate($candidate = null)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    function getAttendancePresentCnt($trainingTitle, $candidateId) {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
