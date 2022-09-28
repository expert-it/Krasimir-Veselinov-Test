<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Fetch all user on dashboad
    public function index()
    {
        $data['page_title'] = "Manage Users";
        $data['users'] = User::orderBy('id','DESC')->get();
        return view('home',$data);
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
        $data['page_title'] = "Create User";
        $input = $request->all();
        if(count($input) > 0){
            $this->validate($request,[
                "name" => 'required|min:3|max:50',
                "email" => 'required',
                "phone" => 'required',
                'password' => 'min:6|required_with:confirm_password|same:confirm_password',
                'confirm_password' => 'min:6',
                "photo" => 'required',
            ]);
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
                return redirect()->back()->with('message','success|User Added Successfully.');    
            }
            else
                return redirect()->back()->with('message','warning|Error While Saving. Please try again.');    
        }
        return view('users.addUser', $data);
    }

    // Edit Exsisting User From Database
    public function editUser(Request $request, $id = null)
    {
        if($id==null || !is_numeric($id))
            return redirect()->back()->with('message','warning|This url is either wrong or expired.');
        if(!User::find($id))
            return redirect()->route('projects')->with('message','warning|User not found.');
        $input = $request->all();
        if(count($input) > 0){
            $this->validate($request,[
                "name" => 'required|min:3|max:50',
                "email" => 'required',
                "phone" => 'required',
                'password' => 'nullable|min:6|same:confirm_password',
                'confirm_password' => 'nullable|min:6',
                "photo" => 'nullable',
            ]);
            $user = User::find($id);
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
            if (User::whereId($id)->update($input))
                return redirect()->back()->with('message','success|User Added Successfully.');    
            else
                return redirect()->back()->with('message','warning|Error While Saving. Please try again.');    
        }
        $data['formData'] = User::find($id);
        // echo "<pre>"; print_r($data['formData']); die;
        $data['page_title'] = 'Edit User';
        return view('users.addUser', $data);
    }

    // View Exsisting User From Database
    public function viewUser(Request $request, $id = null)
    {
        if($id==null || !is_numeric($id))
            return redirect()->back()->with('message','warning|This url is either wrong or expired.');
        if(!User::find($id))
            return redirect()->back()->with('message','warning|User not found.');
        $data['formData'] = User::find($id);
        $data['page_title'] = 'View User';
        return view('users.viewUser', $data);
    }

    // Delete Exsisting User From Database
    public function deleteUser(Request $request, $id = null)
    {
        if($id==null || !is_numeric($id)){
            return redirect()->back()->with('message','warning|This url is either wrong or expired.');
        }
        else{
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
                return redirect()->back()->with('message','success|User Delected Successfully.');
            }
            else{
                return redirect()->back()->with('message','warning|Error While Deleting. Please try again.');
            }
        }
    }
}
