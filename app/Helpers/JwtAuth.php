<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

        public function __construct(){
            $this->key = 'esta-es-mi-clave-secreta-2343532423467872314'; //clave de cifrado
        }

       public function signup($email, $password, $getToken = null){

             $user = User::where(
                 array(
                     'email' => $email,
                     'password' => $password
                 ))->first(); //el primero que le llegue

                 $signup = false;
                 if(is_object($user)){
                     $signup = true;
                 }
                 if($signup){
                     //generar el token y devolverlo
                     $token = array(
   //el indice de un objeto de jwt se didentifica con: sub
                    'sub' => $user->id,
                    'email' =>  $user->email,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'iat' => time(),   //tiempo de creacion del token timestamp
                    'exp' => time() + (7 * 24 * 60 * 60) //tiemo de expiracion del tiempo le sumanos una semana 7 dias x 24 horas x 60 mini x 60 segu

                  );

                  $jwt = JWT::encode($token, $this->key, 'HS256'); //cifrado
                  $decoded = JWT::decode($jwt, $this->key, array('HS256'));

                      if(is_null($getToken)){
                          return $jwt;
                      }
                      else {
                          return $decoded;
                      }

                 }else{
                     //devolver error


                     return array('status' => 'error', 'message' => 'Login ha fallado!!');
                 }

       }

             public function checkToken($jwt, $getIdentity = false){
                $auth = false;

                //como puede generar varias exepciones meter dentro de try
                try{
                    $decoded = JWT::decode($jwt, $this->key, array('HS256'));

                }catch(\UnexpectedValueException $e){
                    $auth = false;
                }catch(\DomainException $e){
                    $auth = false;
                }
                   // si existe         y es un objeto            y existe el id (sub)
                if(isset($decoded) && is_object($decoded) && isset($decoded->sub)){
                    $auth = true;
                }else{
                    $auth = false;
                }

                if($getIdentity){
                    return $decoded;
                }

                return $auth;
             }

  //try para generar


}
