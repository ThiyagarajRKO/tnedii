<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use DB;

class TrainingController extends Controller
{
    public function trainingApplicants()
    {
        return view('training-applicants.index');
    }

    public function getTrainingApplicants(Request $request)
    {
        try {
            $start = $request->start;
            $length = $request->length;
            $search = $request['search']['value'] ?? '';

            $query = DB::table('training_applicants')
                ->select(
                    'training_type',
                    'candidate_type',
                    'name',
                    'email',
                    'prefix',
                    'care_of',
                    'father_mother_husband_name',
                    'gender',
                    'date_of_birth',
                    'aadhaar_no',
                    'physically_challenged',
                    'community',
                    'qualification',
                    'address',
                    'district',
                    'pincode',
                    'contact_no'
                )
                ->when($search, function ($query, $search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('training_type', 'LIKE', "%{$search}%")
                            ->orWhere('candidate_type', 'LIKE', "%{$search}%")
                            ->orWhere('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('prefix', 'LIKE', "%{$search}%")
                            ->orWhere('care_of', 'LIKE', "%{$search}%")
                            ->orWhere('father_mother_husband_name', 'LIKE', "%{$search}%")
                            ->orWhere('gender', 'LIKE', "%{$search}%")
                            ->orWhere('date_of_birth', 'LIKE', "%{$search}%")
                            ->orWhere('aadhaar_no', 'LIKE', "%{$search}%")
                            ->orWhere('physically_challenged', 'LIKE', "%{$search}%")
                            ->orWhere('community', 'LIKE', "%{$search}%")
                            ->orWhere('qualification', 'LIKE', "%{$search}%")
                            ->orWhere('address', 'LIKE', "%{$search}%")
                            ->orWhere('district', 'LIKE', "%{$search}%")
                            ->orWhere('pincode', 'LIKE', "%{$search}%")
                            ->orWhere('contact_no', 'LIKE', "%{$search}%");
                    });
                });

            $count = $query->count();

            $rows = $query
                ->orderBy('created_at', 'DESC')
                ->limit($length > 0 ? $length : 10)
                ->offset($start)->get();


            return response()->json(compact('count', 'rows'));

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500); // 500 Internal Server Error
        }
    }

}
