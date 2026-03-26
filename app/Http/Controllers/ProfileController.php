<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show signature upload page
     */
    public function signature()
    {
        $user = Auth::user();
        
        return view('profile.signature', [
            'user' => $user
        ]);
    }
    
    /**
     * Upload or update user signature
     */
    public function uploadSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048', // 2MB max
        ], [
            'signature.required' => 'File signature harus diupload',
            'signature.image' => 'File harus berupa gambar',
            'signature.mimes' => 'Format yang diperbolehkan: PNG, JPG, JPEG',
            'signature.max' => 'Ukuran maksimal 2MB',
        ]);
        
        $user = Auth::user();
        
        // Validate image dimensions (minimum 200x100 pixels)
        $imageInfo = getimagesize($request->file('signature')->getRealPath());
        if ($imageInfo[0] < 200 || $imageInfo[1] < 100) {
            throw ValidationException::withMessages([
                'signature' => 'Dimensi gambar minimal 200x100 pixels'
            ]);
        }
        
        // Delete old signature if exists
        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }
        
        // Store new signature
        $file = $request->file('signature');
        $filename = 'signature_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('signatures', $filename, 'public');
        
        // Update user signature path
        $user->signature_path = $path;
        $user->save();
        
        return redirect()->route('profile.signature')
            ->with('success', 'Signature berhasil diupload');
    }
    
    /**
     * Delete user signature
     */
    public function deleteSignature()
    {
        $user = Auth::user();
        
        if (!$user->signature_path) {
            return redirect()->route('profile.signature')
                ->with('error', 'Tidak ada signature untuk dihapus');
        }
        
        // Delete file from storage
        Storage::disk('public')->delete($user->signature_path);
        
        // Remove path from database
        $user->signature_path = null;
        $user->save();
        
        return redirect()->route('profile.signature')
            ->with('success', 'Signature berhasil dihapus');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'password.required'         => 'Password baru harus diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password saat ini tidak sesuai.',
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.password')
            ->with('success', 'Password berhasil diubah.');
    }
}
