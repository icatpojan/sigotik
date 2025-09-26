<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfUser;
use App\Models\ConfGroup;
use App\Models\MUpt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $groups = ConfGroup::all();
        $upts = MUpt::all();

        return view('users.index', compact('groups', 'upts'));
    }

    /**
     * Get users data via AJAX
     */
    public function getUsers(Request $request)
    {
        $query = ConfUser::with(['group', 'upt', 'kapals']);

        // Search functionality - includes kapal search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhereHas('kapals', function ($kapalQuery) use ($search) {
                        $kapalQuery->where('nama_kapal', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('conf_group_id', $request->role);
        }

        // Filter by UPT
        if ($request->has('upt') && $request->upt) {
            $query->where('m_upt_code', $request->upt);
        }

        // Per page parameter
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'has_more_pages' => $users->hasMorePages(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:conf_user,username',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:conf_user,email',
            'password' => 'required|string|min:6',
            'group_id' => 'required|exists:conf_group,conf_group_id',
            'upt_id' => 'nullable|exists:m_upt,code',
            'nip' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the highest ID and add 1
            $maxId = ConfUser::max('conf_user_id') ?? 0;
            $newId = $maxId + 1;


            $user = ConfUser::create([
                'conf_user_id' => $newId,
                'username' => $request->username,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'conf_group_id' => $request->group_id,
                'm_upt_code' => $request->upt_id,
                'nip' => $request->nip,
                'golongan' => $request->golongan,
                'is_active' => 1,
                'date_insert' => now(),
                'user_insert' => auth()->user()->conf_user_id ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat',
                'user' => $user->load(['group', 'upt'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ConfUser $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user->load(['group', 'upt'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConfUser $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:conf_user,username,' . $user->conf_user_id . ',conf_user_id',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:conf_user,email,' . $user->conf_user_id . ',conf_user_id',
            'password' => 'nullable|string|min:6',
            'group_id' => 'required|exists:conf_group,conf_group_id',
            'upt_id' => 'nullable|exists:m_upt,code',
            'nip' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [
                'username' => $request->username,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'conf_group_id' => $request->group_id,
                'm_upt_code' => $request->upt_id,
                'nip' => $request->nip,
                'golongan' => $request->golongan,
                'date_update' => now(),
                'user_update' => auth()->user()->conf_user_id ?? 1,
            ];

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'user' => $user->load(['group', 'upt'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConfUser $user)
    {
        try {
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
