@extends('layouts.app')

@section('title', 'Detail Kartu E-Money')
@section('page-title', 'Detail Kartu E-Money')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Card Details -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $card->card_number }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $card->card_type_name }}</p>
            </div>
            <div class="flex gap-2">
                @if(auth()->user()->hasPermission('edit-card'))
                <a href="{{ route('cards.edit', $card) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg">
                    Edit
                </a>
                @endif
                <a href="{{ route('cards.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Seksi</p>
                <p class="text-lg font-semibold text-gray-900">{{ $card->section->name }}</p>
            </div>
            
            <div class="bg-indigo-50 rounded-lg p-4">
                <p class="text-sm text-indigo-600 mb-1">Saldo Saat Ini</p>
                <p class="text-2xl font-bold {{ $card->current_balance < 50000 ? 'text-red-600' : 'text-indigo-900' }}">
                    {{ $card->formatted_balance }}
                </p>
                @if($card->current_balance < 50000)
                <p class="text-xs text-red-600 mt-1">⚠️ Saldo rendah, perlu top-up</p>
                @endif
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $card->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $card->status == 'active' ? 'Aktif' : 'Tidak Aktif' }}
                </span>
            </div>
        </div>

        @if($card->notes)
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm font-medium text-yellow-800 mb-1">Catatan:</p>
            <p class="text-sm text-yellow-700">{{ $card->notes }}</p>
        </div>
        @endif

        <!-- Top Up Button -->
        @if(auth()->user()->hasPermission('edit-card'))
        <div class="mt-6 border-t border-gray-200 pt-6">
            <form method="POST" action="{{ route('cards.topup', $card) }}" class="flex items-end gap-4">
                @csrf
                <div class="flex-1">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Top Up Saldo</label>
                    <input type="number" id="amount" name="amount" min="10000" step="10000" placeholder="Jumlah top up (min Rp 10.000)" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                    Top Up
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Usage History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Penggunaan</h3>
            <p class="text-sm text-gray-600 mt-1">Daftar penggunaan kartu untuk perjalanan dinas</p>
        </div>

        @if($card->cardUsages->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SPD</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Awal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pemakaian</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Akhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($card->cardUsages as $usage)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $usage->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('business-trips.show', $usage->businessTrip) }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                {{ $usage->businessTrip->letter_number }}
                            </a>
                            <div class="text-xs text-gray-500">{{ $usage->businessTrip->employee_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                            {{ $usage->formatted_initial_balance }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-red-600">
                            - {{ $usage->formatted_usage_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold {{ $usage->final_balance < 50000 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $usage->formatted_final_balance }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $usage->usage_notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada riwayat penggunaan</h3>
            <p class="mt-1 text-sm text-gray-500">Kartu ini belum pernah digunakan untuk perjalanan dinas.</p>
        </div>
        @endif
    </div>
</div>
@endsection
