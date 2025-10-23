# Roadmap Proyek Terprioritas

Dihasilkan: 2025-10-22

TODO ini adalah roadmap terperinci dan terprioritas, mulai dari fitur operasional prioritas tinggi hingga integrasi. Setiap item mencakup Langkah-langkah, Kriteria penerimaan, dan Perkiraan upaya untuk membantu perencanaan dan implementasi.

---

## - [ ] 1. CRUD Item / Inventaris (Prioritas: Tinggi)
**Tujuan:** Manajemen inventaris penuh (daftar, buat, edit, tampilkan, hapus) dengan pencarian, paginasi, dan impor/ekspor. Pastikan `kode` unik dan `stok` akurat.

**Langkah-langkah:**
- [ ] Verifikasi/sesuaikan migrasi `items` (keunikan `kode`, `stok` unsigned).
- [ ] Buat halaman Volt dan komponen Livewire/Volt untuk daftar & form.
- [ ] Implementasikan pencarian sisi server, pengurutan, paginasi.
- [ ] Tambahkan validasi buat/edit dan penanganan batasan `kode` unik.
- [ ] Implementasikan impor CSV dan ekspor CSV/Excel (opsional gunakan maatwebsite/excel).
- [ ] Tambahkan unit test (alur sukses buat + cari) dan QA manual.

**Kriteria Penerimaan:**
- [ ] Pengguna dapat membuat, mengedit, menghapus item.
- [ ] Daftar mendukung pencarian dan paginasi.
- [ ] Keunikan `kode` ditegakkan (DB + validasi).

**Perkiraan upaya:** Medium

---

## - [ ] 2. Pergerakan Stok / Log Audit (Prioritas: Tinggi)
**Tujuan:** Merekam setiap perubahan stok untuk keterlacakan (traceability) dan pelaporan.

**Langkah-langkah:**
- [ ] Buat migrasi `stock_movements` (item_id, qty, movement_type, reference_table, reference_id, user_id, note, created_at).
- [ ] Buat model `StockMovement` dan relasi ke `Item`.
- [ ] Update `StockService` untuk menyisipkan catatan pergerakan saat menambah/mengurangi.
- [ ] Buat halaman Volt untuk melihat pergerakan per item dan pergerakan global terbaru.
- [ ] Tambahkan tes untuk memastikan pergerakan dicatat untuk alur GI dan DO.

**Kriteria Penerimaan:**
- [ ] Setiap `StockService::increase/decrease` membuat entri `stock_movements`.

**Perkiraan upaya:** Kecil–Medium

---

## - [ ] 3. Penerimaan Barang (Goods Inwards) (Prioritas: Tinggi)
**Tujuan:** Menerima barang ke inventaris dengan tanda terima multi-baris dan lampiran opsional; menambah stok dan mencatat pergerakan.

**Langkah-langkah:**
- [ ] Buat migrasi/model: `goods_inwards` + opsional `goods_inward_items`.
- [ ] Bangun halaman form Volt untuk membuat GI dengan baris dinamis (pilih item, jumlah, harga (opsional), catatan).
- [ ] Dukung lampiran file (simpan di `storage/app/public`).
- [ ] Saat simpan: buat GI + baris GI, panggil `StockService::increase()` per item dan buat entri pergerakan stok.
- [ ] Tambahkan validasi dan tes.

**Kriteria Penerimaan:**
- [ ] Membuat GI akan menambah `stok` item sejumlah qty yang ditentukan dan mencatat pergerakannya. Lampiran dapat diakses dari detail GI.

**Perkiraan upaya:** Medium–Besar

---

## - [ ] 4. Surat Jalan (Delivery Orders) (Prioritas: Tinggi)
**Tujuan:** Membuat dan mengelola DO yang mengurangi stok saat dikonfirmasi dan menghasilkan SJ (PDF) yang dapat dicetak.

**Langkah-langkah:**
- [ ] Buat migrasi/model `delivery_orders` dan `delivery_order_items` (jika belum ada).
- [ ] Bangun halaman CRUD Volt untuk DO: buat, edit (draft), konfirmasi (mengurangi stok), daftar, detail.
- [ ] Implementasikan alur konfirmasi: transaksi + `StockService::decrease()` untuk setiap item dengan row lock.
- [ ] Hasilkan SJ PDF yang bisa dicetak menggunakan dompdf atau snappy dan template Blade.
- [ ] Tes: konfirmasi mengurangi stok; tidak bisa konfirmasi jika stok tidak mencukupi.

**Kriteria Penerimaan:**
- [ ] Mengkonfirmasi DO secara atomik mengurangi stok dan mencatat pergerakan; SJ PDF dapat diunduh/dicetak.

**Perkiraan upaya:** Besar

---

## - [ ] 5. Dashboard (KPI & Tindakan Cepat) (Prioritas: Tinggi)
**Tujuan:** Dashboard tingkat tinggi yang menunjukkan kesehatan sistem dan tindakan cepat.

**Langkah-langkah:**
- [ ] Buat route GET /dashboard dan view Volt `resources/views/dashboard.blade.php`.
- [ ] Implementasikan widget Livewire/Volt kecil untuk: total item, total pelanggan, total nilai stok, jumlah stok menipis, DO tertunda, pergerakan stok terbaru.
- [ ] Tindakan cepat: tombol untuk membuat DO atau GI (tautan ke form).
- [ ] Tambahkan query yang di-cache untuk metrik berat (gunakan cache dengan TTL singkat).

