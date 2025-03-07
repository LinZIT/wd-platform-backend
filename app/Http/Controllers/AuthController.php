<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\GeneralActionRecord;
use App\Models\Role;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function get_all_online_users(Request $request)
    {

        $users = User::with('department')->whereHas('status', function ($query) {
            $query->where('description', 'Activo');
        })
            // ->whereHas('role', function ($query) {
            //     $query->where('description', 'Usuario');
            // })
            ->where(['isOnline' => 1])
            ->orWhere(['isOnline' => 0])
            ->orderBy('isOnline', 'DESC')
            ->get();
        return response()->json(['status' => true, 'data' => $users]);
    }
    public function get_all_offline_users(Request $request)
    {

        $users = User::with('department')->whereHas('status', function ($query) {
            $query->where('description', 'Activo');
        })
            // ->whereHas('role', function ($query) {
            //     $query->where('description', 'Usuario');
            // })
            ->where('isOnline', 0)
            ->get();
        return response()->json(['status' => true, 'data' => $users]);
    }
    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }
        $user = User::with('role', 'status', 'department')->where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->isOnline = 1;
        $user->save();
        $user->token = $token;
        $G_A_R = GeneralActionRecord::create([
            'description' => "El usuario $user->names $user->surnames ($user->document) inició sesión. ($user->email)",
            'importance' => 'Normal',
            'author' => 'WD-System',
        ]);
        $G_A_R->user()->associate($user);
        $G_A_R->save();
        return response()->json([
            'status' => true,
            'message' => 'Bienvenido ' . $user->names . ' ' . $user->surnames,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
    /**
     * Cerrar sesion
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $G_A_R = GeneralActionRecord::create([
            'description' => "El usuario $user->first_name $user->lastname ($user->document) cerró sesión. ($user->email)",
            'importance' => 'Leve',
            'author' => 'WD-System',
        ]);

        $user_disconnected = User::find($user->id);
        $user_disconnected->isOnline = 0;
        $user_disconnected->save();
        $G_A_R->user()->associate($user);
        $G_A_R->save();
        $request->user()->currentAccessToken()->delete();
        return [
            'status' => true,
            'message' => 'Has cerrado sesion exitosamente'
        ];
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'names' => 'required|string|max:255',
            'surnames' => 'string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'document' => 'required|string|max:20|unique:users',
            'address' => 'required|string',
            'password' => 'required|string|min:8',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'names' => $request->names,
            'surnames' => $request->surnames,
            'phone' => $request->phone,
            'email' => $request->email,
            'document' => $request->document,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'color' => '#C0EA0F',
        ]);
        // Obtener status activo o crear status si no existe
        $status = Status::firstOrNew(['description' => 'Activo']);
        $status->save();

        // Se asocia el status al usuario
        $user->status()->associate($status);

        // Obtener rol cliente o crear rol si no existe
        $role = Role::firstOrNew(['description' => 'Usuario']);
        $role->save();

        // Se asocia el rol al usuario
        $user->role()->associate($role);
        $department = Department::where('id', $request->department)->first();
        $user->department()->associate($department);
        // Se guarda el usuario
        $user->save();

        // Token de auth
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json(['data' => $user, 'token' => $token, 'token_type' => 'Bearer', 'status' => true]);
    }
    /**
     * Registrar administrador de condominios
     */
    public function register_master(Request $request)
    {
        // return response()->json($request);

        $validator = Validator::make($request->all(), [
            'names' => 'string|max:255',
            'surnames' => 'string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'document' => 'required|string|max:20|unique:users',
            'address' => 'required|string',
            'password' => 'required|string|min:8',
            'level' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }
        try {
            //code... 
            $user = User::create([
                'names' => $request->names,
                'surnames' => $request->surnames,
                'phone' => $request->phone,
                'email' => $request->email,
                'document' => $request->document,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'level' => $request->level,
                'color' => '#C0EA0F',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['status' => false, 'errors' => $th->getMessage()], 400);
        }
        $user = User::create([
            'names' => $request->names,
            'surnames' => $request->surnames,
            'phone' => $request->phone,
            'email' => $request->email,
            'document' => $request->document,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'color' => '#C0EA0F',
        ]);
        // Obtener status activo o crear status si no existe
        $status = Status::firstOrNew(['description' => 'Activo']);
        $status->save();

        // Se asocia el status al usuario
        $user->status()->associate($status);

        // Obtener rol cliente o crear rol si no existe
        $role = Role::firstOrNew(['description' => 'Master']);
        $role->save();

        // Se asocia el rol al usuario
        $user->role()->associate($role);
        $department = Department::where('id', $request->department)->first();
        $user->department()->associate($department);
        // Se guarda el usuario
        $user->save();

        // Token de auth
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json(['data' => $user, 'token' => $token, 'token_type' => 'Bearer', 'status' => true]);
    }

    public function change_color(Request $request, User $user)
    {
        if (!$request->color) {
            return response()->json(['status' => false, 'message' => 'El color es obligatorio'], 400);
        }
        $user->color = $request->color;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Se ha cambiado el color'], 200);
    }
    public function change_theme(Request $request, User $user)
    {
        if (!$request->theme) {
            return response()->json(['status' => false, 'message' => 'El tema es obligatorio'], 400);
        }
        $user->theme = $request->theme;
        $user->save();

        return response()->json(['status' => true, 'message' => 'Se ha cambiado el tema'], 200);
    }
    public function get_logged_user_data(Request $request)
    {
        $data = $request->user();
        $token = $request->bearerToken();
        $user = User::with('status', 'role', 'department')->where('id', $data->id)->first();
        $user->token = $token;
        return response()->json(['user' => $user]);
    }
    public function get_user_by_id(Request $request, User $user)
    {
        $user_data = User::with('status', 'role', 'department')->where('id', $user->id)->first();
        return response()->json(['status' => true, 'data' => $user]);
    }
    public function get_it_users_paginated(Request $request, Ticket $ticket)
    {
        $users = User::select('color', 'status_id', 'id', 'department_id', 'names', 'surnames')
            ->with('status', 'department')
            ->whereHas('department', function ($query) {
                $query = Department::where('description', 'IT');
            })
            ->whereDoesntHave('tickets', function ($query) use ($ticket) {
                $query->where('tickets.id', $ticket->id);
            })
            ->paginate();

        return response()->json(['status' => true, 'data' => $users]);
    }

    public function change_password(Request $request, User $user)
    {
        try {
            $newUser = User::where('id', $user->id)->first();
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'errors' => $th->getMessage()], 500);
        }
        if ($request->password != $request->confirmPassword) {
            return response()->json(['status' => false, 'errors' => ['password' => 'Las contraseñas no coinciden']], 400);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
            'confirmPassword' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }
        $new_password = $request->password;
        $newUser->password = Hash::make($request->password);
        $newUser->save();
        $G_A_R = GeneralActionRecord::create([
            'description' => "El usuario $newUser->names $newUser->surnames ($newUser->document) cambio su contraseña, pass: $new_password. ($newUser->email)",
            'importance' => 'Alta',
            'author' => 'WD-System',
        ]);
        $G_A_R->user()->associate($newUser);
        $G_A_R->save();
        return response()->json(['status' => true, 'message' => 'Se ha cambiado la contraseña', 'user' => $newUser], 200);
    }
    public function index()
    {
        return response()->json(['status' => true]);
    }
}
