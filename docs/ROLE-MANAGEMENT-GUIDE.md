# PANDUAN: Cara Membuat & Mengelola Role Baru Yang Benar

## 🎯 MASALAH YANG SERING TERJADI

1. ❌ Role sudah dibuat tapi **menu tidak tampil** → Lupa beri permission `view-*`
2. ❌ Sudah ubah role user tapi **tidak berubah** → Tidak save/cache browser
3. ❌ Permission sudah dikasih tapi **tetap tidak bisa akses** → Cache tidak di-clear

---

## ✅ CARA MEMBUAT ROLE BARU YANG BENAR (via UI)

### Step 1: Login sebagai Super Admin
```
Email: admin@iuse-ippi.com
Password: password (atau password yang Anda set)
```

### Step 2: Buka Manajemen Role
1. Klik sidebar → **Settings** → **Manajemen Role**
2. Klik tombol **"+ Tambah Role"**

### Step 3: Isi Data Role
```
Nama Role: [Contoh: Gudang]
Deskripsi: [Contoh: Mengelola stok barang consumable]
```

### Step 4: ⚠️ PILIH PERMISSION DENGAN BENAR

**WAJIB** centang permission `view-*` untuk setiap menu yang ingin ditampilkan:

#### Contoh: Role "Gudang" yang perlu akses Consumable
```
☑ view-consumables      ← WAJIB (tampilkan menu)
☑ view-stock-movements  ← Bisa lihat history stok
☑ add-stock             ← Bisa tambah stok
☑ reduce-stock          ← Bisa kurangi stok
☐ create-consumable     ← Tidak perlu (tidak boleh buat item baru)
☐ delete-consumable     ← Tidak perlu (tidak boleh hapus item)
```

#### Contoh: Role "Finance" yang perlu E-Money dan Business Trip
```
☑ view-card                ← WAJIB (tampilkan menu E-Money)
☑ create-card
☑ edit-card
☑ view-business-trips      ← WAJIB (tampilkan menu Business Trip)
☑ approve-business-trip    ← Bisa approve
```

#### Contoh: Role "HRD" yang perlu Absensi, Overtime, Employee
```
☑ view-dashboard           ← WAJIB (tampilkan dashboard)
☑ view-absences            ← WAJIB (tampilkan menu Absensi)
☑ create-absence
☑ edit-absence
☑ view-overtimes           ← WAJIB (tampilkan menu Overtime)
☑ approve-overtime         ← Bisa approve
☑ view-employees           ← WAJIB (tampilkan menu Data Karyawan)
☑ create-employee
☑ edit-employee
```

### Step 5: Save Role
1. Klik tombol **"Simpan"**
2. **TUNGGU** sampai muncul notifikasi sukses
3. **JANGAN** langsung close browser sebelum ada konfirmasi

### Step 6: Verifikasi Permission
1. Kembali ke halaman Manajemen Role
2. Klik **"Edit"** pada role yang baru dibuat
3. **Cek ulang** apakah semua permission yang centang tadi masih tercentang
4. Jika hilang → Ada error saat save, ulangi Step 4-5

---

## ✅ CARA ASSIGN ROLE KE USER (via UI)

### Step 1: Buka Manajemen User
1. Klik sidebar → **Settings** → **Manajemen User**
2. Cari user yang ingin diubah

### Step 2: Edit User
1. Klik tombol **"Edit"** pada user tersebut
2. Pada dropdown **"Role"**, pilih role baru
3. Klik **"Simpan"**

### Step 3: ⚠️ WAJIB LOGOUT USER TERSEBUT
Jika user sedang login, role tidak akan berubah sampai logout!

1. Minta user untuk **Logout**
2. Atau paksa logout dengan: `php artisan cache:clear`

### Step 4: Test Login
1. User login ulang dengan email dan password mereka
2. **GUNAKAN INCOGNITO MODE** untuk test
3. Periksa sidebar - hanya menu sesuai permission yang tampil

---

## 🔧 COMMAND LINE TOOLS (Alternatif/Troubleshooting)

Jika cara via UI tidak bekerja, gunakan command line:

### Update Role User
```bash
php update-user-role.php [email] [role_name]

# Contoh:
php update-user-role.php sunan.jaya@iuse-ippi.com Driver
php update-user-role.php finance@iuse-ippi.com Finance
```

### Cek Permission User
```bash
php check-user.php [email]

# Contoh:
php check-user.php sunan.jaya@iuse-ippi.com
```

### Verifikasi Permission System
```bash
php verify-permissions.php
```

### Clear All Cache
```bash
php artisan optimize:clear
```

---

## 📋 CHECKLIST: Membuat Role Baru

Gunakan checklist ini setiap kali membuat role baru:

### Sebelum Buat Role:
- [ ] Tentukan menu apa saja yang perlu diakses
- [ ] Catat permission apa yang diperlukan
- [ ] Login sebagai Super Admin

### Saat Buat Role:
- [ ] Isi nama dan deskripsi role
- [ ] Centang permission `view-*` untuk SETIAP menu yang ingin tampil
- [ ] Centang permission aksi (create/edit/delete/approve) sesuai kebutuhan
- [ ] Klik Save dan tunggu notifikasi sukses

### Setelah Buat Role:
- [ ] Verifikasi: Edit role dan cek permission masih tercentang
- [ ] Test: Assign ke user test
- [ ] Test: Login dengan user test (Incognito Mode)
- [ ] Test: Periksa sidebar - hanya menu yang diberi permission tampil
- [ ] Clear cache: `php artisan optimize:clear`

