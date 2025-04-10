<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Resources\UserResource;
use CodebarAg\TwilioVerify\Facades\TwilioVerify;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Authentication API",
 *      description="API documentation for authentication",
 *      @OA\Contact(
 *          email="your-email@example.com"
 *      ),
 * )
 */
class AuthController extends ApiController
{

    protected $letters = [];
    protected $auth;
    public $apiKey = "";
    public $sendOtpUrl = "https://smspro.nikita.kg/api/otp/send";
    public $verifyOtpUrl = "https://smspro.nikita.kg/api/otp/verify";
    public $test_number = '+996123456789,+996987654321';


    public function __construct()
    {
        $this->letters = range('A', 'Z');
    }


    public function sendOtpTwillio(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->respondValidationError($validator->errors());
        }
        $phoneNumber = $request->phone;
        if ($phoneNumber === $this->test_number) {
            return response()->json([
                'otpsend' => true,
                'message' => "Send Successfully",
                "phone" => $phoneNumber,
                'otp_token' =>
                'testtoken'
            ], 200);
        }

        try {
            $verificationStart = TwilioVerify::start(to: $request->phone);
            return response()->json([
                'otpsend' => true,
                'message' => "Send Successfully",
                "phone" => $phoneNumber,
                'otp_token' => ''
            ], 200);
        } catch (\Exception $e) {
        }
        return response()->json([
            'otpsend' => false,
            'message' => "Otp does not send. please contact support team."
        ], 200);
    }

    public function verifyOtpTwillio(Request $request)
    {


        $validator = Validator::make($request->all(), [
            "phone" => 'required',
            'otp_code' => 'required|min:4|max:4'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $phoneNumber = $request->phone;
        $token = $request->otp_token;
        $otpCode = $request->otp_code;

        // test number
        if (in_array($phoneNumber, explode(',', $this->test_number)) && $otpCode == 1234) {
            $user = User::where('phone', $phoneNumber)->first();

            if (!$user) {
                $data = [
                    'phone' => $phoneNumber,
                    'password' => bcrypt(rand(00000, 9999999)),
                    'promocode' => mt_rand(10000000, 99999999),
                    'registered' => 0,
                ];
                $user = User::create($data);
                $type = "register";
            }
            Auth::login($user);
            $token = auth()->user()->createToken('RestaurantCustomerAuth')->accessToken;
            return response()->json(['otp' => true, 'type' => 'success', 'message' => 'Otp verified and login', 'token' => $token, 'user' => new UserResource($user)], 200);
        }


        try {
            $verificationCheck = TwilioVerify::check(to: $phoneNumber, code: $otpCode);
            $user = User::where('phone', $phoneNumber)->first();
            if (!$user) {
                $data = [
                    'phone' => $phoneNumber,
                    'password' => bcrypt(rand(00000, 9999999)),
                    'promocode' => mt_rand(10000000, 99999999),
                    'registered' => 0,
                ];
                $user = User::create($data);
                $type = "register";
            }
            Auth::login($user);
            $token = auth()->user()->createToken('B2BAuth')->accessToken;
            return response()->json(['otp' => true, 'type' => 'success', 'message' => 'Otp verified and login', 'token' => $token, 'user' => new UserResource($user)], 200);
        } catch (\Exception $e) {
            return response()->json(['otp' => false, 'type' => 'error', 'message' => $e->getMessage()], 200);
        }
        return response()->json(['otp' => false, 'type' => 'error', 'message' => 'Invalid OTP code'], 200);
    }

    public function testLogin(Request $request)
    {
        $user = Auth::loginUsingId($request->user_actual_id, true);
        $data['token'] =  $user->createToken('B2BAuth')->accessToken;
        $data['user'] =  new UserResource($user);
        $this->response['message']  =   'Login Successful';
        $this->response['data']     =   $data;
        return $this->respond($this->response);
    }

    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $phoneNumber = $request->phone;
        session()->put('phone', $phoneNumber);
        if (in_array($phoneNumber, explode(',', $this->test_number))) {
            return response()->json(['otpsend' => true, 'message' => "Send Successfully", "phone" => $phoneNumber, 'otp_token' => 'testtoken'], 200);
        }


        $transactionId = time();
        $ph = str_replace('+', '', $phoneNumber);
        $ph = str_replace(' ', '', $ph);
        $data = [
            "transaction_id" => $transactionId,
            "phone" => $ph
        ];

        $headers = [
            "X-API-KEY:  $this->apiKey",
            'Content-Type: application/json'
        ];

        try {
            $responseData = $this->sendCurlRequest($this->sendOtpUrl, $data, $headers);
            if (is_array($responseData)) {
                $status = $responseData['status'];
                if ($status === 7) {
                    return response()->json(['otpsend' => false, 'message' => "Invalid Phone Number"], 200);
                }
                if ($status === 0) {
                    $token = $responseData['token'];
                    session()->put('otp_token', $token);
                    return response()->json(['otpsend' => true, 'message' => "Send Successfully", "phone" => $phoneNumber, 'otp_token' => $token], 200);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Send OTP login error: ' . $e->getMessage());
        }
        return response()->json(['otpsend' => false, 'message' => "Otp does not send. please contact support team."], 200);
    }


    public function verifyOTPApi(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            "phone" => 'required',
            "otp_token" => "required",
            'otp_code' => 'required|min:4|max:4'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $phoneNumber = $request->phone;
        $token = $request->otp_token;
        $otpCode = $request->otp_code;

        // test number
        if (in_array($phoneNumber, explode(',', $this->test_number)) && $otpCode == 1234) {
            $user = User::where('phone', $phoneNumber)->first();

            if (!$user) {
                $data = [
                    'phone' => $phoneNumber,
                    'password' => bcrypt(rand(00000, 9999999)),
                    'promocode' => mt_rand(10000000, 99999999),
                    'registered' => 0,
                ];
                $user = User::create($data);
                $type = "register";
            }
            Auth::login($user);
            $token = auth()->user()->createToken('B2BAuth')->accessToken;
            return response()->json(['otp' => true, 'type' => 'success', 'message' => 'Otp verified and login', 'token' => $token, 'user' => new UserResource($user)], 200);
        }



        //real code
        $data = [
            "code" => $otpCode,
            "token" => $token
        ];
        $headers = [
            "X-API-KEY:  $this->apiKey",
        ];
        try {
            $responseData = $this->sendCurlRequest($this->verifyOtpUrl, $data, $headers);
            if (is_array($responseData)) {
                $status = $responseData['status'];
                if ($status === 0) {
                    $user = User::where('phone', $phoneNumber)->first();
                    $type = "login";
                    if (!$user) {
                        $data = [
                            'phone' => $phoneNumber,
                            'password' => bcrypt(rand(00000, 9999999)),
                            'promocode' => mt_rand(10000000, 99999999),
                            'registered' => 0,
                        ];
                        $user = User::create($data);
                        $type = "register";
                    }
                    Auth::login($user);
                    $token = auth()->user()->createToken('B2BAuth')->accessToken;
                    return response()->json([
                        'otp' => true,
                        'type' => $type,
                        'message' => 'Otp verified and login',
                        'token' => $token,
                        'user' => new UserResource($user)
                    ], 200);
                }
            }
        } catch (\Exception $e) {
            // Log or handle the exception appropriately
            \Log::error('verifyOTPApi login error: ' . $e->getMessage());
        }
        return response()->json(['otp' => false, 'type' => 'error', 'message' => 'Invalid OTP code'], 200);
    }


    function sendCurlRequest($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            return $error;
        } else {
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $responseData = json_decode($response, true);
            return $responseData;
        }
        curl_close($ch);
    }
}
