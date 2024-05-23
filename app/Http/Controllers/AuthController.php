<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;
use App\Mail\SendOtp;
class AuthController extends Controller
{
    public function signup(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:15',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'data' => null, 'message' => 'validation error', 'errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
       
        $otp = rand(100000, 999999);
       $otpob = OTP::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(30),
            'otp_type' => 'signup'
        ]);

        Mail::to($user)->send(new SendOtp($otpob, $user));
        $token = $user->createToken('Token')->accessToken;
        $data = [
            "token" => $token,
            "user"  => $user
        ];
        return response()->json(["success"=>true, "data"=>$data, "message"=> "Sent an OTP to your email, please verify your email address."]);
    }

    public function verifyEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'data' => null, 'message' => 'validation error', 'errors' => $validator->errors()], 400);
        }

        $otpdata = Otp::where(['email'=>auth()->user()->email, 'otp'=>$request->otp,'otp_type'=>'signup'])->where('expires_at', ">", now())->orderBy('id', 'DESC')->first();
        if(!$otpdata)
            return response(["success"=>false, "data"=>null, "message"=>"Otp is invalid or expired"], 400);
        User::where('id', auth()->user()->id)->update(["email_verified_at"=>now()]);
        $otpdata->delete();
        return response(["success"=>true, "data"=>auth()->user(), "message"=>"Email verified"]);
    }

    public function getUserDetail(){
        return response(["success"=>true, "data"=>auth()->user(), "message"=>"User data fetched"]);
    }

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'data' => null, 'message' => 'validation error', 'errors' => $validator->errors()], 400);
        }
        $user = User::where('email' , $request->email)->first();
        if(!$user){
            return response(["success"=> false,"data"=>null,"message"=>"your Email address not found in our record please check your email address"]);
        }
        $otp = rand(100000, 999999);
        $otpob = OTP::create([
             'email' => $request->email,
             'otp' => $otp,
             'expires_at' => Carbon::now()->addMinutes(30),
             'otp_type' => 'forgotPassword'
         ]);
 
         Mail::to($user)->send(new SendOtp($otpob, $user));

         $data = [
            "user"  => $user
        ];
        return response()->json(["success"=>true, "data"=>$data, "message"=> "Sent an OTP to your email, please verify your email address."]);
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'data' => null, 'message' => 'validation error', 'errors' => $validator->errors()], 400);
        }

        $otpdata = Otp::where(['email'=>$request->email, 'otp'=>$request->otp,'otp_type'=>'forgotpassword'])->where('expires_at', ">", now())->orderBy('id', 'DESC')->first();
        if(!$otpdata)
            return response(["success"=>false, "data"=>null, "message"=>"Otp is invalid or expired"], 400);
        $user =  User::where('email', $request->email)->first();
        $user->update(["password"=>$request->password]);
        $token = $user->createToken('Token')->accessToken;
        $otpdata->delete();
        
        $data = [
            "user"  => $user,
            "token" =>$token
        ];

        return response(["success"=>true, "data"=>$data, "message"=>"Password Reset"]);



    }


    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|max:16',
            
        ]);

        if ($validator->fails()) {
            return response(['status' => false, 'data' => null, 'message' => 'validation error', 'errors' => $validator->errors()], 400);
        }
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
  
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('Token')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }








   
    }
