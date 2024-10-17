<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //

    public function register(Request $request){
        $validation=[
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required','unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ];
        Validator::make($request->all(),$validation)->validate();

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);
        $user->ip_address = $request->ip();
        $user->user_agent=$request->server("HTTP_USER_AGENT");
        $user->save();
        Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'account_number' => UUIDGenerate::accountNumber(),
                'amount' => 0,
            ]
        );


        $token = $user->createToken('E_Wallet')->accessToken;

        return success('You are Successfully Register',['token' => $token]);

    }

    public function login(Request $request){

        $validation=[
            'email' => ['required', 'string', 'email', 'max:255',],
            'password' => ['required', 'string', 'min:8'],
        ];
        Validator::make($request->all(),$validation)->validate();

        if (Auth::attempt(['email' => $request->email,'password' => $request->password])) {
            $user = auth()->user();

            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0,
                ]
            );

            $token = $user->createToken('E_Wallet')->accessToken;
            return success('You are Successfully Login',['token' => $token]);
        }
        return fail('These credentials do not match our records.',null);
    }

    public function logout(){
        $user = Auth::user();

        $user->token()->revoke();

        return success('You are Successfully Logout',null);

    }
}
