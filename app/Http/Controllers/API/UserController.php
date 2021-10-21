<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Actions\Fortify\PasswordValidationRules;

class UserController extends Controller
{
    //
    use PasswordValidationRules;//validasi bawaan fortify
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
        
    }

    public function register(Request $request){

        try{
            $request->validate([
                'name'=> ['required','string','max:255'],
                'email'=> ['required','string','email','unique:users'],
                'password'=> $this->passwordRules()
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'houseNumber'=> $request->houseNumber,
                'phoneNumber' => $request->phoneNumber,
                'city' => $request->city,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email',$request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainToken;

            return ResposeFormatter::success([
                'access_token'=>$tokenResult,
                'token_type'=>'Bearer',
                'user'=> $user
            ]);
        }
        catch(Exception $error){
            return ResponseFormatter::error([
                'message' =>'Something went wrong',
                'error' => $error
            ],'Authentication Failed',500);

        }

    }

    public function logout(Request $request){
        //ambil siapa yg login
 
        $token = $request->user()->currentAccessToken()->detele();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    //mengambil user yang sedang login
    public function fetch(Request $request){
        return ResponseFormatter::success($request->user(),'data user barhasil diambial');
    }

    public function upfateProfil(Request $request){
        $data = $request->all();

        $user= Auth::user();
        $user->update($data);

        return ResponseFormatter::success($user,'Profile Update');
    } 
    public function updatePhoto(Request $request){
        $validator = Validator::make($request->all(),[
            'file'=>'required|image|max:2048'
        ]);

        if($validator->fails()){
            return ResponseFormatter::error(
                ['error'=> $validator->errors()],
                'Update photo fails',
                401
            );
        }
        if($request->file('file')){
            $file = $request->file->store('assets/user','public');

            //simapn foto ke db 
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();

            return ResponseFormatter::success([$file],'File Sucessfully uploaded');
        }
    }
}
