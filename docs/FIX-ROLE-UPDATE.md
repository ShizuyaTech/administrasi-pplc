# 🔧 PERBAIKAN: Database Tidak Update Saat Edit Role

## ❌ MASALAH YANG DITEMUKAN

User tidak bisa mengubah role melalui UI karena:

1. **Form `users/edit.blade.php`** tidak memiliki field untuk pilih role
2. **UpdateUserRequest** tidak validate `role_id`
3. **UserController::update()** tidak menyimpan `role_id`

Akibatnya, meskipun Anda ubah role di form (jika manually edit HTML), database tidak akan update.

---

## ✅ PERBAIKAN YANG DILAKUKAN

### 1. Update Validation Request
**File:** `app/Http/Requests/UpdateUserRequest.php`

**Ditambahkan:**
```php
'role_id' => ['required', 'exists:roles,id'],
```

### 2. Update Controller
**File:** `app/Http/Controllers/UserController.php`

**Method `edit()`:**
- Tambah query untuk ambil semua role
- Pass `$roles` ke view

**Method `update()`:**
- Tambah `'role_id' => $request->role_id` di array update

### 3. Update Form Edit
**File:** `resources/views/users/edit.blade.php`

**Ditambahkan:**
- Field dropdown untuk pilih role
- Hapus display role dari info karyawan (karena sekarang bisa diubah)

---

## 🧪 CARA TEST PERBAIKAN

### Test 1: Update Role via UI

1. **Login sebagai Super Admin**
   ```
   Email: admin@iuse-ippi.com
   Password: password
   ```

2. **Buka Manajemen User**
   ```
   Sidebar → Settings → Manajemen User
   ```

3. **Edit User Budi Prasetyo**
   - Klik tombol "Edit" pada user Budi Prasetyo
   - Anda akan melihat dropdown **"Role"** di form
   - Pilih role yang diinginkan (misal: Driver)
   - Klik "Update"

4. **Verifikasi Perubahan**
   ```bash
   php check-user.php sunan.jaya@iuse-ippi.com
   ```

5. **Test Login**
   - Logout dari Super Admin
   - Login sebagai Budi Prasetyo
   - Periksa sidebar - hanya menu sesuai permission role baru

---

### Test 2: Update Role via Command Line (Alternatif)

Jika UI masih bermasalah, gunakan script:

```bash
php update-user-role.php sunan.jaya@iuse-ippi.com Driver
php check-user.php sunan.jaya@iuse-ippi.com
```

---

## 📋 CHECKLIST SETELAH PERBAIKAN

Setiap kali update role user:

- [ ] Clear cache: `php artisan optimize:clear`
- [ ] Verifikasi: `php check-user.php [email]`
- [ ] Minta user logout (jika sedang login)
- [ ] User login ulang dengan Incognito Mode
- [ ] Periksa sidebar - menu sesuai permission role baru

---

## 🎯 CARA KERJA SETELAH PERBAIKAN

### Edit User via UI (Sekarang Bisa Update Role!)

1. **Admin Edit User:**
   - Pilih user yang ingin diubah
   - Klik Edit
   - Ubah email, password, **atau ROLE**
   - Klik Update
   - Database **LANGSUNG UPDATE** ✅

2. **User Logout & Login Ulang:**
   - Setelah admin update role
   - User harus logout dan login ulang
   - Session akan refresh dengan role baru
   - Sidebar menampilkan menu sesuai permission baru

### Flow Permission Check:

```
User Login → Auth → Session → Check Role → Get Permissions → Render Sidebar
                                    ↓
                            Database: users.role_id
                                    ↓
                            Database: permission_role
                                    ↓
                            Sidebar: Only show menu with view-* permission
```

---

## ⚠️ PENTING: Role vs Employee Role

Ada 2 konsep role dalam sistem ini:

### 1. **User Role** (`users.role_id`)
- Role untuk akses sistem/aplikasi
- Menentukan permission dan menu yang tampil
- **BISA diubah via Edit User** ✅
- Contoh: Driver, Gudang, Finance, HRD

### 2. **Employee Role** (`employees.role_id`)
- Role organisasi/jabatan karyawan
- Data HR/kepegawaian
- Hanya bisa diubah via Edit Employee
- Contoh: Foreman, Group Leader, Staff

