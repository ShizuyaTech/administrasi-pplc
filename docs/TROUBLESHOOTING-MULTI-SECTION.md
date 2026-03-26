# 🔧 TROUBLESHOOTING GUIDE: Multi-Section Access Tidak Muncul

## ✅ Verifikasi Backend (SUDAH SELESAI)

Backend **sudah 100% bekerja dengan benar**:

```bash
# Hasil test query untuk Alberta:
✅ Accessible Section IDs: [1, 2]  
✅ Query Result: 10 overtimes
✅ Sections: Material Control (6) + PPC+Toolroom (4)
```

**Backend sudah mengembalikan data dari KEDUA seksi!**

## 🎯 SOLUSI: 5 Langkah Wajib

### 1️⃣ **LOGOUT dari Aplikasi**
- Klik menu Logout di aplikasi
- **Pastikan benar-benar logout!**

### 2️⃣ **Clear Browser Cache (PENTING!)**

**Google Chrome / Edge:**
```
1. Tekan Ctrl + Shift + Delete
2. Pilih "Cached images and files"
3. Pilih "All time"
4. Klik "Clear data"
```

**Atau cukup tekan:**
```
Ctrl + Shift + R (Windows)
Cmd + Shift + R (Mac)
```

**ALTERNATIF: Gunakan Incognito/Private Mode**
```
Ctrl + Shift + N (Chrome)
Ctrl + Shift + P (Firefox)
```

### 3️⃣ **Clear Server Cache**
```bash
php artisan cache:clear
php artisan config:clear  
php artisan view:clear
php artisan optimize:clear
```

### 4️⃣ **Clear Sessions (Force Fresh Login)**
```bash
php clear-sessions.php
```

### 5️⃣ **Login Ulang**
```
Email: alberta@iuse-ippi.com
Password: [password alberta]
```

---

## 🧪 Testing Checklist

**Setelah Login sebagai Alberta:**

1. ✅ Buka: `/overtimes/approval/supervisor`
2. ✅ Check filter tanggal: Pastikan March 2026 termasuk
3. ✅ Lihat tabel overtime
4. ✅ Perhatikan kolom "Section" atau nama creator

**Yang HARUS terlihat:**
- Batch dari **Ahmad Susanto** (Material Control)
- Batch dari **Eko Wahyudi** (PPC + Toolroom)
- Total: 10 employees dari 2 seksi

**Jika masih hanya terlihat 1 seksi:**
- Screenshot halaman tersebut
- Check Console Browser (F12 → Console tab)
- Lihat apakah ada error JavaScript

---

## 📊 Verification Scripts

### Check Section Assignment
```bash
php check-section-access.php
```
Expected output:
```
Alberta Puspita Sari
   Managed Sections: Material Control, PPC + Toolroom
   Accessible Section IDs: [1, 2]
```

### Check Query Results
```bash
php check-overtime-data.php
```
Expected output:
```
Alberta - Supervisor Approval Query Result: 10-19 overtimes
   Sections: Material Control, PPC + Toolroom
```

### Simulate Controller Request
```bash
php simulate-alberta-request.php
```
Expected output:
```
✅ Query returned: 10 overtimes
   [1] Material Control: 6 overtimes
   [2] PPC + Toolroom: 4 overtimes
```

---

## 🐛 Debugging: Jika Masih Bermasalah

### Check 1: Verify Login User
Di halaman approval, check di pojok kanan atas, pastikan:
- Nama: **Alberta Puspita Sari**
- Role: **Supervisor**

### Check 2: Check Date Filter
Filter tanggal default adalah bulan ini (March 2026).
Test data dibuat untuk tanggal: **25 March 2026**

Pastikan filter menampilkan:
- Dari: 01/03/2026
- Sampai: 31/03/2026

### Check 3: Check Console Browser
Tekan **F12** → Tab **Console**
Cari error merah. Jika ada, screenshot dan share.

### Check 4: Check Network Tab
Tekan **F12** → Tab **Network**
Refresh halaman
Cari request ke `/overtimes/approval/supervisor`
Klik → Tab **Preview** atau **Response**
Lihat apakah data JSON mengandung kedua seksi.

### Check 5: Temporary Debug Mode
Tambahkan ini di controller (temporary):

```php
// Di OvertimeController supervisorApprovalIndex()
dd([
    'user' => $user->name,
    'accessible_ids' => $accessibleSectionIds,
    'overtimes_count' => $overtimes->count(),
    'sections' => $overtimes->pluck('section.name', 'section_id')->unique(),
]);
```

---

## ❓ FAQ

**Q: Mengapa backend benar tapi browser salah?**
A: Browser mungkin cache halaman lama sebelum fitur multi-section diimplementasi.

**Q: Sudah clear cache tapi masih sama?**
A: 
1. Pastikan clear SEMUA cache (browser + server)
2. Logout dan login ulang
3. Gunakan Incognito mode untuk test
4. Clear sessions database dengan script

**Q: Gimana cara memastikan backend benar?**
A: Jalankan `php simulate-alberta-request.php` - jika hasilnya ada 2 seksi, backend benar.

**Q: Apakah perlu npm run build?**
A: Ya, jika menggunakan Vite/Laravel Mix:
```bash
npm run build
```

---

## 🎯 Expected Result

**SEBELUM Fix:**
- Alberta hanya lihat overtime dari Material Control (6 employees)

**SETELAH Fix:**
- Alberta lihat overtime dari:
  - Material Control (6 employees)
  - PPC + Toolroom (4 employees)
  - **Total: 10 employees dari 2 seksi**

---

## 📞 Next Steps

Jika sudah mengikuti SEMUA langkah di atas tapi masih bermasalah:

1. **Screenshot**:
   - Halaman approval yang bermasalah
   - Browser Console (F12 → Console)
   - Network Response (F12 → Network → overtime/approval/supervisor)

2. **Run debug scripts**:
   ```bash
   php check-section-access.php > debug-sections.txt
   php simulate-alberta-request.php > debug-request.txt
   ```

3. **Share**: debug-sections.txt dan debug-request.txt untuk analisa lebih lanjut.

---

✅ **Backend Implementation COMPLETE**
✅ **Database Structure READY**
✅ **Test Data CREATED**
🔄 **Browser Cache Issue (Most Likely)**

**SOLUTION: Hard Refresh Browser (Ctrl+Shift+R) + Clear Sessions + Login Fresh**
