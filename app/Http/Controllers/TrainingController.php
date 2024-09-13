<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use DB;
use App\Models\TrainingApplicants;
use App\Utils\CrudHelper;

class TrainingController extends Controller
{
    public function viewTrainingApplicants()
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
                    'contact_no',
                    'photo',
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
            ], 400); // 500 Internal Server Error
        }
    }

    function addTrainingApplicant(Request $request)
    {
        try {
            // Save the new applicant
            $trainingApplicant = new TrainingApplicants;
            $trainingApplicant->training_type = $request->input('training_type');
            $trainingApplicant->candidate_type = $request->input('candidate_type');
            $trainingApplicant->name = $request->input('name');
            $trainingApplicant->email = $request->input('email');
            $trainingApplicant->prefix = $request->input('prefix');
            $trainingApplicant->care_of = $request->input('care_of');
            $trainingApplicant->father_mother_husband_name = $request->input('father_mother_husband_name');
            $trainingApplicant->gender = $request->input('gender');
            $trainingApplicant->date_of_birth = $request->input('date_of_birth');
            $trainingApplicant->aadhaar_no = $request->input('aadhaar_no');
            $trainingApplicant->physically_challenged = $request->input('physically_challenged');
            $trainingApplicant->community = $request->input('community');
            $trainingApplicant->qualification = $request->input('qualification');
            $trainingApplicant->address = $request->input('address');
            $trainingApplicant->district = $request->input('district');
            $trainingApplicant->pincode = $request->input('pincode');
            $trainingApplicant->contact_no = $request->input('contact_no');
            $trainingApplicant->agree_to_privacy_notice = $request->input('agree_to_privacy_notice');

            // Save the record (created_at and updated_at will be automatically handled)
            $trainingApplicant->save();

            CrudHelper::uploadFiles($request, $trainingApplicant);

            return response()->json([
                'success' => true,
                'message' => trans('core/base::notices.create_success_message'),
                'data' => [
                    'training_applicant_id' => $trainingApplicant->id,
                ],
            ], 201); // 201 Created
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400); // 500 Internal Server Error
        }
    }

    public function downloadApplicants(Request $request)
    {
        try {
            $query = DB::table('training_applicants')
                ->select(
                    DB::raw('training_type as `Training Type`'),
                    DB::raw('candidate_type as `Candidate Type`'),
                    DB::raw('name as `Name`'),
                    DB::raw('email as `Email`'),
                    DB::raw('prefix as `Prefix`'),
                    DB::raw('care_of as `Care Of`'),
                    DB::raw('father_mother_husband_name as `Father/Mother/Husband Name`'),
                    DB::raw('gender as `Gender`'),
                    DB::raw('date_of_birth as `Date of Birth`'),
                    DB::raw('aadhaar_no as `Aadhaar No`'),
                    DB::raw('physically_challenged as `Physically Challenged`'),
                    DB::raw('community as `Community`'),
                    DB::raw('qualification as `Qualification`'),
                    DB::raw('address as `Address`'),
                    DB::raw('district as `District`'),
                    DB::raw('pincode as `Pincode`'),
                    DB::raw('contact_no as `Contact No`')
                );

            $count = $query->count();

            $rows = $query->get();


            return response()->json(compact('count', 'rows'));

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400); // 500 Internal Server Error
        }
    }

}
