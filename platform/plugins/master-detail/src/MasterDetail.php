<?php

namespace Impiger\MasterDetail;

use Impiger\MasterDetail\Repositories\Interfaces\MasterDetailInterface;
use Illuminate\Support\Arr;
use App\Utils\CrudHelper;
use Request;
use Carbon\Carbon;

class MasterDetail
{
    /**
     * @var MasterDetailInterface
     */
    protected $masterDetailRepository;

    /**
     * MasterDetailgroups constructor.
     * @param MasterDetailInterface $userRepository
     */
    public function __construct(MasterDetailInterface $masterDetailRepository)
    {
        $this->masterDetailRepository = $masterDetailRepository;
    }

    public function getAcademicYearIdByDate($date)
    {
        $date = ($date) ? $date : date('Y-m-d');
        $ayData = \Impiger\MasterDetail\Models\AcademicYears::get();
        $ayId = 0;

        foreach ($ayData as $ay) {
            $startDate = Carbon::createFromFormat('M-Y', $ay->session_start)->format('Y-m-01');
            $endDate = Carbon::createFromFormat('M-Y', $ay->session_end)->format('Y-m-30');

            if (($date >= $startDate) && ($date <= $endDate)) {
                return $ay->id;
            }

            if ($ay->is_running) {
                $ayId = $ay->id;
            }
        }

        return $ayId;
    }
}
