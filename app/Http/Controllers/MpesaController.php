<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\MpesaTransaction;
use App\Models\c2bCallbacks;
use Log;



class MpesaController extends Controller
{
    //Create password

    public function lipaNaMpesaPassword()
    {
        //timestamp
        $timestamp = Carbon::rawParse('now')->format('YmdHms');
        //passkey
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        //businessShortCode
        $bussinessShortCode = 174379;
        //generate password
        $mpesaPassword = base64_encode($bussinessShortCode.$passkey.$timestamp);

        return $mpesaPassword;
    }

    public function newAccessToken()
    {
        $consumerKey="zcHIjMYOD6FH8C0b27J38NCTMx9BNGMA";
        $consumerSecret="GWMEFGZOLzyJT47t";
        $credentials= base64_encode($consumerKey.":".$consumerSecret);
        $url="https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        //curl http request
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic ".$credentials,"Content-Type:application/json"));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token=json_decode($curl_response);
        curl_close($curl);
       
        return $access_token->access_token;  
    
    }

    public function stkPush(Request $request)
    {
        //  dd($request);  
        // dynamism
        
            $phone =  $request->phone;
            $formatedPhone = substr($phone, 1);
            $code = "254";
            $phonenumber = $code.$formatedPhone;
            $url="https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        
        $curl_post_data = [
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => '1',
            'PartyA' => '254799238995', 
            'PartyB' => 174379,
            'PhoneNumber' => '254799238995', 
            'CallBackURL' => 'https://5ceb-102-222-146-137.ngrok.io/api/stk/callback',
            'AccountReference' => "Gacheri",
            'TransactionDesc' => "Till Lipa na Mpesa"
        ];

        $data_data=json_encode($curl_post_data);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->newAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_data);
        // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 200);

        $curl_response = curl_exec($curl);
        $message=json_decode($curl_response);
        // return $curl_response;

        if(isset($message->errorMessage)){
            return $message->errorMessage;
        }else{
            return $message->CustomerMessage;
        }
        curl_close($curl);
        
    }

    public function MpesaResponse(Request $request)
    {
        $response = $request->getContent();

        $transaction = new MpesaTransaction;

        $mpesacallback = json_encode($response);
        $transaction->response = $mpesacallback;
        $data = json_decode($response);
        $resultcode = $data->Body->stkCallback->ResultCode;
        
        if($resultcode != "0")
        {
           return "You have cancelled your transaction";
        }
        else 
        {
            $transaction->save();
            $callbackdata = new c2bCallbacks;
            $callbackdata->MerchantRequestID=$data->Body->stkCallback->MerchantRequestID;
            $callbackdata->CheckoutRequestID=$data->Body->stkCallback->CheckoutRequestID;
            $callbackdata->ResultCode=$data->Body->stkCallback->ResultCode;
            $callbackdata->ResultDesc=$data->Body->stkCallback->ResultDesc;
            $metadata = $data->Body->stkCallback->CallbackMetadata;
            $callbackdata->transAmount=$metadata->Item[0]->Value;
            $callbackdata->MpesaReceiptNumber=$metadata->Item[1]->Value;
            $callbackdata->TransactionDate=$metadata->Item[3]->Value;
            $callbackdata->PhoneNumber=$metadata->Item[4]->Value;
            $callbackdata->save();      
        }
        // Log::info($resultcode);
    }

    