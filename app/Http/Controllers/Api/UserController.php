<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:conf_user,email,' . $user->conf_user_id . ',conf_user_id',
            'nip' => 'nullable|string|max:255',
            'golongan' => 'nullable|string|max:255',
            'ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];

            if ($request->has('nama_lengkap')) {
                $updateData['nama_lengkap'] = $request->nama_lengkap;
            }

            if ($request->has('email')) {
                $updateData['email'] = $request->email;
            }

            if ($request->has('nip')) {
                $updateData['nip'] = $request->nip;
            }

            if ($request->has('golongan')) {
                $updateData['golongan'] = $request->golongan;
            }

            // Handle TTD file upload
            if ($request->hasFile('ttd')) {
                $ttdFile = $request->file('ttd');
                $ttdName = 'ttd_' . $user->conf_user_id . '_' . time() . '.' . $ttdFile->getClientOriginalExtension();

                // Create directory if not exists
                $ttdPath = public_path('images/ttd');
                if (!file_exists($ttdPath)) {
                    mkdir($ttdPath, 0755, true);
                }

                // Move file to images/ttd
                $ttdFile->move($ttdPath, $ttdName);
                $updateData['ttd'] = $ttdName;
            }

            $updateData['date_update'] = now();
            $updateData['user_update'] = $user->conf_user_id;

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate',
                'user' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
