# 📘 PANDUAN LENGKAP: Membuat Role Baru di Aplikasi

## 🎯 Tujuan
Panduan ini menjelaskan cara membuat role baru dengan permission yang tepat sehingga menu sidebar tampil sesuai keinginan.

---

## ✅ LANGKAH 1: Login sebagai Super Admin

Hanya Super Admin yang bisa membuat role baru.

```
Email: admin@iuse-ippi.com
Password: password
```

---

## ✅ LANGKAH 2: Buka Halaman Manajemen Role

1. Klik menu **"Settings"** di sidebar
2. Klik **"Manage User"**
3. Klik **"Manajemen Role"**
4. URL: `http://localhost:8000/roles`

---

## ✅ LANGKAH 3: Klik "Tambah Role Baru"

Isi form dengan data role baru:

### Contoh 1: Role "Gudang"
Gudang hanya perlu akses Data Consumable dan Stock Movement.

**Form:**
- **Nama Role:** Gudang
- **Deskripsi:** Mengelola stok barang consumable

**Permission yang WAJIB dicentang:**
- ✅ `view-consumables` ← **WAJIB** (agar menu tampil)
- ✅ `create-consumable`
- ✅ `edit-consumable`
- ✅ `view-stock-movements`
- ✅ `create-stock-movement`

**Hasil:** Menu "Data Consumable" akan tampil, menu lain tidak.

---

### Contoh 2: Role "HR"
HR perlu akses Data Karyawan, Absensi, dan Overtime.

**Form:**
- **Nama Role:** HR
- **Deskripsi:** Mengelola data karyawan dan absensi

**Permission yang WAJIB dicentang:**
- ✅ `view-dashboard` ← **WAJIB** jika ingin tampilkan Dashboard
- ✅ `view-employees` ← **WAJIB** (agar menu Data Karyawan tampil)
- ✅ `create-employee`
- ✅ `edit-employee`
- ✅ `view-absences` ← **WAJIB** (agar menu Absensi tampil)
- ✅ `create-absence`
- ✅ `edit-absence`
- ✅ `view-overtimes` ← **WAJIB** (agar menu Overtime tampil)
- ✅ `approve-overtime`

**Hasil:** Menu Dashboard, Data Karyawan, Absensi, dan Overtime akan tampil.

---

### Contoh 3: Role "Finance"
Finance perlu akses Kartu E-Money dan Data Consumable.

**Form:**
- **Nama Role:** Finance
- **Deskripsi:** Mengelola kartu e-money dan pengeluaran

**Permission yang WAJIB dicentang:**
- ✅ `view-card` ← **WAJIB** (agar menu Kartu E-Money tampil)
- ✅ `create-card`
- ✅ `edit-card`
- ✅ `view-consumables` ← **WAJIB** (agar menu Data Consumable tampil)
- ✅ `view-stock-movements`

**Hasil:** Menu Kartu E-Money dan Data Consumable akan tampil.

---

## ✅ LANGKAH 4: Simpan Role

Klik tombol **"Simpan"** atau **"Create Role"**.

---

## ✅ LANGKAH 5: Buat User dengan Role Baru

1. Buka **"Manajemen User"**
2. Klik **"Tambah User Baru"**
3. Isi form:
   - **Name:** Nama user
   - **Email:** email@company.com
   - **Password:** password123
   - **Role:** Pilih role yang baru dibuat (misal: "Gudang")
   - **Section:** Pilih section yang sesuai
4. Klik **"Simpan"**

---

## ✅ LANGKAH 6: Test Login dengan User Baru

1. **LOGOUT** dari Super Admin
2. **CLEAR BROWSER CACHE** atau buka **Incognito Mode**
3. Login dengan kredensial user baru
4. Periksa sidebar - hanya menu yang diberi permission `view-*` yang tampil

---

## ⚠️ KESALAHAN UMUM & SOLUSI

### ❌ Masalah 1: Menu tidak tampil padahal sudah kasih permission create/edit

**Penyebab:** Tidak centang permission `view-*`

**Solusi:** 
- Centang permission `view-[nama-menu]` dulu
- Contoh: Untuk tampilkan menu Absensi, centang `view-absences`

---

### ❌ Masalah 2: Semua menu tampil padahal cuma kasih 3 permission

**Penyebab:** Login sebagai Super Admin, bukan user role baru

**Solusi:**
- Logout dari Super Admin
- Login dengan user role baru yang sudah dibuat

---

### ❌ Masalah 3: Menu lama masih tampil setelah ubah permission

**Penyebab:** Browser cache belum dibersihkan

**Solusi:**
```bash
# 1. Clear cache aplikasi
php artisan optimize:clear

# 2. Clear browser cache atau gunakan Incognito Mode
```

---

### ❌ Masalah 4: User bisa lihat menu tapi error saat akses halaman

**Penyebab:** Permission incomplete (punya `view-*` tapi tidak ada permission aksi)

**Solusi:**
- Tambahkan permission `create-*`, `edit-*`, atau `delete-*` sesuai kebutuhan
- Jangan kasih `view-*` saja tanpa permission CRUD jika user perlu aksi

