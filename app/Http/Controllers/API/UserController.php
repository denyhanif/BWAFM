<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    public function login(Request $request){
        try{
            $request->validate([
                'email'=>'email|required',
            ]);
        }
        catch{

        }
        finally{
            
        }
    }
}
