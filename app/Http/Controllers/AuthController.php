<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PHPUnit\Exception;

class AuthController extends Controller {
    public function loginView(){
        return view('auth.login');
    }

    public function login(Request $request){
        $this->validate($request, [
            'username' => 'required',
        ],[
            'username.required' => 'Username is Required!',
        ]);

        Log::channel('auth')->info($request->input('username'). ' TRY LOGIN!');

        try {
            // Check user status
            $user = DB::table('users')
                ->where('username', '=', $request->input('username'))
                ->first();

            if($user->status_id != 1){
                Log::channel('auth')->info($request->input('username'). ' ATTEMPT FAILED! USER NON-ACTIVE!');
                return redirect()->back()->withInput()->withErrors([
                    'Username or Password is invalid!'
                ]);
            }

            if(Auth::guard('web')->attempt(['username' => $request->input('username'), 'password' => $request->input('login')['password']], $request->has('remember'))){
                Log::channel('auth')->info($request->input('username'). ' ATTEMPT SUCCESS!');
                return redirect()->intended('/');
            }
            else {
                Log::channel('auth')->info($request->input('username'). ' ATTEMPT FAILED!');
                return redirect()->back()->withInput()->withErrors([
                    'Username or Password is invalid!'
                ]);
            }
        } catch (\Exception $exception){
            Log::channel('auth')->error($exception);
            return redirect()->back()->withInput()->withErrors([
                'Something went wrong!'
            ]);
        }
    }

    public function logout(Request $request){
        Auth::guard('web')->logout();

        return redirect()->route('login');
    }



    // PASSWORD

    public function changePassword(){
        return view('auth.change_password');
    }

    public function updatePassword(Request $request){
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ], [
            'old_password.required' => 'Old Password is required!',
            'password.required' => 'New Password is required!',
            'password.min' => 'Password needs to be 6 characters minimum!',
            'confirm_password.required' => 'Confirm New Password is required!',
            'confirm_password.same' => "Password dan Confirm Password doesn't match!",
        ]);

        try {
            if(!Hash::check($request->input('old_password'), Auth::user()->password)){
                return \redirect()->back()->withErrors([
                    'Old Password is invalid!'
                ]);
            }

            Auth::user()->update([
                'password' => Hash::make($request->input('password'))
            ]);

            return redirect()->back()->with([
                'success' => 'Successfully changed your password!'
            ]);
        } catch (Exception $exception){
            Log::channel('auth')->error($exception);

            return redirect()->back()->withErrors([
                'Something went wrong!'
            ]);
        }
    }
}