---

## 📋 DAFTAR PERMISSION `view-*` UNTUK MENU

| Menu di Sidebar | Permission yang WAJIB |
|-----------------|----------------------|
| Dashboard | `view-dashboard` |
| Absensi | `view-absences` |
| Overtime | `view-overtimes` |
| Surat Perjalanan Dinas | `view-business-trips` |
| Data Karyawan | `view-employees` |
| Data Consumable | `view-consumables` |
| Jam Istirahat | `view-break-times` |
| Kartu E-Money | `view-card` |
| Manajemen User | `view-users` |
| Manajemen Role | `view-roles` |
| Manajemen Permission | `view-permissions` |

**INGAT:** Tanpa permission `view-*`, menu TIDAK AKAN TAMPIL di sidebar!

---

## 🔧 TOOLS UNTUK CEK PERMISSION

### 1. Cek detail user spesifik:
```bash
php check-user.php <email>
```

**Contoh:**
```bash
php check-user.php sunan.jaya@iuse-ippi.com
```

**Output:** Menampilkan semua permission user dan menu yang akan tampil.

---

### 2. Verifikasi permission role:
```bash
php verify-permissions.php
```

**Output:** Verifikasi apakah permission sudah sesuai ekspektasi.

---

### 3. List semua role dan permission:
```bash
php list-all-roles.php
```

**Output:** Daftar lengkap semua role dan permission masing-masing.

---

## 📝 CHECKLIST SEBELUM MEMBUAT ROLE BARU

- [ ] Sudah login sebagai Super Admin
- [ ] Sudah tentukan fitur apa saja yang role ini butuhkan
- [ ] Sudah tahu permission `view-*` mana yang perlu dicentang
- [ ] Sudah centang permission `view-*` untuk SETIAP menu yang ingin tampil
- [ ] Sudah centang permission aksi (create/edit/delete) sesuai kebutuhan
- [ ] Sudah simpan role
- [ ] Sudah buat user dengan role baru
- [ ] Sudah logout dari Super Admin
- [ ] Sudah clear browser cache atau gunakan Incognito
- [ ] Sudah test login dengan user baru
- [ ] Sidebar hanya menampilkan menu yang diberi permission

---

## 💡 TIPS PRO

1. **Buat role dari yang paling sederhana dulu**
   - Contoh: Driver hanya 3 permission, mudah di-test

2. **Test satu role, baru buat role berikutnya**
   - Jangan buat banyak role sekaligus tanpa test

3. **Gunakan Incognito Mode untuk test**
   - Hindari cache browser yang bikin bingung

4. **Catat permission yang digunakan**
   - Buat dokumentasi role dan permission-nya

5. **Gunakan naming convention yang jelas**
   - Role: "Gudang", "HR", "Finance", "Driver", dll
   - Jangan: "User1", "Test", "Coba", dll

---

## ❓ FAQ

**Q: Kenapa menu E-Money tampil padahal saya tidak kasih permission?**
A: Kemungkinan Anda login dengan user yang role-nya SUDAH punya permission `view-card`. Cek dengan `php check-user.php <email>`.

**Q: Bagaimana cara ubah permission role yang sudah ada?**
A: Login sebagai Super Admin → Manajemen Role → Edit Role → Centang/uncentang permission → Save → Logout → Clear cache → Login ulang dengan user role tersebut.

**Q: Apakah Super Admin bisa lihat semua menu?**
A: Ya, Super Admin punya 52 permission dan bisa akses SEMUA menu.

**Q: Bagaimana cara hapus permission dari role?**
A: Login sebagai Super Admin → Manajemen Role → Edit Role → Uncentang permission yang mau dihapus → Save → User dengan role tersebut perlu logout dan login ulang.

**Q: Kenapa setelah ubah permission, menu masih tampil?**
A: Clear browser cache atau gunakan Incognito Mode. Atau jalankan `php artisan optimize:clear`.

---

## 📞 TROUBLESHOOTING

Jika masih ada masalah setelah ikuti panduan ini:

1. **Jalankan command diagnostic:**
   ```bash
   php check-user.php <email-user-anda>
   ```

2. **Lihat output:** Apakah permission sudah sesuai yang Anda setting?

3. **Jika permission SALAH:** Edit role di UI, centang/uncentang yang benar, save.

4. **Jika permission BENAR tapi menu tidak sesuai:** 
   - Logout
   - Clear cache: `php artisan optimize:clear`
   - Clear browser cache (Ctrl+Shift+Del)
   - Login ulang

5. **Masih error?** Kemungkinan ada bug di kode. Cek console browser (F12) untuk error message.

---

**🎯 KESIMPULAN:**

Sistem permission sudah bekerja dengan benar. Kunci utamanya:
1. **Centang permission `view-*`** untuk tampilkan menu
2. **Logout dan clear cache** setelah ubah permission
3. **Test dengan user yang benar** (bukan Super Admin)
4. **Gunakan Incognito Mode** untuk menghindari cache

Selamat mencoba! 🚀
