@extends('layouts.app')

@section('title', 'Upload Signature')
@section('page-title', 'Upload Signature untuk E-Sign')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800">✅ {{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-800">❌ {{ session('error') }}</p>
        </div>
    @endif

    <!-- Current Signature Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">✍️ Signature Saat Ini</h2>
        
        @if($user->hasSignature())
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-6 flex justify-center items-center border border-gray-200">
                    <img src="{{ $user->signature_url }}" 
                         alt="Signature {{ $user->name }}" 
                         class="max-h-32 border border-gray-300 bg-white p-2 rounded">
                </div>
                
                <div class="text-sm text-gray-600">
                    <p><strong>Filename:</strong> {{ basename($user->signature_path) }}</p>
                    <p><strong>Upload:</strong> {{ toUserTime($user->updated_at, 'd M Y, H:i') }}</p>
                </div>
                
                <form method="POST" action="{{ route('profile.signature.delete') }}" 
                      onsubmit="return confirm('Hapus signature ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                        🗑️ Hapus Signature
                    </button>
                </form>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-400 text-6xl mb-4">📝</div>
                <p class="text-gray-600">Belum ada signature diupload</p>
            </div>
        @endif
    </div>

    <!-- Upload Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            {{ $user->hasSignature() ? '🔄 Upload Signature Baru' : '➕ Upload Signature' }}
        </h2>
        
        <form method="POST" action="{{ route('profile.signature.upload') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <!-- File Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Signature <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="signature" 
                           accept="image/png,image/jpeg,image/jpg"
                           required
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border file:border-gray-300
                                  file:text-sm file:font-medium
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100
                                  cursor-pointer">
                    @error('signature')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Requirements Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 font-medium mb-2">📋 Ketentuan File Signature:</p>
                    <ul class="text-sm text-blue-700 space-y-1 ml-4 list-disc">
                        <li>Format: PNG, JPG, atau JPEG</li>
                        <li>Ukuran maksimal: 2 MB</li>
                        <li>Dimensi minimal: 200 x 100 pixels</li>
                        <li>Background transparan (PNG) disarankan</li>
                        <li>Gambar jelas dan tidak blur</li>
                    </ul>
                </div>
                
                <!-- Tips -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-sm text-yellow-800 font-medium mb-2">💡 Tips Membuat Signature Digital:</p>
                    <ul class="text-sm text-yellow-700 space-y-1 ml-4 list-disc">
                        <li>Tandatangan di kertas putih dengan spidol hitam</li>
                        <li>Foto/scan dengan pencahayaan baik</li>
                        <li>Gunakan tool online untuk remove background (jika PNG)</li>
                        <li>Crop agar hanya signature yang terlihat</li>
                    </ul>
                </div>
                
                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg">
                        💾 Upload Signature
                    </button>
                    <a href="{{ route('dashboard') }}" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-3 px-6 rounded-lg">
                        Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-md font-semibold text-gray-800 mb-3">ℹ️ Tentang E-Signature</h3>
        <div class="text-sm text-gray-600 space-y-2">
            <p>
                Signature yang diupload akan digunakan untuk:
            </p>
            <ul class="list-disc ml-6 space-y-1">
                <li><strong>Supervisor:</strong> Approval tahap 1 pada laporan overtime PDF</li>
                <li><strong>Manager:</strong> Final approval tahap 2 pada laporan overtime PDF</li>
            </ul>
            <p class="mt-3 text-yellow-700 bg-yellow-50 p-3 rounded border border-yellow-200">
                ⚠️ <strong>Penting:</strong> Signature ini bersifat legal dan akan tercetak di dokumen resmi. 
                Pastikan signature Anda jelas dan sesuai dengan tanda tangan asli.
            </p>
        </div>
    </div>

</div>
@endsection
