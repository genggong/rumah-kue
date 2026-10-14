# 📘 Panduan Penggunaan Aplikasi Rumah Kue

Aplikasi manajemen pesanan untuk rumah produksi **"Rumah Kue"** — alur **Pemesan → Admin → Produksi** dengan pengelolaan bahan baku, stok, harga, dan timeline pengerjaan sesuai hari & jam pemesanan.

---

## 🔑 Akun & Login

Buka aplikasi → halaman login. Gunakan akun sesuai peran:

| Role | Username | Password | Kegunaan |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Kelola semua: pesanan, bahan, produk, pengguna, laporan |
| **Produksi** | `produksi` | `produksi123` | Lihat antrian & tandai pesanan selesai |
| **Pemesan** | `pemesan` | `pemesan123` | Buat pesanan & pantau status |

> Akun otomatis dibuat saat aplikasi pertama dijalankan. Admin bisa tambah pengguna baru via menu **Pengguna**.

---

## 1️⃣ PEMESAN — Cara Memesan

### Langkah:
1. **Login** sebagai `pemesan` → klik menu **Buat Pesanan**.
2. Pilih **tanggal pengambilan** (minimal hari ini) & **jam pengambilan**.
3. Pilih **produk** (kue atau paket nasi) — bisa lebih dari satu item.
4. Atur **jumlah (qty)** tiap produk.
5. Isi **catatan** jika perlu (tulisan di kue, request tambahan, alamat antar, dll).
6. Klik **Kirim Pesanan**.

### Pantau status:
- Menu **Riwayat Saya** → lihat semua pesanan + statusnya.
- Klik **Detail** → lihat item, jadwal pengambilan, dan **timeline** (kapan dibuat, diverifikasi, masuk produksi, selesai).

> ⏰ Jangan lupa datang sesuai **hari & jam pengambilan** yang tertera.

---

## 2️⃣ ADMIN — Verifikasi & Kelola

### A. Kelola Pesanan (menu **Pesanan**)
1. **Daftar pesanan** — filter berdasarkan status (Baru / Verifikasi / Produksi / Selesai / Batal).
2. Klik **Detail** pada pesanan baru → lihat:
   - Item pesanan & total harga
   - **Kebutuhan bahan baku** (dihitung otomatis dari resep × qty)
   - **Cek stok**: CUKUP (hijau) / KURANG (merah) + nilai bahan
3. Aksi:
   - **✓ Terima & Kirim ke Produksi** — stok bahan **otomatis dikurangi**, mutasi tercatat, timeline diperbarui.
   - **Kirim ke Produksi (tanpa kurangi stok)** — jika bahan belum mau dipotong (mis. menunggu pembelian).
   - **Tolak** — isi alasan opsional, pesanan berstatus Batal.

### B. Bahan Baku (menu **Bahan Baku**)
- **+ Bahan Baru**: nama, satuan (kg/butir/pcs/liter), harga satuan, stok awal, min stok.
- **Stok +/−**: mutasi masuk (beli), keluar (produksi), atau koreksi (set stok langsung) — semua tercatat.
- **Edit**: ubah nama/satuan/harga/min stok.
- Dashboard menampilkan **peringatan bahan menipis** (stok ≤ min stok).

### C. Produk & Resep (menu **Produk & Resep**)
- **+ Produk Baru**: nama, kategori (KUE / NASI), harga jual.
- Klik **Resep & Bahan** pada produk → atur komposisi bahan per produk (nama bahan + qty).
- Otomatis tampil **estimasi biaya bahan** dan **margin** (harga jual − biaya bahan) — membantu menentukan harga jual yang sehat.

### D. Pengguna (menu **Pengguna**)
- Tambah pengguna baru: pemesan / produksi / admin, lengkap dengan no HP.

### E. Laporan (menu **Laporan**)
- Pilih rentang tanggal → lihat: jumlah pesanan, **total omzet**, penjualan per produk, per status.
- Berguna untuk rekap harian/mingguan/bulanan.

---

## 3️⃣ PRODUKSI — Pengerjaan

### Antrian (menu **Antrian Produksi**)
- Daftar pesanan yang sudah **direkomendasikan produksi** (status Produksi), diurutkan otomatis berdasarkan **hari & jam pengambilan** — paling awal dikerjakan duluan.
- **Timeline Pengerjaan** menampilkan urutan lengkap #1, #2, #3...
- Klik **Detail** untuk lihat item & kebutuhan bahan.

### Menandai selesai
- Setelah selesai dikerjakan → klik **✓ Selesai**.
- Status pesanan berubah menjadi **Selesai** & tercatat di timeline.
- Pemesan bisa melihatnya di **Riwayat Saya**.

---

## 4️⃣ ALUR LENGKAP (Contoh)

```
Pemesan buat pesanan: 1× Kue Bolu Coklat + 3× Paket Nasi Ayam
   Tanggal: 16 Okt 2026, jam 10:30
        ↓
Admin buka detail → lihat kebutuhan bahan:
   - Tepung 0.5 kg + Telur 3 butir (dari kue)
   - Beras 0.9 kg (dari 3 nasi)
   Stok CUKUP ✓ → klik "Terima & Kirim ke Produksi"
        ↓
Stok otomatis berkurang + mutasi tercatat + timeline "Diverifikasi"
        ↓
Tim Produksi lihat antrian (urut 16 Okt 10:30) → kerjakan → "✓ Selesai"
        ↓
Pemesan lihat status SELESAI di Riwayat Saya → ambil pesanan
```

---

## 📌 Tips Penggunaan

1. **Cek stok sebelum terima pesanan** — aplikasi sudah otomatis flag CUKUP/KURANG, tapi tetap pastikan bahan fisik tersedia.
2. **Update stok rutin** — set min stok yang wajar agar peringatan menipis muncul sebelum kehabisan.
3. **Resep harus lengkap** — pesanan tanpa resep tidak bisa dihitung kebutuhan bahannya (tampil "tidak ada resep").
4. **Laporan berkala** — rekap omzet per bulan buat evaluasi produk terlaris.

---

## 🛠️ Instalasi (Ringkas)

```bash
php -S 127.0.0.1:8898   # dari folder rumah-kue
```
- DB SQLite otomatis dibuat & akun seed otomatis muncul saat pertama kali dibuka.
- Detail lengkap: lihat `README.md`.

© 2026 Rumah Kue