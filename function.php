<?php
header("Content-Type:application/json");

define("HARI_BULAN", 30);
define("HARI_TAHUN", 360);
define("BULAN_TAHUN", 12);

if (isset($_POST['jumlahKredit']) && 
    $_POST['jangkaWaktu'] &&
    $_POST['bungaPertahun'] &&
    $_POST['metode']) {
    
    $_metode = $_POST['metode'];
    $_jumlahKredit = $_POST['jumlahKredit'];
    $_jangkaWaktu = $_POST['jangkaWaktu'];
    $_bungaPertahun = $_POST['bungaPertahun'];

    switch ($_metode) {
        case 1:
            $hasil = metode_flat($_jumlahKredit, $_jangkaWaktu, $_bungaPertahun);
            response($hasil, $_metode);
            break;

        case 2:
            $hasil = metode_efektif($_jumlahKredit, $_jangkaWaktu, $_bungaPertahun);
            response($hasil, $_metode);
            break;

        default:
            response("Invalid request.");
            break;
    }
    

} else {

    response("Invalid request.");

}

function response($data, $metode=0){
    $res['data'] = $data;
    $res['metode'] = $metode;
    $json_response = json_encode($res);
    echo $json_response;
}

function metode_flat($jumlahPinjaman, $jangkaWaktu, $sukuBunga) {
    $data = [];
    $sukuBunga = ($sukuBunga / 100) * $jangkaWaktu;
    $pokok = $jumlahPinjaman / $jangkaWaktu;
    $bunga = $jumlahPinjaman * $sukuBunga / $jangkaWaktu;
    $sisaPinjaman = $jumlahPinjaman;
    $jumlahAngsuran = $pokok + $bunga;

    for($i = 0; $i < $jangkaWaktu; $i++) {
        $sisaPinjaman -= $pokok;
        array_push($data, [
            "no"                => $i + 1,
            "pokok"             => round($pokok),
            "bunga"             => round($bunga),
            "jumlahAngsuran"    => round($jumlahAngsuran),
            "sisaPinjaman"      => round($sisaPinjaman)
        ]);
    }
    return $data;
}

function metode_efektif($jumlahPinjaman, $jangkaWaktu, $sukuBunga) {
    $data = [];
    $sukuBunga = ($sukuBunga / 100)* 12;
    $sisaPinjaman = $jumlahPinjaman;
    $pokok = $jumlahPinjaman / $jangkaWaktu;
    
    for($i = 0; $i < $jangkaWaktu; $i++) {
        $bunga = $sisaPinjaman * $sukuBunga * (HARI_BULAN / HARI_TAHUN);
        $jumlahAngsuran = ( $pokok + $bunga );
        $sisaPinjaman -= $pokok;
        array_push($data, [
            "no"                => $i + 1,
            "pokok"             => round($pokok),
            "bunga"             => round($bunga),
            "jumlahAngsuran"    => round($jumlahAngsuran),
            "sisaPinjaman"      => round($sisaPinjaman)
        ]);
    }
    return $data;
}

?>
