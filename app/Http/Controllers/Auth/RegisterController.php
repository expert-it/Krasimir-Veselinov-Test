<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Twilio\Rest\Client;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            "name" => 'required|min:3|max:255',
            "email" => 'required|unique:users,email',
            "phone" => 'required',
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6',
            "photo" => 'required'
        ]);
    }
    private function sendMessage($receiverNumber, $message)
    {
        try {
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_TOKEN");
            $twilio_number = getenv("TWILIO_FROM");
            $client = new Client($account_sid, $auth_token);
            $client->messages->create( $receiverNumber, [ 'from' => $twilio_number, 'body' => $message] );
            return true;
        }
        catch (Exception $e) {
            // dd("Error: ". $e->getMessage());
            return false;
        }
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $request = app('request');
        if($data['photo']) {
            $image = $request->file('photo');
            $name = uniqid().date("dmyhis").'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('storage/images/');
            $image->move($destinationPath, $name);
            $data['photo']=$name;
        }
        unset($data['confirm_password'],$data['_token']);
        $data['password'] = Hash::make($data['password']);
        $user = new User($data);
        if ($user->save()){
            $details = [
                'subject' => 'Registration Success',
                'title' => 'Hello '.$user->name.',',
                'body' => 'Your account  successfully created.',
                'mail_type' => 'new_user'
            ];
            \Mail::to($user->email)->send(new \App\Mail\Mailer($details));
            $message = $details['title'].' '.$details['body'];
            $this->sendMessage($user->phone, $message);
            return $user;
        }
        else
            return false;


        // return User::create([
        //     'name' => $data['name'],
        //     'email' => $data['email'],
        //     'password' => Hash::make($data['password']),
        // ]);
    }
}
