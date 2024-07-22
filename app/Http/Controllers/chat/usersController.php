<?php

namespace App\Http\Controllers\chat;

use App\Http\Controllers\Controller;
use App\Http\Controllers\responseController;
use App\Models\chat\usersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class usersController extends Controller
{
    public function checkSession()
    {
        if (session_status() == PHP_SESSION_NONE)
        {
            session_start();
        }
    }
    //List Users
    public function indexDataUsers(Request $request)
    {
        return usersModel::indexDataUser($request);
    }
    //Add Users
    public function storeDataUsers(Request $request){
       $validator = Validator::make(
          $request->all(),
          [
            'name'     => 'required',
            'email'    => 'required',
            'password' => 'required'
          ]
       );
        if($validator->fails())
            {
                return responseController::client($validator->getMessageBag()->toArray());
            }
        else
            {
               $result = usersModel::storeDataUsers($request);
               return $result;
            }
    }
}