### Jika Ada Masalah:
- [ ] Cek dengan: `php check-user.php [email]`
- [ ] Update manual: `php update-user-role.php [email] [role]`
- [ ] Clear browser cache atau gunakan Incognito
- [ ] Logout dan login ulang

---

## ⚠️ KESALAHAN YANG HARUS DIHINDARI

### ❌ SALAH 1: Hanya Kasih Permission Aksi
```
Role: Kasir
Permission yang dipilih:
☑ create-card
☑ edit-card
☐ view-card  ← LUPA INI!

Hasil: Menu E-Money TIDAK TAMPIL (karena tidak ada view-card)
```

**✅ BENAR:**
```
☑ view-card      ← WAJIB!
☑ create-card
☑ edit-card
```

### ❌ SALAH 2: Tidak Logout Setelah Ubah Role
```
1. Edit user Budi: role Foreman → Driver
2. Budi masih login
3. Budi refresh halaman
4. Role masih Foreman (cache session!)

Hasil: Role tidak berubah
```

**✅ BENAR:**
```
1. Edit user Budi: role Foreman → Driver
2. Minta Budi LOGOUT
3. Budi login ulang
4. Role sudah berubah
```

### ❌ SALAH 3: Test Tanpa Clear Cache
```
1. Buat role baru
2. Assign ke user
3. Login di browser yang sama (ada cache)
4. Menu tidak tampil

Hasil: Dikira permission salah, padahal cuma cache
```

**✅ BENAR:**
```
1. Buat role baru
2. Assign ke user  
3. Login di Incognito Mode (tanpa cache)
4. Menu tampil dengan benar
```

---

## 📊 CONTOH ROLE LENGKAP

### Role: "Supervisor Produksi"
**Kebutuhan:** Absensi, Overtime, Data Karyawan (view only)

```
Permission yang dipilih:
☑ view-dashboard
☑ view-absences
☑ create-absence
☑ edit-absence
☑ export-absences
☑ view-overtimes
☑ create-overtime
☑ approve-overtime
☑ export-overtimes
☑ view-employees      ← Hanya view, tidak bisa edit
☐ create-employee     ← Tidak perlu
☐ edit-employee       ← Tidak perlu
```

**Menu yang tampil:**
- ✅ Dashboard
- ✅ Absensi (bisa create, edit, export)
- ✅ Overtime (bisa create, approve, export)
- ✅ Data Karyawan (hanya view, tidak ada tombol tambah/edit)

---

### Role: "Admin Gudang"
**Kebutuhan:** Consumable dan Stock Movement

```
Permission yang dipilih:
☑ view-consumables
☑ create-consumable
☑ edit-consumable
☑ view-stock-movements
☑ add-stock
☑ reduce-stock
☐ delete-consumable   ← Tidak kasih (sudah final)
```

**Menu yang tampil:**
- ✅ Data Consumable (bisa create, edit, kelola stok)
- ❌ Menu lainnya tidak tampil

---

### Role: "Finance"
**Kebutuhan:** Business Trip dan E-Money

```
Permission yang dipilih:
☑ view-dashboard
☑ view-business-trips
☑ approve-business-trip
☑ complete-business-trip
☑ print-business-trip
☑ export-business-trips
☑ view-card
☑ create-card
☑ edit-card
```

**Menu yang tampil:**
- ✅ Dashboard
- ✅ Surat Perjalanan Dinas (bisa approve, complete, print, export)
- ✅ Kartu E-Money (bisa create, edit)

---

## 🚀 QUICK START: Buat Role Baru Sekarang

### 1. Login Super Admin
```
http://localhost:8000/login
Email: admin@iuse-ippi.com
Password: password
```

### 2. Buka Manajemen Role
```
Sidebar → Settings → Manajemen Role → + Tambah Role
```

### 3. Gunakan Template Permission

**Template "View Only" (Hanya Lihat):**
```
☑ view-dashboard
☑ view-[fitur]
```

**Template "Editor" (Lihat + Edit):**
```
☑ view-[fitur]
☑ create-[fitur]
☑ edit-[fitur]
```

**Template "Approver" (Lihat + Approve):**
```
☑ view-[fitur]
☑ approve-[fitur]
```

**Template "Full Access" (Semua):**
```
☑ view-[fitur]
☑ create-[fitur]
☑ edit-[fitur]
☑ delete-[fitur]
☑ approve-[fitur]
☑ export-[fitur]
```

### 4. Save & Test
```bash
# Clear cache
php artisan optimize:clear

# Test dengan user
php check-user.php [email]
```

---

## 💡 TIPS PRO

1. **Selalu gunakan Incognito Mode saat test** → Tidak ada cache yang mengganggu
2. **Buat role test dulu** → Jangan langsung assign ke user produksi
3. **Dokumentasikan permission setiap role** → Buat catatan di deskripsi
4. **Gunakan script verifikasi** → `php verify-permissions.php` setelah perubahan
5. **Backup sebelum ubah role penting** → Export dulu datanya

---

## 📞 TROUBLESHOOTING

| Masalah | Solusi |
|---------|--------|
| Menu tidak tampil setelah kasih permission | Cek permission `view-*` sudah dicentang |
| Role tidak berubah setelah edit | Logout dan login ulang |
| Error saat save role | Cek log: `storage/logs/laravel.log` |
| Permission hilang setelah save | Ada bug di controller, edit manual via database |
| User tidak bisa akses halaman | Cek permission di controller juga |

---

**Sekarang Anda sudah siap membuat role baru tanpa error berulang!** 🎉

Jika masih ada masalah, gunakan script verifikasi untuk debugging:
```bash
php check-user.php [email]
php verify-permissions.php
```
