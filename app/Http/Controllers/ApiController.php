<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    // Fetch all user on dashboad
    public function getUsers()
    {
        return User::all();
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

    // Add New User to Database
    public function addUser(Request $request)
    {
        $input = $request->all();
        if(count($input) > 0){
            $validator = Validator::make(request()->all(), [
                "name" => 'required|min:3|max:50',
                "email" => 'required|unique:users,email',
                "phone" => 'required',
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'min:6',
                "photo" => 'required'
            ]);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 200);
            }
            if($request->hasFile('photo')) {
                $image = $request->file('photo');
                $name = uniqid().date("dmyhis").'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('storage/images/');
                $image->move($destinationPath, $name);
                $input['photo']=$name;
            }
            unset($input['confirm_password']);
            $input['password'] = Hash::make($input['password']);
            $user = new User($input);
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
                return response()->json($user, 200);
            }
            else
                return response()->json(['message'=>'Error While Creating. Please try again.'], 500);
        }
        return response()->json(['message'=>'Bad Request.'], 400);
    }

    // Edit Exsisting User From Database
    public function editUser(Request $request, $id = null)
    {
        if($id==null || !is_numeric($id))
            return response()->json(['message'=>'Bad Request.'], 400);
        if(!User::find($id))
            return response()->json(['message'=>'This url is either wrong or expired.'], 404);

        $input = $request->all();
        if(count($input) > 0){
            $user = User::find($id);
            $validator = Validator::make(request()->all(), [
                'email' => 'required|unique:users,email,'.$user->id,
                "name" => 'required|min:3|max:50',
                "phone" => 'required',
                'password' => 'nullable|min:6|same:confirm_password',
                'confirm_password' => 'nullable|min:6',
                "photo" => 'nullable',
            ]);
            if($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 200);
            }
            if($request->hasFile('photo')) {
                $image = $request->file('photo');
                $name = uniqid().date("dmyhis").'.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('storage/images/');
                $image->move($destinationPath, $name);
                $file_path = $destinationPath.$user->photo;
                if(isset($input['set_image']) && file_exists(@$file_path))
                    unlink($file_path);
                $input['photo'] = $name;
            }else{
                $input['photo'] = $user->photo;
            }
            if($input['password'] != "")
                $input['password'] = Hash::make($input['password']);
            else
                $input['password'] = $user->password;
            // echo "<pre>"; print_r($input); die;
            unset($input['confirm_password'],$input['_token']);
            $user = User::whereId($id)->update($input);
            if($user)
                return User::find($id);
            else
                return response()->json(['message'=>'Error While Updating. Please try again.'], 500);
        }
        return User::find($id);
    }

    // View Exsisting User From Database
    public function viewUser(Request $request, $id = null)
    {
        if($id==null || !is_numeric($id))
            return response()->json(['message'=>'Bad Request.'], 400);
        if(!User::find($id))
            return response()->json(['message'=>'This url is either wrong or expired.'], 404);
        return User::find($id);
    }

    // Delete Exsisting User From Database
    public function deleteUser(Request $request, $id = null)
    {
        if($id==null || !is_numeric($id))
            return response()->json(['message'=>'Bad Request.'], 400);
        if(!User::find($id))
            return response()->json(['message'=>'This url is either wrong or expired.'], 404);
        $user = User::find($id);
        if(User::find($id)->delete()){
            $details = [
                'subject' => 'Account Deleted',
                'title' => 'Hello '.$user->name.',',
                'body' => 'Your account deleted, Please contact again.',
                'mail_type' => 'new_user'
            ];
            \Mail::to($user->email)->send(new \App\Mail\Mailer($details));
            $message = $details['title'].' '.$details['body'];
            $this->sendMessage($user->phone, $message);
            return response()->json(['message'=>'Deleted Successfully'], 200);
        }
        else
            return response()->json(['message'=>'Error While Deleting. Please try again.'], 500);
    }
}
