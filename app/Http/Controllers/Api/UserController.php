<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        return UserResource::collection($query->paginate(10));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data'    => new UserResource($user)
        ], 201); 
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data user',
            'data'    => new UserResource($user)
        ], 200);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diperbarui',
            'data'    => new UserResource($user)
        ], 200);
    }

    public function destroy(User $user)
    {

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus'
        ], 200);
    }
}