# Panduan Permission System

## Daftar Permission yang Tersedia

### Dashboard
- `view-dashboard` - **WAJIB** untuk mengakses dashboard

### Absensi
- `view-absences` - **WAJIB** untuk lihat menu Absensi
- `create-absence` - Membuat data absensi
- `edit-absence` - Edit data absensi
- `delete-absence` - Hapus data absensi
- `export-absences` - Export data absensi

### Overtime
- `view-overtimes` - **WAJIB** untuk lihat menu Overtime
- `create-overtime` - Membuat data overtime
- `edit-overtime` - Edit data overtime
- `delete-overtime` - Hapus data overtime
- `approve-overtime` - Approve/reject overtime
- `export-overtimes` - Export data overtime

### Business Trips (Surat Perjalanan Dinas)
- `view-business-trips` - **WAJIB** untuk lihat menu Surat Perjalanan Dinas
- `create-business-trip` - Membuat surat perjalanan dinas
- `edit-business-trip` - Edit surat perjalanan dinas
- `delete-business-trip` - Hapus surat perjalanan dinas
- `approve-business-trip` - Approve surat perjalanan dinas
- `complete-business-trip` - Complete surat perjalanan dinas
- `print-business-trip` - Print surat perjalanan dinas
- `export-business-trips` - Export data business trips

### Master Data - Karyawan
- `view-employees` - **WAJIB** untuk lihat menu Data Karyawan
- `create-employee` - Membuat data karyawan
- `edit-employee` - Edit data karyawan
- `delete-employee` - Hapus data karyawan
- `export-employees` - Export data karyawan

### Master Data - Consumables
- `view-consumables` - **WAJIB** untuk lihat menu Data Consumable
- `create-consumable` - Membuat data consumable
- `edit-consumable` - Edit data consumable
- `delete-consumable` - Hapus data consumable
- `add-stock` - Menambah stok
- `reduce-stock` - Mengurangi stok
- `view-stock-movements` - Lihat pergerakan stok

### Master Data - Break Times
- `view-break-times` - **WAJIB** untuk lihat menu Jam Istirahat
- `create-break-time` - Membuat jam istirahat
- `edit-break-time` - Edit jam istirahat
- `delete-break-time` - Hapus jam istirahat

### Master Data - Kartu E-Money
- `view-card` - **WAJIB** untuk lihat menu Kartu E-Money
- `create-card` - Membuat kartu e-money
- `edit-card` - Edit kartu e-money
- `delete-card` - Hapus kartu e-money

### Settings - User Management
- `view-users` - **WAJIB** untuk lihat menu Manajemen User
- `create-user` - Membuat user baru
- `edit-user` - Edit user
- `delete-user` - Hapus user

### Settings - Role Management
- `view-roles` - **WAJIB** untuk lihat menu Manajemen Role
- `create-role` - Membuat role baru
- `edit-role` - Edit role
- `delete-role` - Hapus role

### Settings - Permission Management
- `view-permissions` - **WAJIB** untuk lihat menu Manajemen Permission
- `assign-permission` - Assign permission ke role

## PENTING: Cara Membuat Role Baru

Saat membuat role baru, **WAJIB** memberikan permission `view-*` untuk setiap menu yang ingin ditampilkan.

### Contoh Role Driver
Driver hanya perlu akses Surat Perjalanan Dinas:
```
✓ view-business-trips (WAJIB - untuk tampilkan menu)
✓ create-business-trip (Bisa buat surat)
✓ edit-business-trip (Bisa edit surat)
```

**Hasil:** Sidebar hanya menampilkan menu "Surat Perjalanan Dinas"

### Contoh Role Staff
Staff perlu akses Absensi, Overtime, dan lihat Dashboard:
```
✓ view-dashboard (WAJIB - untuk tampilkan dashboard)
✓ view-absences (WAJIB - untuk tampilkan menu)
✓ create-absence
✓ view-overtimes (WAJIB - untuk tampilkan menu)
✓ create-overtime
```

**Hasil:** Sidebar menampilkan Dashboard, Absensi, dan Overtime

## Cara Kerja Sidebar

Sidebar di `resources/views/components/sidebar.blade.php` menggunakan permission check:

```php
@if(auth()->user()->hasPermission('view-menu-name'))
    <a href="...">Menu Name</a>
@endif
```

**Tanpa permission `view-*`, menu TIDAK AKAN TAMPIL** di sidebar.

## Checklist Saat Membuat Role Baru

1. ✓ Tentukan fitur apa saja yang perlu diakses
2. ✓ Berikan permission `view-*` untuk SETIAP menu yang perlu tampil
3. ✓ Berikan permission additional (create/edit/delete) sesuai kebutuhan
4. ✓ Test login dengan user yang memiliki role tersebut
5. ✓ Verifikasi hanya menu yang diberi permission yang tampil

## Tips

- **Super Admin** sebaiknya mendapat SEMUA permission
- **Group Leader/Foreman** sebaiknya mendapat permission untuk section mereka
- **Staff** hanya permission operasional (view, create, edit)
- **Driver** hanya permission business trips
- Jangan lupa berikan permission `view-dashboard` jika user perlu lihat dashboard

## Troubleshooting

**Q: Menu masih tampil padahal tidak diberi permission?**
A: Pastikan di sidebar sudah ada `@if(auth()->user()->hasPermission('view-xxx'))`

**Q: User tidak bisa akses halaman padahal menu tampil?**
A: Periksa permission di Controller. Pastikan permission yang dicek konsisten dengan yang ada di role.

**Q: Cara menambah permission baru?**
A: 
1. Tambah di database (table permissions)
2. Tambah check di sidebar jika perlu menu
3. Tambah check di controller untuk authorization
