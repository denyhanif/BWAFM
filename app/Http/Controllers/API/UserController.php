<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function login(Request $request){
        try{
            // validasi input
            $request->validate([
                'email'=>'email|required',
            ]);
            //Check kredensial login
            $crdentials = request(['email','password']);
            if(!Auth::attempt($crdentials)){
                return ResponseFormatter::error([
                    'message'=>'Unauthorized'
                ],'Authentication Failed',500);
            }
            //jika tiak berhasil maka throw error
            $user = User::where('email',$request->email)->first();
            if(!Hash::check($request->password,$user->password,[])){
                throw new \Exception('Invalid Credentials');
            }

            // jika berhasil maka buat token
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type'=> 'Bearer',
                'user' =>$user
            ],'Authenticated');
        }
        catch(Exception $error){
            return  ResponseFormatter::error([
                'message'=> 'Something wnt wrong',
                'error'=> $error
            ],'Auth Failed',500);

        }
        finally{
            
        }
    }
}
