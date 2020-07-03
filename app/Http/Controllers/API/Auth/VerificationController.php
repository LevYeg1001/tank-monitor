<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verifyEmail($token)
    {
        try {
            Log::info('Start VerificationController:verifyEmail');
            $verifyEmail = VerifyEmail::where('token', $token)->first();
            if (!$verifyEmail) {
                throw new \Exception('Invalid token for email verification');
            }
            $user = $verifyEmail->user;
            $user->email_verified_at = Carbon::now();
            $user->save();
            $verifyEmail->delete();
            Log::info('End VerificationController:verifyEmail:success');

            return response()->json([
                'message' => 'Email successfully verified.',
                'user' => $user,
                'status' => 200,
            ]);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::info('Catch for VerificationController:verifyEmail');
            Log::error($message);
            Log::info('End VerificationController:verifyEmail:error');

            return response()->json([
                'message' => $message,
                'status' => 400,
            ]);
        }
    }
}
