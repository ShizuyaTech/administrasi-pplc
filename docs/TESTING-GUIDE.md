# CARA TEST PERMISSION SYSTEM - ROLE DRIVER

## ✅ HASIL VERIFIKASI SISTEM

Permission system sudah **BENAR**! Role Driver hanya bisa akses:
- ✅ Surat Perjalanan Dinas (view-business-trips)

Role Driver **TIDAK BISA** akses:
- ❌ Dashboard
- ❌ Absensi
- ❌ Overtime
- ❌ Data Karyawan
- ❌ Data Consumable
- ❌ Jam Istirahat
- ❌ **Kartu E-Money** ← Menu ini TIDAK tampil untuk Driver
- ❌ Manajemen User
- ❌ Manajemen Role
- ❌ Manajemen Permission

---

## 🔍 CARA TEST YANG BENAR

### Langkah 1: Pastikan Anda Logout Dulu
**PENTING:** Jika Anda sedang login dengan akun lain (Super Admin, Group Leader, dll), **LOGOUT DULU**!

1. Klik tombol Logout di sidebar
2. Atau buka: http://localhost:8000/logout

### Langkah 2: Clear Browser Cache
Ini SANGAT PENTING untuk menghindari data lama:

**Chrome/Edge:**
- Tekan `Ctrl + Shift + Delete`
- Pilih "Cached images and files"
- Klik "Clear data"

**Firefox:**
- Tekan `Ctrl + Shift + Delete`
- Pilih "Cache"
- Klik "Clear Now"

Atau lebih mudah: Buka browser dalam **Incognito/Private Mode**

### Langkah 3: Login dengan Akun Driver
Gunakan kredensial berikut:

```
Email: driver@iuse-ippi.com
Password: driver123
```

### Langkah 4: Periksa Sidebar
Setelah login, sidebar **HANYA** harus menampilkan:

```
┌─────────────────────────┐
│   PPLC IPPI             │
├─────────────────────────┤
│                         │
│ ✓ Surat Perjalanan     │
│   Dinas                 │
│                         │
│ (tidak ada menu lain)   │
│                         │
├─────────────────────────┤
│ 👤 Driver Test          │
│    Driver               │
│    [Logout]             │
└─────────────────────────┘
```

**Menu yang TIDAK tampil:**
- Dashboard
- Absensi
- Overtime
- Section "Master Data" (termasuk E-Money)
- Section "Settings"

---

## ❌ JIKA MASIH ADA MASALAH

### Masalah 1: Menu E-Money masih tampil
**Penyebab:**
- Anda masih login dengan akun Super Admin/Group Leader/Foreman
- Browser cache belum dibersihkan

**Solusi:**
1. Logout TOTAL dari aplikasi
2. Close semua tab browser
3. Buka Incognito/Private window
4. Login dengan driver@iuse-ippi.com
5. Periksa lagi

### Masalah 2: Tidak bisa login
**Solusi:**
Jalankan script ini lagi:
```bash
php create-driver-user.php
```

### Masalah 3: Menu lain masih tampil
**Penyebab:** Permission role Driver salah dikonfigurasi

**Solusi:**
1. Clear cache: `php artisan optimize:clear`
2. Verifikasi permission: `php verify-permissions.php`
3. Login sebagai Super Admin
4. Buka: Manajemen Role → Edit Role "Driver"
5. Pastikan HANYA 3 permission yang dicentang:
   - ✓ View Business Trips
   - ✓ Create Business Trip
   - ✓ Edit Business Trip
6. Save, logout, login ulang sebagai Driver

---

## 🔧 COMMAND BERGUNA

```bash
# Clear semua cache
php artisan optimize:clear

# Buat/reset user Driver
php create-driver-user.php

# Verifikasi permission system
php verify-permissions.php
```

---

## 📝 UNTUK MEMBUAT ROLE BARU DI MASA DEPAN

### Checklist Wajib:
1. ✓ Tentukan menu apa yang perlu ditampilkan
2. ✓ Berikan permission `view-*` untuk SETIAP menu
3. ✓ Berikan permission aksi (create/edit/delete) sesuai kebutuhan
4. ✓ JANGAN lupa clear cache setelah ubah permission
5. ✓ Test dengan user yang memiliki role tersebut
6. ✓ Test dalam Incognito mode

### Contoh: Role "Finance"
Jika Finance perlu akses Consumable dan Kartu E-Money:

```
✓ view-consumables (WAJIB - tampilkan menu Consumable)
✓ view-stock-movements
✓ view-card (WAJIB - tampilkan menu E-Money)
✓ create-card
✓ edit-card
```

**Hasil:** Sidebar tampil menu Data Consumable dan Kartu E-Money saja.

---

## ⚠️ KESALAHAN UMUM

### ❌ SALAH:
"Saya sudah kasih permission create-business-trip tapi menu tidak tampil"

**→ Menu tidak tampil karena tidak ada permission `view-business-trips`**

### ✅ BENAR:
"Saya kasih permission view-business-trips dulu, baru kasih create/edit"

**→ Menu tampil karena ada permission `view-*`, dan bisa create/edit karena ada permission-nya**

---

## 📞 TROUBLESHOOTING CEPAT

| Gejala | Penyebab | Solusi |
|--------|----------|--------|
| Menu E-Money tampil untuk Driver | Login sebagai user lain | Logout & login ulang |
| Menu lain tampil semua | Browser cache | Clear cache / Incognito |
| Tidak ada menu sama sekali | Permission belum disave | Check di Manajemen Role |
| Error saat akses halaman | Permission incomplete | Tambah permission yang diperlukan |

---

## ✅ KESIMPULAN

Sistem permission **SUDAH BENAR**. Yang perlu diperhatikan:

1. **WAJIB** logout dulu sebelum test role baru
2. **WAJIB** clear browser cache atau gunakan Incognito
3. **WAJIB** login dengan user yang benar (driver@iuse-ippi.com)
4. Periksa role dan permission di database, bukan dari tampilan menu saja

Jika sudah ikuti langkah-langkah di atas dan masih ada masalah, screenshot sidebar yang tampil dan user yang sedang login untuk analisa lebih lanjut.
