<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\MpesaTransaction;


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
    
        // Http request

        // $request = new HttpRequest();
        // $request->setUrl('https://sandbox.safaricom.co.ke/oauth/v1/generate');
        // $request->setMethod(HTTP_METH_POST);

        // $request->setQueryData(array(
        //         'grant_type' => 'client_credentials'
        //     ));

        // $request->setHeaders(array(
        //     'Content-Type:application/json',
        //     'Authorization:Bearer '.$this->newAccessToken())
        // );

        // try {
        //      $response = $request->send();

        //       echo $response->getBody();
        //     }       
        //     catch (HttpException $ex) {
        //         echo $ex;
        //     }

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
        // dd($request);  
        // dynamism
        
            $phone =  $request->phone;
            $formatedPhone = substr($phone, 1);
            $code = "254";
            $phoneNumber = $code.$formatedPhone;

        $url="https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        
        $curl_post_data = [
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => '1',
            'PartyA' => $phoneNumber, 
            'PartyB' => 174379,
            'PhoneNumber' => $phoneNumber, 
            'CallBackURL' => 'https://b10e-102-222-146-133.ngrok.io/api/stk/callback',
            'AccountReference' => "Gacheri",
            'TransactionDesc' => "Till Lipa na Mpesa"
        ];

        $data_string=json_encode($curl_post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$this->newAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        // curl_setopt($curl, CURLOPT_TIMEOUT_MS, 200);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        return $curl_response;
        
        // $curl_response = curl_exec($curl);
        // if($curl_response = curl_exec($curl))curl_close($curl);{
        //     return $curl_response;
            
        //  }
        //  return "STK PUSH FAILED";
    }

    public function MpesaResponse(Request $request)
    {
        $response = $request->getContent();

        $transaction = new MpesaTransaction;
        $transaction->response = json_encode($response);
        $transaction->save();

    }
    public function confirm(){

    }
}
