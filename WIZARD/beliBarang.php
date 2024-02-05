<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "ukk");

if (isset($_GET['pelangganID'])) {
    $pelangganIDFromURL = $_GET['pelangganID'];
} else {
    // Jika tidak ada, kembalikan pengguna ke halaman dataPelanggan.php atau atur nilai default sesuai kebutuhan
    header("Location: dataPelanggan.php");
    exit();
}

// Cek apakah pengguna sudah login
if (!isset($_SESSION['loggedInUserID'])) {
    // Redirect ke halaman login jika belum login
    header("Location: login.php");
    exit();
}

function query($query)
{
    global $connect;
    $result = mysqli_query($connect, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Ambil data pengguna termasuk peran
$userData = query("SELECT * FROM user WHERE userID = " . $_SESSION['loggedInUserID']);

// Periksa apakah data pengguna diambil
if (count($userData) > 0) {
    $userRole = $userData[0]['role'];
} else {
    // Tetapkan peran default jika data pengguna tidak ditemukan (Anda dapat menyesuaikannya berdasarkan logika aplikasi Anda)
    $userRole = 'guest';
}

$produk = query("SELECT * FROM produk");

$loggedInUserName = $_SESSION['loggedInUserName'];

// Proses logout ketika tombol logout diklik
if (isset($_POST['logout'])) {
    session_unset(); // Menghapus semua variabel sesi
    session_destroy(); // Menghancurkan sesi
    header("Location: login.php"); // Mengarahkan kembali ke halaman login
    exit();
}

// Cek apakah user belum login, redirect ke halaman login.php
if (!isset($_SESSION['loggedInUserID'])) {
    header("Location: login.php");
    exit();
}

function tambah($data)
{
    global $connect;

    $tanggalPenjual = htmlspecialchars($data["tanggalPenjual"]);
    $subTotalPenjualan = htmlspecialchars($data["subTotal"]);
    $pelangganID = htmlspecialchars($data["pelangganID"]);

    $query = "INSERT INTO penjualan (tanggalPenjualan, totalHarga, pelangganID)
                VALUES ('$tanggalPenjual', '$subTotalPenjualan', '$pelangganID')";

    mysqli_query($connect, $query);

    $penjualanID = mysqli_insert_id($connect);
    $produkID = htmlspecialchars($data["produkID"]);
    $jumlahProduk = htmlspecialchars($data["jumlahProduk"]);
    $subTotalDetail = htmlspecialchars($data["subTotal"]);

    $query2 = "INSERT INTO detailpenjualan (penjualanID, produkID, jumlahProduk, subTotal)
                VALUES ('$penjualanID', '$produkID', '$jumlahProduk','$subTotalDetail')";

    mysqli_query($connect, $query2);

    // Perbarui stok di tabel produk
    $stokSaatIni = query("SELECT stok FROM produk WHERE produkID = $produkID");

    if ($stokSaatIni[0]['stok'] >= $jumlahProduk) {
        $stokBaru = $stokSaatIni[0]['stok'] - $jumlahProduk;

        // Perbarui stok di tabel produk
        mysqli_query($connect, "UPDATE produk SET stok = $stokBaru WHERE produkID = $produkID");
    } else {
        // Tangani skenario stok tidak mencukupi (Anda mungkin ingin menambahkan penanganan kesalahan yang sesuai)
        echo "
            <script>
                alert('Stok tidak mencukupi!');
                document.location.href = 'dataPelanggan.php';
            </script>
            ";
        exit();
    }

    return mysqli_affected_rows($connect);
}

//cek apakah tombol sudah di tekan atau belum
if (isset($_POST["submit"])) {

    if (tambah($_POST) > 0) {
        echo "
            <script>
                alert('data berhasil ditambahkan!');
                document.location.href = 'dataPelanggan.php';
            </script>
            ";
    } else {
        echo "
            <script>
                alert('data gagal ditambahkan!');
                document.location.href = 'dataPelanggan.php';
            </script>
            ";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="admin_style.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"
        crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="#">Welcome,
            <?php echo $loggedInUserName; ?>!
        </a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li>
                        <form method="post">
                            <button class="dropdown-item" type="submit" name="logout">Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Core</div>
                        <a class="nav-link" href="admin.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <div class="sb-sidenav-menu-heading">Interface</div>

                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne"
                            data-bs-parent="#sidenavAccordion">
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages"
                            aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Pages
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo"
                            data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                    data-bs-target="#pagesCollapseAuth" aria-expanded="false"
                                    aria-controls="pagesCollapseAuth">
                                    Data
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne"
                                    data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="dataUser.php">Data User</a>
                                        <a class="nav-link" href="dataProduk.php">Data Produk</a>
                                        <a class="nav-link" href="dataPelanggan.php">Data Pelanggan</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                                    data-bs-target="#pagesCollapseError" aria-expanded="false"
                                    aria-controls="pagesCollapseError">
                                    Form
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne"
                                    data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="tambahProduk.php">Tambah Produk</a>
                                        <a class="nav-link" href="tambahPelanggan.php">Tambah Pelanggan</a>
                                    </nav>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php echo $loggedInUserName; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Beli Barang</h1>
                    <a href="dataPelanggan.php" type="button" class="btn btn-success" data-toggle="modal"
                        data-target="#createModal">Kembali</a>

                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active"></li>
                    </ol>
                    <div class="row">
                        <form action="" method="post">
                            <div class="mb-3">
                                <input type="hidden" name="pelangganID" id="pelangganID"
                                    value="<?= $pelangganIDFromURL; ?>">
                                <label for="exampleInputProductName" class="form-label">Nama Produk</label>
                                <select class="form-select" id="exampleInputProductName" name="produkID"
                                    onchange="updateSubtotal()" required>
                                    <option selected disabled>Pilih Produk</option>
                                    <?php foreach ($produk as $row): ?>
                                        <option value="<?= $row['produkID']; ?>" data-harga="<?= $row['harga']; ?>">
                                            <?= $row['namaProduk']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputPrice" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="exampleInputPrice" name="tanggalPenjual"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputStock" class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="exampleInputStock" name="jumlahProduk"
                                    oninput="updateSubtotal()" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputHarga" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="exampleInputHarga" name="harga" readonly
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputSubTotal" class="form-label">Sub Total</label>
                                <input type="number" class="form-control" id="exampleInputSubTotal" name="subTotal"
                                    readonly required>
                            </div>
                            <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>

                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Habieb UKK Website 2024</div>
                        <div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
        function updateSubtotal() {
            // Dapatkan harga produk yang dipilih
            var selectedProductId = document.getElementById("exampleInputProductName").value;
            var selectedProduct = document.querySelector("#exampleInputProductName option[value='" + selectedProductId + "']");
            var productPrice = selectedProduct.getAttribute("data-harga");

            // Dapatkan kuantitas yang dimasukkan oleh pengguna
            var quantity = document.getElementById("exampleInputStock").value;

            // Hitung subtotal
            var subtotal = productPrice * quantity;

            // Perbarui input harga
            document.getElementById("exampleInputHarga").value = productPrice;

            // Perbarui input subtotal
            document.getElementById("exampleInputSubTotal").value = subtotal;
        }
    </script>
</body>

</html>