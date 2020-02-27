<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Helpers\JwtAuth;
use App\Foto;
use App\User;





class FotoController extends Controller
{
    public function index(){
       $fotos = Foto::all();
       return response()->json(array(

              'fotos' => $fotos,
              'status' => 'success'
       ), 200);


    }
    public function show($id){
        $foto = Foto::find($id);
  if(is_object($foto)){
        $foto = Foto::find($id)->load('user');
        return response()->json(array('foto' => $foto, 'status' => 'success'), 200);
    }else{
        return response()->json(array('message' => 'El coche no existe', 'status' => 'error'), 400);

    }
}


    public function store(Request $request){

            $hash = $request->header('Authorization', null);

            $jwtAuth = new JwtAuth();
            $checkToken = $jwtAuth->checkToken($hash);

            if($checkToken){
                //reg¡coger datos por post
                 $json = $request->input('json', null);
                 $params = json_decode($json);
                 $params_array = json_decode($json, true);
                 //conseguir el ususrio identificado
                $user = $jwtAuth->checkToken($hash, true);

               // $request->merge($params_array);

                  // $validate = $this->validate($request,[
                    $validate =  Validator::make($params_array,[
                    'title' => 'required|min:5',
                    'description' => 'required',
                    'imagen' => 'required',
                    'status' => 'required'

                ]);
                   if($validate->fails()){
                       return response()->json($validate->errors(), 400);
                   }
               /* $errors = $validate->errors();
                if($errors){
                    return $errors->toJson();
                }*/
                //guarda la foto
                $foto = new Foto();
                $foto -> user_id = $user -> sub;
                $foto -> title = $params->title;
                $foto -> description = $params->description;
                $foto -> status = $params->status;
                $foto -> imagen = $params->imagen;


                $foto->save();

                $data = array(
                    'foto' => $foto,
                    'status'=> 'success',
                    'code' => 200,
                );

               //devolver error
            }else{
                $data = array(
                    'message' => 'Login incorrecto',
                    'status' => 'error',
                'code' => 400,
            );
            }
            return response()->json($data, 200);
        }


       public function update($id, Request $request){

        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
        //recoger parametros post

        $json = $request->input('json', null);
        $params = json_decode($json); //convertir en objeto de php
        $params_array = json_decode($json, true); //convertir en array
        //validar los datos

        $validate =  Validator::make($params_array,[
            'title' => 'required|min:5',
            'description' => 'required',
            'imagen' => 'required',
            'status' => 'required'

        ]);
        //actualizar le registro
        $foto = foto::where('id', $id)->update($params_array);

        $data = array(
          'foto'=> $params,
          'status' => 'success',
          'code' => 200
        );


          //actualiza el coche

        }else{
                $data = array(
                    'message' => 'Login incorrecto',
                    'status' => 'error',
                'code' => 400,
            );
            }
            return response()->json($data, 200);


       }

    public function destroy($id, Request $request){

        $hash = $request->header('Authorization', null);

        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($hash);

        if($checkToken){
       //comporbar que existe el regitro
       $foto = Foto::find($id);

       //borralo
      $foto->delete();
       //devolverlo
       $data = array(
          'foto'-> $foto,
          'status' => 'success',
          'code'=> 200

       );
        }else{

            $data = array(
              'status' -> error,
              'code' => 400,
              'message' => 'Login incorrecto !!'


            );
        }
        return response()->json($data, 200);

    }
}