**User Role ≠ Employee Role**

Contoh:
- Budi Prasetyo → Employee Role: Foreman (jabatan di perusahaan)
- Budi Prasetyo → User Role: Driver (akses di sistem administrasi)

---

## 🚀 KEUNTUNGAN SETELAH PERBAIKAN

### ✅ Sebelum Perbaikan:
- ❌ Admin ubah role di UI → Tidak tersimpan
- ❌ Harus manual edit database atau pakai script
- ❌ User bingung kenapa role tidak berubah
- ❌ Menu sidebar salah/tidak sesuai

### ✅ Setelah Perbaikan:
- ✅ Admin ubah role di UI → **LANGSUNG TERSIMPAN**
- ✅ Tidak perlu edit manual database
- ✅ Role terlihat jelas di form edit
- ✅ Menu sidebar otomatis sesuai permission role baru

---

## 📊 CONTOH SKENARIO

### Skenario 1: Promosi Jabatan
**Situasi:** Budi promosi dari Foreman menjadi Group Leader

**Yang Perlu Dilakukan:**
1. Edit Employee Budi → Ubah position & employee role
2. Edit User Budi → Pilih role "Group Leader"
3. Budi logout & login ulang
4. Sidebar menampilkan menu sesuai permission Group Leader

### Skenario 2: Tugas Tambahan
**Situasi:** Ahmad (GL) ditugaskan handle Finance sementara

**Yang Perlu Dilakukan:**
1. Edit User Ahmad → Pilih role "Finance"
2. Ahmad logout & login ulang
3. Ahmad sekarang bisa akses menu Finance (E-Money, Business Trip approval, dll)
4. Employee data Ahmad tetap sebagai Group Leader

### Skenario 3: Perubahan Akses
**Situasi:** Driver sekarang perlu akses Consumable juga

**Opsi 1 - Ubah Role Driver:**
1. Edit Role "Driver" → Tambah permission `view-consumables`
2. Semua user dengan role Driver otomatis bisa akses Consumable

**Opsi 2 - Buat Role Baru:**
1. Buat role baru "Driver Plus"
2. Kasih permission business-trips + consumables
3. Edit User driver tertentu → Pilih role "Driver Plus"

---

## 💡 TIPS

1. **Selalu clear cache setelah ubah permission atau role**
   ```bash
   php artisan optimize:clear
   ```

2. **Gunakan Incognito Mode saat test**
   - Hindari konflik dengan session lama

3. **Verifikasi dengan script checking**
   ```bash
   php check-user.php [email]
   ```

4. **Dokumentasikan role assignment**
   - Catat siapa dapat role apa dan kenapa

5. **Review permission secara berkala**
   - Pastikan user hanya punya akses yang diperlukan

---

## 🔍 TROUBLESHOOTING

### Masalah: Role berubah di database tapi menu tidak berubah

**Penyebab:** Session cache

**Solusi:**
1. User harus logout
2. Clear browser cache atau buka Incognito
3. Login ulang
4. Sidebar akan update

### Masalah: Form edit tidak ada dropdown role

**Penyebab:** View cache belum clear

**Solusi:**
```bash
php artisan view:clear
php artisan optimize:clear
```

### Masalah: Error "role_id is required"

**Penyebab:** Form submit tanpa pilih role

**Solusi:**
- Pastikan role dipilih di dropdown
- Jika error persist, cek validasi di UpdateUserRequest

### Masalah: "Role tidak valid" saat submit

**Penyebab:** ID role salah atau role sudah dihapus

**Solusi:**
- Cek apakah role masih exist di database
- Refresh halaman edit untuk update dropdown

---

## ✅ KESIMPULAN

**Masalah:** Database tidak update saat ubah role via UI

**Root Cause:** Form, validation, dan controller tidak handle role_id

**Perbaikan:** Tambahkan field role di form + update validation & controller

**Hasil:** Sekarang admin bisa ubah role user langsung via UI tanpa edit database manual! 🎉

---

**Sekarang sistem sudah bekerja dengan benar!** 

Test sekarang dengan:
1. Edit user via UI
2. Pilih role baru
3. Save
4. Verifikasi: `php check-user.php [email]`
5. User logout & login ulang
6. Sidebar menampilkan menu sesuai permission ✅
