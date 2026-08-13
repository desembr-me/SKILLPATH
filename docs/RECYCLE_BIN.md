# Recycle Bin Admin SKILLPATH

Fitur Recycle Bin menggunakan **Laravel Soft Deletes** untuk data yang dikelola admin.

## Data yang masuk Recycle Bin

- Course (`learning_paths`)
- Kategori (`categories`)
- Pengguna, termasuk orang tua dan pengajar (`users`)
- Review (`course_reviews`)

Pesanan tidak menggunakan Recycle Bin karena merupakan riwayat transaksi yang sebaiknya dipertahankan.

## Alur

```text
Admin menekan Hapus
        ↓
Kolom deleted_at diisi
        ↓
Data hilang dari daftar aktif
        ↓
Data tampil di /admin/recycle-bin
        ↓
Admin memilih:
├── Pulihkan
└── Hapus Permanen
```

## Keamanan

- Admin tidak dapat menghapus akun yang sedang digunakan.
- Minimal satu akun admin aktif harus tetap tersedia.
- Saat pengajar dipindahkan ke Recycle Bin, course miliknya otomatis menjadi draft.
- Saat course dipindahkan ke Recycle Bin, course otomatis menjadi draft.
- Course dengan riwayat transaksi atau enrollment tidak dapat dihapus permanen.
- Pengguna dengan riwayat course, transaksi, atau enrollment tidak dapat dihapus permanen.
- Pesanan tetap dapat menampilkan data pengguna yang berada di Recycle Bin.
- Wishlist, Course Saya, dan Live Class menyembunyikan course yang sedang berada di Recycle Bin.

## Route Admin

```text
GET     /admin/recycle-bin
PATCH   /admin/recycle-bin/restore-all
PATCH   /admin/recycle-bin/{type}/{id}/restore
DELETE  /admin/recycle-bin/{type}/{id}
```

Tipe yang didukung:

```text
course
category
user
review
```

## Migration

Jalankan:

```bash
php artisan migrate
```

Migration baru:

```text
database/migrations/2026_08_13_000016_add_soft_deletes_for_admin_recycle_bin.php
```

Jika masih tahap development dan data boleh dihapus:

```bash
php artisan migrate:fresh --seed
```

## Catatan Restore Course

Course yang dipindahkan ke Recycle Bin otomatis diubah menjadi draft. Setelah dipulihkan, course tetap draft. Admin perlu memeriksa course lalu menekan **Publish** jika ingin menampilkannya kembali di marketplace.
