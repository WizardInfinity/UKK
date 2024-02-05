<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "ukk");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedInUserID'])) {
    // Redirect ke halaman login jika belum login
    header("Location: login.php");
    exit();
}

function hapus($produkID)
{
    global $connect;
    mysqli_query($connect, "DELETE FROM produk WHERE produkID = $produkID ");
    return mysqli_affected_rows($connect);
}

// Ambil produkID dari URL
$produkID = $_GET["produkID"];

// Panggil fungsi hapus
if (hapus($produkID) > 0) {
    echo "
        <script>
            alert('Data berhasil dihapus!');
            document.location.href = 'dataProduk.php';
        </script>
    ";
} else {
    echo "
        <script>
            alert('Data gagal dihapus!');
            document.location.href = 'dataProduk.php';
        </script>
    ";
}
?>
