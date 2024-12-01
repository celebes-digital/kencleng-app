<?php

namespace App\Libraries;

class TemplatePesan
{
    // nama, email, kelamin
    public function setelahRegistrasi(array $data): string
    {
        $passwrod = env('DEFAULT_PASSWORD', 'kencleng123');
        $predikat = $data['kelamin'] === 'L' ? 'a' : 'i';

        return 
"Assalamu'alaikum, *{$data['nama']}*. 

Terima kasih telah melakukan registrasi sebagai Donatur Kencleng Jariyah

Berikut data login Anda:
*Username: {$data['email']}*
*Password: {$passwrod}*

Kami doakan semoga infaqnya setiap bulan menjadi pahala jariyah yang tak terputus dan menjadi tiket syurga Firdaus Anda kelak di akhirat, Insya Allah. 

Syukron wajaka{$predikat}llahu khairan katsiiran🙏🏻
";
    }

    // nama, no_kencleng, tgl_batas
    public function reminder7HariSebelumKoleksi(array $data): string
    {
        $predikat   = $data['kelamin'] === 'L' ? 'bapak' : 'ibu';
        $imbuhan    = $data['kelamin'] === 'L' ? 'Pak' : 'Bu';
        $inisial    = $data['kelamin'] === 'L' ? 'a' : 'i';

        return
"Assalamu'alaikum warahmatullahi wabarakatuh {$data['nama']}, 

Bagaimana kabarnya {$imbuhan}? 

Ijin mengingatkan bahwa, kencleng Bapak dengan ID *{$data['no_kencleng']}* akan dikumpulkan *10 hari lagi*, pada *{$data['tgl_batas']}*.

Kami doakan semoga {$predikat} selalu dalam keadaan sehat walafiat, serta diberi kelancaran dalam seluruh urusannya. 

Syukron wa jazaaka{$inisial}llahu khairan katsiran

Assalamu'alaikum warahmatullahi wabarakatuh";
    }

    // nama, no_kencleng, tgl_batas
    public function reminder3HariSebelumKoleksi(array $data): string
    {
        $predikat   = $data['kelamin'] === 'L' ? 'bapak' : 'ibu';
        $imbuhan    = $data['kelamin'] === 'L' ? 'Pak' : 'Bu';
        $inisial    = $data['kelamin'] === 'L' ? 'a' : 'i';
        return
"Assalamu'alaikum Warahmatullahi Wabarakatuh {$data['nama']}, 

Bagaimana kabarnya {$imbuhan}? 

Ijin mengingatkan bahwa, kencleng {$predikat} dengan ID *{$data['no_kencleng']}* akan dikumpulkan *3 hari lagi*, pada *{$data['tgl_batas']}*

Kami doakan semoga {$predikat} selalu dalam keadaan sehat walafiat, serta diberi kelancaran dalam seluruh urusannya. 

Syukron wa jazaaka{$inisial}llahu khairan katsiran

Assalamu'alaikum warahmatullahi wabarakatuh. 

Mohon dipilih opsi pengumpulan dengan memilih salah satu opsi berikut

1. Dikumpul oleh kolektor Aqtif
2. Diantar langsung ke Aqtif
3. Ditransfer
";
    }

    // public function setelahRegistrasiKe(array $data): string
    // {
    //     return "Halo $nama, terima kasih telah mendaftar di website kami. Silahkan login untuk melanjutkan. Pesan ke-$pesanKe";
    // }

    // kelamin, nama, jumlah
    public function konfirmasiKoleksiKeDonatur(array $data): string
    {
        $predikat   = $data['kelamin'] === 'L' ? 'bapak' : 'ibu';
        $imbuhan    = $data['kelamin'] === 'L' ? 'Pak' : 'Bu';
        $inisial    = $data['kelamin'] === 'L' ? 'a' : 'i';
        return
"Assalamu'alaikum {$data['nama']},

Alhamdulillah, hasil isi kencleng {$predikat} setelah dihitung oleh kolektor Aqtif adalah *Rp. {$data['jumlah']}*.

Kami akan kirimkan notifikasi penerimaan setelah team kolektor menyetorkan uang kencleng tersebut ke bagian keuangan. 

Syukron wa jazaaka{$inisial}llahu khairan katsiran.";
    }

    // kelamin, nama, jumlah
    public function konfirmasiDiterimaKeDonatur(array $data): string
    {
        $predikat   = $data['kelamin'] === 'L' ? 'bapak' : 'ibu';
        $imbuhan    = $data['kelamin'] === 'L' ? 'Pak' : 'Bu';
        $inisial    = $data['kelamin'] === 'L' ? 'a' : 'i';
        return
"Assalamu'alaikum {$data['nama']},

Alhamdulillah, isi kencleng {$predikat} setelah dihitung sebesar *Rp. {$data['jumlah']}*, telah kami terima dengan baik. 

Insya Allah, infaq Bapak akan kami alokasikan untuk membiayai program-program tahfizh, semoga setiap rupiahnya Allah balas dengan balasan 700kali lipat bahkan tak terhingga. 

Laporan alokasi dana akan kami laporkan setiap periode 1 bulan. Insya Allah. 

Syukron wa jazaaka{$inisial}llahu khairan katsiran.";
    }

    public function reminderTagLokasiKeDonatur(array $data): string
    {
        $predikat   = $data['kelamin'] === 'L' ? 'bapak' : 'ibu';
        $imbuhan    = $data['kelamin'] === 'L' ? 'Pak' : 'Bu';
        $inisial    = $data['kelamin'] === 'L' ? 'a' : 'i';
        return 
"Assalamu'alaikum {$data['nama']}, 

Semoga {$predikat} selalu dalam keadaan sehat dan berada dalam naungan rahmat Allah dalam segala aktifitasnya. 

Bersama pesan ini kami sertakan link, untuk mengaktifkan lokasi {$imbuhan} ya, agar terdata di sistem aplikasi kencleng jariyah dan untuk menjadi panduan bagi kolektor Aqtif pada saat pengumpulan. 

Berikut langkah-langkahnya
1. Klik link berikut 
https://linktaglokasi.kenclengjariyah.
2. Klik tombol longitude dan altitude \"KLIK DISINI\"
3. Setelah terisi angka, klik \"SIMPAN\"

Syukron wa jazaaka{$inisial}llahu khairan katsiran atas kerjasmaanya.";
    }
}
