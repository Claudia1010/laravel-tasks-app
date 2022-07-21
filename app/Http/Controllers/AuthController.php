<?php

/*vamos a meter todas las funciones aqui que van a tener relacion con la 
autentificacion de un usuario en mi app (por ej. register y login)*/

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), 
        [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->password)
        ]);
        
        $token = JWTAuth::fromUser($user);
        
        return response()->json(compact('user','token'),201);
    }

    public function login(Request $request)
    {
        //el request y only esta creando un array con esos campos espec y lo guarda en input
        $input = $request->only('email', 'password');
        $jwt_token = null;
        //devuelve un token con las credenciales que le hemos pasado y comprueba que son validas
        //devolviendo true o false, devolviendo un mensaje, y si es true un token si es true,
        // y si es false devuelve el token con un null
        if (!$jwt_token = JWTAuth::attempt($input)) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid Email or Password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
            ]);
    }

    public function logout(Request $request){
        //funcion validate, asegurarse q le estamos pasando ese token
        $this->validate($request, [
        'token' => 'required'
        ]);
        try {
            //invalidate para invalidar el token
            JWTAuth::invalidate($request->token);
            
            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);

        } catch (\Exception $exception) {
            
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me(){
       //muestra el perfil del usuario luego de hacer una autentificacion
        return response()->json(auth()->user());;
    }

}
