<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Mail\NewUserRegistered;
use App\Models\VerifyEmail;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * @param RegisterRequest $request
     * @return mixed
     */
    public function register(RegisterRequest $request)
    {
        try {
            Log::info('Start RegisterController:register');
            $data = $request->all();
            // Get some random bytes
            $token = random_bytes(8);
            $data['token'] = bin2hex($token);
            $newUser =  new User($data);
            $newUserCreated = $newUser->save();
            if (!$newUserCreated) {
                throw new \Exception('Unable to create new user.');
            }
            $newUser->verifyEmail()->create([
                'token' => $data['token'],
            ]);
            $mailData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'url' =>  env('APP_URL') . '/verifyEmail/' . $data['token']
            ];
            Mail::to($data['email'])->send(new NewUserRegistered($mailData));
            Log::info('End RegisterController:register:success');

            return response()->json([
                'user' => $newUser,
                'status' => 200,
            ]);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            Log::info('Catch for RegisterController:register');
            Log::error($message);
            Log::info('End RegisterController:register:error');

            return response()->json([
                'message' => $message,
                'status' => 400,
            ]);
        }
    }
}
