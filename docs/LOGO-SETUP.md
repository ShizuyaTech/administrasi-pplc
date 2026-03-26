# Logo Setup Instructions / Instruksi Setup Logo

## How to Add Your IPAI Logo / Cara Menambahkan Logo IPAI Anda

### Step 1: Save the Logo / Langkah 1: Simpan Logo

Save your IPAI logo image file with the name `logo-ipai.png` to the following location:

Simpan file gambar logo IPAI Anda dengan nama `logo-ipai.png` ke lokasi berikut:

```
D:\Development\administrasi-app\public\images\logo-ipai.png
```

### Step 2: Supported Formats / Format yang Didukung

- PNG (Recommended / Direkomendasikan) - with transparent background
- JPG/JPEG
- SVG

### Step 3: Rebuild Assets / Rebuild Assets

After adding the logo, rebuild the frontend assets:

Setelah menambahkan logo, rebuild frontend assets:

```bash
npm run build
```

Or for development with hot reload:

Atau untuk development dengan hot reload:

```bash
npm run dev
```

### Step 4: Clear Cache / Clear Cache

Clear application cache to ensure logo appears:

Clear cache aplikasi untuk memastikan logo muncul:

```bash
php artisan optimize:clear
```

## Where the Logo Appears / Di Mana Logo Muncul

The IPAI logo will be displayed in the following locations:

Logo IPAI akan ditampilkan di lokasi berikut:

1. ✅ **Login Page** - Large logo above login form
   - Halaman Login - Logo besar di atas form login

2. ✅ **Sidebar** - Small logo in the sidebar header
   - Sidebar - Logo kecil di header sidebar

3. ✅ **Browser Tab** - As favicon
   - Tab Browser - Sebagai favicon

## Fallback Behavior / Perilaku Fallback

If the logo file is not found, the application will display a default icon instead.

Jika file logo tidak ditemukan, aplikasi akan menampilkan icon default.

## Recommended Logo Specifications / Spesifikasi Logo yang Direkomendasikan

- **Format**: PNG with transparent background
- **Minimum Resolution**: 200x200 pixels
- **Recommended Resolution**: 512x512 pixels
- **Aspect Ratio**: Square or keep original IPAI logo ratio
- **File Size**: < 500KB for optimal loading

---

## Updated Files / File yang Diupdate

The following files have been updated to use your IPAI logo:

File berikut telah diupdate untuk menggunakan logo IPAI Anda:

- `resources/views/auth/login.blade.php` - Login page
- `resources/views/layouts/app.blade.php` - Main layout with favicon
- `resources/views/components/sidebar.blade.php` - Sidebar logo

## Need Help? / Butuh Bantuan?

If the logo doesn't appear after following these steps:

Jika logo tidak muncul setelah mengikuti langkah-langkah ini:

1. Check the file name is exactly `logo-ipai.png`
2. Check the file location is `public/images/logo-ipai.png`
3. Clear browser cache (Ctrl + Shift + Delete)
4. Hard refresh the page (Ctrl + Shift + R)
5. Make sure you ran `npm run build`
6. Check browser console for any errors
