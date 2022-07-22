<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    const ROLE_SUPER_ADMIN = 3;

    public function addSuperAdminRoleToUser($id){
            try {
                //trayendome el usuario y almacenandolo en user, objeto user de tipo model user
                $user = User::find($id);
                //al obj user añadele el role super admin
                $user->roles()->attach(self::ROLE_SUPER_ADMIN);
    
                return response()->json(
                    [
                        'success' => true,
                        'message' => "Adding super admin role to user ".$id,
                        'data' => $user
                    ],
                    200
                );
            } catch (\Exception $exception) {
                Log::error("Error adding super admin role to user: ". $exception->getMessage());
    
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Error adding super admin role to user"
                    ],
                    500
                );
            }
     }

     public function removeSuperAdminRoleToUser($id){
        try {
            //trayendome el usuario y almacenandolo en user, es un objeto user de tipo model user
            $user = User::find($id);
            //al obj user añadele el role super admin
            $user->roles()->detach(self::ROLE_SUPER_ADMIN);

            return response()->json(
                [
                    'success' => true,
                    'message' => "Removing super admin role to user ".$id,
                    'data' => $user
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error("Error removing super admin role to user: ". $exception->getMessage());
    
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Error removing super admin role to user"
                    ],
                    500
                );
        }
    }
}