**Kriteria Penerimaan:**
- [ ] Dashboard dimuat < 1 detik (tergantung DB) dan menunjukkan KPI yang akurat. Tindakan cepat mengarah ke form yang benar.

**Perkiraan upaya:** Kecil–Medium

---

## - [ ] 6. Peningkatan Pelanggan (Prioritas: Medium)
**Tujuan:** Menambah data pelanggan lebih dalam (beberapa alamat, narahubung) dan impor/ekspor.

**Langkah-langkah:**
- [ ] Tambah migrasi/model `customer_addresses` dan `customer_contacts`.
- [ ] Update UI Volt Pelanggan untuk mengelola alamat & kontak (inline atau via modal).
- [ ] Implementasikan impor/ekspor CSV untuk pelanggan dan alamat.

**Kriteria Penerimaan:**
- [ ] Pelanggan dapat memiliki banyak alamat & kontak; impor memetakan kolom dengan benar.

**Perkiraan upaya:** Medium

---

## - [ ] 7. CRUD Pemasok / Vendor (Prioritas: Medium)
**Tujuan:** Mengelola pemasok untuk alur Penerimaan Barang (GI) dan PO.

**Langkah-langkah:**
- [ ] Buat migrasi/model `suppliers` dan halaman CRUD Volt dasar.
- [ ] Izinkan memilih pemasok di form GI.

**Kriteria Penerimaan:**
- [ ] Pemasok dapat dikelola dan dipilih dalam alur GI.

**Perkiraan upaya:** Kecil

---

## - [ ] 8. Laporan & Ekspor (Prioritas: Medium)
**Tujuan:** Menyediakan laporan operasional dengan kemampuan ekspor CSV/PDF.

**Langkah-langkah:**
- [ ] Tentukan laporan yang dibutuhkan: valuasi stok, stok menipis, pengiriman per periode, barang diterima.
- [ ] Bangun halaman Volt dengan filter tanggal dan tombol ekspor (CSV/PDF).
- [ ] Implementasikan ekspor latar belakang (queued jobs) untuk dataset besar jika diperlukan.

**Kriteria Penerimaan:**
- [ ] Laporan mengembalikan data yang benar untuk rentang yang dipilih dan dapat mengekspor CSV/PDF.

**Perkiraan upaya:** Medium–Besar

---

## - [ ] 9. Pengguna, Peran & Izin (Prioritas: Medium)
**Tujuan:** Kontrol akses berbasis peran (RBAC) untuk menu dan tindakan.

**Langkah-langkah:**
- [ ] Instal dan konfigurasi Spatie Laravel-Permission (atau implementasikan solusi kustom).
- [ ] Buat peran (admin, manager, operator) dan izin yang dipetakan ke rute/tindakan.
- [ ] Tambahkan UI admin untuk mengelola peran/penugasan.

**Kriteria Penerimaan:**
- [ ] Pengguna menerima akses yang benar dan tindakan yang tidak sah diblokir.

**Perkiraan upaya:** Medium

---

## - [ ] 10. Pengaturan & Info Perusahaan (Prioritas: Rendah–Medium)
**Tujuan:** Pengaturan aplikasi terpusat (profil perusahaan, gudang default, template cetak).

**Langkah-langkah:**
- [ ] Buat tabel `settings` atau gunakan pendekatan berbasis konfigurasi dengan UI admin.
- [ ] Bangun halaman pengaturan Volt dan editor JSON sederhana untuk string template.

**Kriteria Penerimaan:**
- [ ] Admin dapat memperbarui info perusahaan yang digunakan di header dan template cetak.

**Perkiraan upaya:** Kecil

---

## - [ ] 11. Manajemen Aset (Prioritas: Rendah)
**Tujuan:** Melacak aset perusahaan (akuisisi, depresiasi, lokasi).

**Langkah-langkah:**
- [ ] Buat migrasi/model `assets`.
- [ ] Halaman CRUD Volt dan transfer opsional antar lokasi.

**Kriteria Penerimaan:**
- [ ] CRUD dasar siklus hidup aset diimplementasikan.

**Perkiraan upaya:** Medium

---

## - [ ] 12. Integrasi & API (Prioritas: Rendah)
**Tujuan:** Menyediakan endpoint API untuk integrasi eksternal (aplikasi seluler, webhook).

**Langkah-langkah:**
- [ ] Desain permukaan API minimal (item, pelanggan, endpoint pembuatan DO).
- [ ] Implementasikan otentikasi berbasis token (Laravel Sanctum) dan API resources.

**Kriteria Penerimaan:**
- [ ] Klien pihak ketiga dapat mendaftar item dan membuat DO melalui API yang terotentikasi.

**Perkiraan upaya:** Medium

---

### Cara melanjutkan
- Balas dengan satu item untuk memulai (misalnya, `Mulai: CRUD Item / Inventaris`).
- Saya akan menandai tugas yang dipilih di manajer todo sebagai `sedang dikerjakan`, membuat file yang diperlukan, dan menjalankan pemeriksaan cepat.
- Untuk perubahan database, saya akan menyiapkan migrasi tetapi tidak akan menjalankannya di mesin Anda kecuali Anda memintanya.

Jika Anda mau, saya juga bisa membuat Git branch dan PR untuk setiap tugas besar.

---

### Cara menggunakan
- Untuk memulai tugas, balas di sini (atau buat issue) dengan judul tugas.
- Saya dapat mengimplementasikan tugas apa pun secara end-to-end (scaffold rute/views/models/migrations) dan menjalankan pemeriksaan cepat.

Jika Anda ingin ini sebagai Git commit/branch dan PR, beri tahu saya dan saya akan membuatnya.