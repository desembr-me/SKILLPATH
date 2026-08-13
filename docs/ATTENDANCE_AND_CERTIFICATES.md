# Kehadiran dan Sertifikat

## Status booking

- `booked`: kursi sudah dipesan.
- `attended`: peserta hadir.
- `absent`: peserta tidak hadir.
- `cancelled`: booking dibatalkan.

Pengajar dapat memperbarui status dari dashboard jadwal. Saat status menjadi `attended`, `checked_in_at` dicatat.

## Status sesi

- `scheduled`: sesi masih terjadwal.
- `completed`: pelaksanaan selesai.
- `cancelled`: sesi dibatalkan.

## Kelayakan sertifikat

Sertifikat hanya dapat diterbitkan jika:

1. kelas mengaktifkan sertifikat,
2. anak mempunyai enrollment aktif,
3. kelas mempunyai setidaknya satu sesi yang tidak dibatalkan,
4. seluruh sesi wajib berstatus `completed`,
5. anak memiliki booking `attended` pada seluruh sesi wajib.

`final_score` pada sertifikat sekarang merepresentasikan persentase kehadiran, bukan skor aktivitas online.
