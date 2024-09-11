<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Session;
use Exception;
use App\Models\PaymentHistory;
use DB,Auth;

class RazorpayPaymentController extends Controller
{
    public function index()
    {        
        if(isset($_GET['id'])){
            $training_title_id = $_GET['id'];
            $training = DB::table('training_title')->where('id', $_GET['id'])->first();
            if($training) {
                $candidate = array(
                    'user_id' => Auth::id(), 
                );
                $entrepreneur = DB::table('entrepreneurs')->where($candidate)->first();
                // dd($candidate);
                if($entrepreneur) {
                    $training->candidate_name = $entrepreneur->name;
                    $training->candidate_email = $entrepreneur->email;
                    $training->candidate_mobile = ($entrepreneur->mobile) ? $entrepreneur->mobile : '';
                } else {
                    $training->candidate_name = '';
                    $training->candidate_email = '';
                    $training->candidate_mobile = '';
                }
                $training->payment_status = 0;
                $training->amount = ($training->fee_amount * 100);
                // dd($training);
                return view('razorpay.view',compact('training'))->with('payment', 0);
            }
        }  
        
        // if($training_fee_amount != $_GET['amount']) {
        //     return view('razorpay.view')->with('amount', $amount)->with('payment', 0)->with('error_chk', 'Invalid Amount');
        // }
        // if(isset($amount) && ($amount >= 100)) {
        //     return view('razorpay.view')->with('amount', $amount)->with('payment', 0)->with('notes', $notes);
        // } 
    }

    public function store(Request $request)
    {
        $input = $request->all();
  
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount'])); 

                \Log::info("Payment Response");
                // \Log::info(json_encode($response));

                 $payment_status = $response['status'];
                 $payment_id = $response['id']; 
                 $amount = $response['amount'];
                 $status = ($payment_status == 'captured') ? 1: 0;
                 if($payment_status == 'captured') {
                    Session::put('success', 'Payment Successful');
                 } else if($payment_status == 'failed') {
                    Session::put('error', 'Payment Failed');
                 } else {
                    Session::put('success', 'Payment Successful');
                 }
                 $data = new PaymentHistory();
                 $data->user_id = Auth::id();
                 $data->training_title_id = $response['notes']->training_title_id;
                 $data->payment_id = $payment_id;
                 $data->status = $status;
                 $data->response = $response->toArray();
                 $data->save();
                 \Log::info("Payment ".$payment_id);
                 $training = DB::table('training_title')->where('id', $response['notes']->training_title_id)->first();
                 if($training) {
                    $candidate = array(
                        'user_id' => Auth::id(), 
                    );
                    $entrepreneur = DB::table('entrepreneurs')->where($candidate)->first();
                    // dd($candidate);
                    if($entrepreneur) {
                        $training->candidate_name = $entrepreneur->name;
                        $training->candidate_email = $entrepreneur->email;
                        $training->candidate_mobile = ($entrepreneur->mobile) ? $entrepreneur->mobile : '';
                    } else {
                        $training->candidate_name = '';
                        $training->candidate_email = '';
                        $training->candidate_mobile = '';
                    }
                    $training->payment_status = $status;
                    $training->amount = ($training->fee_amount * 100);
                    \Log::info(json_encode($data->response['notes']));

                    // $onboard = (Array) $response['notes'];

                    $onboard['training_title_id'] = $response['notes']->training_title_id;
                    $onboard['annual_action_plan_id'] = $response['notes']->annual_action_plan_id;
                    $onboard['division_id'] = $response['notes']->division_id;
                    $onboard['financial_year_id'] = $response['notes']->financial_year_id;
                    $onboard['entrepreneur_id'] = $entrepreneur->id;
                    // {"action_paln_id":"69","training_title_id":"2159","division_id":"1","financial_year_id":"1"}  

                    $trainee = app(\Impiger\Entrepreneur\Repositories\Interfaces\TraineeInterface::class)->createOrUpdate($onboard);

                    // dd($training);
                    return view('razorpay.view',compact('training'))->with('payment', $payment_id);
                }
                // return view('razorpay.view')->with('amount', $amount)->with('payment', $payment_id);
                
            } catch (Exception $e) {
                return  $e->getMessage();
                Session::put('error',$e->getMessage());
                return redirect()->back();
            }
        }      
    }

    public function razorpay($data)
    {        
        if(is_array($data)){
            $training_title_id = $data['id'];
            $amount = $data['amount'];
        }
        // if(isset($_GET['amount'])){
        //     $amount = ($_GET['amount'] * 100);
        // } else {
        //     $amount = 0;
        // } 
        if(isset($data['id'])){
            $training_title_id = $data['id'];
            $result = DB::table('training_title')->where('id', $training_title_id)->first();
            // foreach ($result as $datum) {
            //     $training_fee_amount = $datum->fee_amount;
            // }
            $training_fee_amount = $result->fee_amount;
            
        }  else {
                $training_title_id = 0;
        }     
        $notes = array('training_id' => 2159, 'action_plan_id' => 68, 'user_id' => 4, 'division_id' => 1, 'financial_year_id' => 1);
        if($training_fee_amount != $data['amount']) {
            return view('razorpay.view')->with('amount', $amount)->with('payment', 0)->with('error_chk', 'Invalid Amount');
        }
        if(isset($amount) && ($amount >= 100)) {
            return view('razorpay.view')->with('amount', $amount)->with('payment', 0)->with('notes', $notes);
        } 
    }
}
