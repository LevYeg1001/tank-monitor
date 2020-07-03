<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function index()
    {
        return view('Auth/login');
    }

    public function login(LoginRequest $request)
    {
        try {
            Log::info('Start LoginController:login');
            $data = $request->all();
            $attemptSuccess = Auth::attempt($data);
            if (!$attemptSuccess) {
                throw new \Exception('Unable to login');
            }
            $user = Auth::user();

            Log::info('End LoginController:login:success');

            return response()->json([
                'success' => true,
                'token' => $user->api_token,
                'user' => $user,
                'status' => 200,
            ]);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::info('Catch for LoginController:login');
            Log::error($message);
            Log::info('End LoginController:login:error');

            return response()->json([
                'message' => $message,
                'status' => 400,
            ]);
        }
    }
}
