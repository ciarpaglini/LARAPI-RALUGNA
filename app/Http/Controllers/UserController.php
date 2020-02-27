<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use App\User;

class UserController extends Controller
{
    public function register(Request $request){
       //recoger post


        $json = $request->input('json', null);
        $params = json_decode($json);

        $email = (!is_null($json) && isset($params->email)) ? $params->email : null;
        $name = (!is_null($json) && isset($params->name)) ? $params->name : null;
        $surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
        $role = 'ROLE_USER';
        $password = (!is_null($json) && isset($params->password)) ? $params->password : null;

        if(!is_null($email)  && !is_null($password)  && !is_null($name)){

            //crear el usurio
            $user = new User();
            $user ->email = $email;
            $user ->name = $name;
            $user ->password = $password;
            $user ->surname = $surname;
            $user ->role = $role;

            $pwd = hash('sha256', $password);
            $user->password = $pwd;

            //comprobar el ususrio duplicado
            $isset_user = User::where('email', '=', $email)->count();
           // echo $isset_user; die();
            if($isset_user == 0){


                //guardar usuario
                $user->save();

                $data = array(
                  'status'=>'success',
                  'code' => 200,
                  'message' => 'Usuario regitrado correctamente'

                );
            }else{
                 //no guardado porque ya existe
                 $data = array(
                    'status'=>'error',
                    'code' => 400,
                    'message' => 'Usuario duplicado, no puede registrarse'
                 );

            }


        }else{
            $data = array(
              'status' => 'error',
              'code' => 400,
              'message' => 'Usuario no creado'

            );
        }
        return response() ->json($data, 200);

    }


    public function login(Request $request){
       $jwtAuth = new JwtAuth();

       //recibir por post
       $json = $request->input('json', null);
       $params = json_decode($json);
                      //condiciones ternarias
       $email = (!is_null($json) && isset($params->email))  ?  $params->email : null;
       $password = (!is_null($json) && isset($params->password))  ?  $params->password : null;
       $getToken = (!is_null($json) && isset($params->gettoken))  ?  $params->gettoken : null;

       //cifrar la password
       $pwd = hash('sha256', $password);

       if(!is_null($email) && !is_null($password)&& ($getToken == null || $getToken == 'false')){
           $signup = $jwtAuth -> signup($email, $pwd);



       }elseif($getToken != null){
        $signup = $jwtAuth -> signup($email, $pwd, $getToken);


       }else{
        $signup = array(

            'status' => 'error',
            'message' => 'Envia tus datos por post'
        );
       }
       return response()->json($signup, 200);


    }

}
