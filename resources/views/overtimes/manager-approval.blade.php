@extends('layouts.app')

@section('title', 'Approval Overtime - Manager')
@section('page-title', 'Approval Overtime (Manager - Tahap 2 Final)')

@section('content')
<div class="space-y-6" x-data="overtimeApproval()">
    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Dari</label>
                <input type="date" name="date_from" value="{{ $date_from }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Sampai</label>
                <input type="date" name="date_to" value="{{ $date_to }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="regular" {{ $type == 'regular' ? 'selected' : '' }}>Harian</option>
                    <option value="additional" {{ $type == 'additional' ? 'selected' : '' }}>Susulan</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg">
                    🔍 Tampilkan
                </button>
            </div>
        </form>
    </div>

    @if(count($grouped) > 0)
        <!-- Approval Summary -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-800">
                        <strong><span x-text="selectedCount"></span> karyawan dipilih</strong> untuk final approval
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button @click="approveSelected" 
                            x-show="selectedCount > 0"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">
                        ✅ Approve Final
                    </button>
                    <button @click="showRejectModal = true" 
                            x-show="selectedCount > 0"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                        ❌ Reject
                    </button>
                </div>
            </div>
        </div>

        <!-- Grouped Overtimes by Date -->
        @foreach($grouped as $dateKey => $dateGroup)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Date Header -->
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">
                        📅 {{ $dateGroup['date']->format('d F Y') }} ({{ $dateGroup['date']->format('l') }})
                    </h3>
                </div>

                <!-- Loop through Types -->
                @foreach($dateGroup['types'] as $typeKey => $typeGroup)
                    <div class="p-6 border-b last:border-b-0">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-semibold text-gray-700">
                                {{ $typeKey == 'regular' ? '🕐 OVERTIME HARIAN' : '📝 OVERTIME SUSULAN' }}
                            </h4>
                            <button @click="toggleSelectGroup('{{ $dateKey }}-{{ $typeKey }}')"
                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                ☑️ Select All
                            </button>
                        </div>

                        <!-- Loop through Batches -->
                        @foreach($typeGroup['batches'] as $batchKey => $batch)
                            <div class="mb-6 last:mb-0 bg-gray-50 rounded-lg p-4">
                                <!-- Batch Header with Supervisor Info -->
                                <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            👤 {{ $batch['creator']->name }} ({{ $batch['section']->name }})
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            Waktu: {{ substr($batch['start_time'], 0, 5) }} - {{ substr($batch['end_time'], 0, 5) }} WIB
                                        </p>
                                        @if($batch['overtimes']->first()->supervisorApprover)
                                            <p class="text-xs text-green-600 mt-1">
                                                ✅ Disetujui Supervisor: {{ $batch['overtimes']->first()->supervisorApprover->name }} 
                                                ({{ toUserTime($batch['overtimes']->first()->supervisor_approved_at, 'd M, H:i') }})
                                            </p>
                                        @endif
                                    </div>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        {{ $batch['overtimes']->count() }} karyawan
                                    </span>
                                </div>

                                <!-- Employees Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 w-10">
                                                    <input type="checkbox" 
                                                           @click="toggleSelectBatch('{{ $batchKey }}')"
                                                           class="w-4 h-4 text-indigo-600 rounded">
                                                </th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">No</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Nama Karyawan</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-600">Waktu</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-600">Total Jam</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-600">Deskripsi Pekerjaan</th>
                                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-600">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($batch['overtimes'] as $index => $overtime)
                                                <tr class="hover:bg-gray-50" data-batch="{{ $batchKey }}">
                                                    <td class="px-4 py-3">
                                                        <input type="checkbox" 
                                                               value="{{ $overtime->id }}"
                                                               @click="toggleSelect({{ $overtime->id }})"
                                                               :checked="selectedIds.includes({{ $overtime->id }})"
                                                               class="w-4 h-4 text-indigo-600 rounded overtime-checkbox">
                                                    </td>
                                                    <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 text-sm font-medium">{{ $overtime->employee_name }}</td>
                                                    <td class="px-4 py-3 text-sm text-center whitespace-nowrap">
                                                        {{ substr($overtime->start_time, 0, 5) }} - {{ substr($overtime->end_time, 0, 5) }}
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-center">{{ number_format($overtime->total_hours, 1) }} jam</td>
                                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $overtime->work_description }}</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button @click="openEditModal({{ $overtime->id }}, '{{ substr($overtime->start_time, 0, 5) }}', '{{ substr($overtime->end_time, 0, 5) }}', '{{ addslashes($overtime->work_description) }}', '{{ $overtime->employee_name }}')"
                                                                class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">
                                                            ✏️ Edit
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Batch Summary -->
                                <div class="mt-3 pt-3 border-t border-gray-200 text-sm text-gray-600">
                                    <strong>Total:</strong> {{ $batch['overtimes']->count() }} karyawan, 
                                    {{ number_format($batch['overtimes']->sum('total_hours'), 1) }} jam
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach

    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="text-gray-400 text-6xl mb-4">📭</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Tidak Ada Data Overtime</h3>
            <p class="text-gray-500">Tidak ada overtime yang menunggu approval Manager untuk periode yang dipilih.</p>
        </div>
    @endif

    <!-- Reject Modal -->
    <div x-show="showRejectModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showRejectModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6" @click.stop>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reject Overtime (Manager)</h3>
            <p class="text-sm text-gray-600 mb-4">
                Anda akan reject <strong><span x-text="selectedCount"></span> karyawan</strong>. 
                Silakan masukkan alasan penolakan:
            </p>
            
            <textarea x-model="rejectionReason"
                      rows="4"
                      placeholder="Contoh: Budget tidak mencukupi, tidak sesuai prosedur, dll."
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 mb-4"></textarea>
            
            <div class="flex justify-end space-x-3">
                <button @click="showRejectModal = false; rejectionReason = ''"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button @click="rejectSelected"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    Reject
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Individual Overtime Modal -->
    <div x-show="showEditModal" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showEditModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 p-6" @click.stop>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                ✏️ Edit Overtime - <span x-text="editEmployeeName"></span>
            </h3>
            
            <form @submit.prevent="saveEdit">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                            <input type="time" 
                                   x-model="editStartTime"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                            <input type="time" 
                                   x-model="editEndTime"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan</label>
                        <textarea x-model="editWorkDescription"
                                  rows="4"
                                  required
                                  maxlength="500"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" 
                            @click="showEditModal = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        💾 Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function overtimeApproval() {
    return {
        selectedIds: [],
        showRejectModal: false,
        rejectionReason: '',
        showEditModal: false,
        editOvertimeId: null,
        editStartTime: '',
        editEndTime: '',
        editWorkDescription: '',
        editEmployeeName: '',
        
        get selectedCount() {
            return this.selectedIds.length;
        },
        
        toggleSelect(id) {
            const index = this.selectedIds.indexOf(id);
            if (index > -1) {
                this.selectedIds.splice(index, 1);
            } else {
                this.selectedIds.push(id);
            }
        },
        
        toggleSelectBatch(batchId) {
            const batchCheckboxes = document.querySelectorAll(`tr[data-batch="${batchId}"] input[type="checkbox"]`);
            const allChecked = Array.from(batchCheckboxes).every(cb => this.selectedIds.includes(parseInt(cb.value)));
            
            batchCheckboxes.forEach(checkbox => {
                const id = parseInt(checkbox.value);
                if (allChecked) {
                    const index = this.selectedIds.indexOf(id);
                    if (index > -1) this.selectedIds.splice(index, 1);
                } else {
                    if (!this.selectedIds.includes(id)) this.selectedIds.push(id);
                }
            });
        },
        
        toggleSelectGroup(groupKey) {
            const allCheckboxes = document.querySelectorAll('.overtime-checkbox');
            const allIds = Array.from(allCheckboxes).map(cb => parseInt(cb.value));
            const allSelected = allIds.every(id => this.selectedIds.includes(id));
            
            if (allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = allIds;
            }
        },
        
        async approveSelected() {
            if (this.selectedCount === 0) {
                alert('Pilih minimal 1 karyawan untuk di-approve');
                return;
            }
            
            if (!confirm(`Final Approve ${this.selectedCount} karyawan? Ini adalah approval terakhir.`)) {
                return;
            }
            
            try {
                const response = await fetch('{{ route("overtimes.manager.bulk-approve") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        overtime_ids: this.selectedIds
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message + '\n\nPDF dapat di-generate dari menu Overtime → Generate PDF');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat approve');
            }
        },
        
        async rejectSelected() {
            if (this.selectedCount === 0) {
                alert('Pilih minimal 1 karyawan untuk di-reject');
                return;
            }
            
            if (!this.rejectionReason.trim()) {
                alert('Alasan penolakan harus diisi');
                return;
            }
            
            try {
                const response = await fetch('{{ route("overtimes.manager.bulk-reject") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        overtime_ids: this.selectedIds,
                        rejection_reason: this.rejectionReason
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat reject');
            }
        },
        
        openEditModal(overtimeId, startTime, endTime, workDescription, employeeName) {
            this.editOvertimeId = overtimeId;
            this.editStartTime = startTime;
            this.editEndTime = endTime;
            this.editWorkDescription = workDescription;
            this.editEmployeeName = employeeName;
            this.showEditModal = true;
        },
        
        async saveEdit() {
            if (!this.editStartTime || !this.editEndTime || !this.editWorkDescription.trim()) {
                alert('Semua field harus diisi');
                return;
            }
            
            // Validate end time is after start time
            if (this.editEndTime <= this.editStartTime) {
                alert('Jam selesai harus lebih besar dari jam mulai');
                return;
            }
            
            try {
                const response = await fetch(`/overtimes/${this.editOvertimeId}/update-individual`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        start_time: this.editStartTime,
                        end_time: this.editEndTime,
                        work_description: this.editWorkDescription
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan perubahan');
            }
        }
    }
}
</script>
@endpush

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
