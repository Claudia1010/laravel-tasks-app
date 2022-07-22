<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        try {
            Log::info("Creating a task");

            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => $validator->errors()
                    ],
                    400
                );
            };

            $title = $request->input('title');
            $userId = Auth()->user()->id;

            $task = new Task();
            $task->title = $title;
            $task->user_id = $userId;

            $task->save();


            return response()->json(
                [
                    'success' => true,
                    'message' => "Task created"
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error("Error creating task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => "Error creating tasks"
                ],
                500
            );
        }
    }
    public function getAllTasks()
    {
        try {
            Log::info("Getting all Tasks");

            $userId = auth()->user()->id;

            // $tasks = Task::query()->where('user_id', $userId)->get()->toArray();

            //tambien se puede usar el query usando las relaciones
            // $tasks = User::query()->find($userId)->tasks;

            //el ultimo tasks esta trayendo las tareas que corresponden al userId desde el modelo user.php
            $tasks = User::find($userId)->tasks;


            return response()->json(
                [
                    'success' => true,
                    'message' => "Get all Tasks",
                    'data' => $tasks
                ],
                200
            );
        } catch (\Exception $exception) {

            Log::error("Error getting task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => "Error getting tasks"
                ],
                500
            );
        }
    }

    public function getTaskById($id){
        try {
            $userId = auth()->user()->id;

            $task = Task::query()->where('id', $id)->where('user_id', $userId)->get()->toArray();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Task retrieved successfully",
                    'data' => $task
                ],
                200
            );

        } catch (\Exception $exception) {

            Log::error("Error getting task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => "Error getting task"
                ],
                500
            );
        }
    }

    public function deleteTask($id){

        try {

            $userId = auth()->user()->id;

            $task = Task::query()->where('id', $id)->where('user_id', $userId)->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Task retrieved successfully",
                    'data' => $task
                ],
                200
            );

        } catch (\Exception $exception) {

            Log::error("Error getting task: " . $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => "Error getting task"
                ],
                500
            );
        }
        
    }

    public function updateTask(Request $request, $id){
        try {

            $userId = auth()->user()->id;

            $task = Task::find($id);

            if (!$task) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => "Task doesn´t exists"
                    ],
                    404
                );
            }

            if ($userId != $task->user_id) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => "Unauthorize user"
                    ],
                    403
                );
            }

            $validator = Validator::make($request->all(), 
            [
                'title' => 'required|string|max:255',
                'status' => 'required|boolean'
            ]);
            
            if($validator->fails()){
                return response()->json($validator->errors()->toJson(),400);
            }


            $title = $request->input('title');

            $status = $request->input('status');

            if(isset($title)){

                $task->title = $title;

            }

            if (isset($status)){

                $task->status = $status;
            }

            $task->save();

            return response()->json(
                [
                    'success' => true,
                    'message' => "Task ".$id." updated successfully",
                    'data' => $task
                ],
                200
            );

        } catch (\Exception $exception) {
            Log::error("Error updating task: ". $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => "Error updating task"
                ],
                500
            );
        }
    }

    public function getUserByIdTask($id){
        try {

            //recupero la tarea q corresponde a ese Id, lo guardo en $task
            $task = Task::query()->find($id);

           /*con esa tarea voy al modelo user y por la relacion creada con task
            traigo el userId y lo guardo en $user*/
            $user = $task->user;

            return response()->json(
                [
                    'success' => true,
                    'message' => "Getting user with task ".$id,
                    'data' => $user
                ],
                200
            );
        } catch (\Exception $exception) {
            Log::error("Error getting user: ". $exception->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => "Error bringing user"
                ],
                500
            );
        }
    }
 }

