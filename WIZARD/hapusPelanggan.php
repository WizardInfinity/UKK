<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "ukk");

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedInUserID'])) {
    // Redirect ke halaman login jika belum login
    header("Location: login.php");
    exit();
}

function hapus($pelangganID)
{
    global $connect;
    mysqli_query($connect, "DELETE FROM pelanggan WHERE pelangganID = $pelangganID ");
    return mysqli_affected_rows($connect);
}

// Ambil pelangganID dari URL
$pelangganID = $_GET["pelangganID"];

// Panggil fungsi hapus
if (hapus($pelangganID) > 0) {
    echo "
        <script>
            alert('Data berhasil dihapus!');
            document.location.href = 'dataPelanggan.php';
        </script>
    ";
} else {
    echo "
        <script>
            alert('Data gagal dihapus!');
            document.location.href = 'dataPelanggan.php';
        </script>
    ";
}
?>