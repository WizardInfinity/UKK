<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "ukk");

function login($data)
{
    global $connect;

    $email = htmlspecialchars($data["email"]);
    $password = htmlspecialchars($data["hashPassword"]);

    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $user = mysqli_fetch_assoc($result);

        // Memeriksa apakah password yang dimasukkan cocok dengan hash di database
        if ($user && password_verify($password, $user['hashPassword'])) {
            return $user; // Mengembalikan data user setelah login berhasil
        }
    }

    return null; // Mengembalikan null jika login gagal
}


// Cek apakah tombol login sudah ditekan
if (isset($_POST["submit"])) {
    // Panggil fungsi login
    $loggedInUser = login($_POST);
    if ($loggedInUser) {
        $_SESSION['loggedInUserID'] = $loggedInUser['userID'];
        $_SESSION['loggedInUserName'] = $loggedInUser['username'];
        echo "
            <script>
                alert('Login Berhasil!');
                document.location.href = 'admin.php'; // Ganti dengan halaman setelah login
            </script>
        ";
        exit(); // Penting untuk menghentikan eksekusi script setelah redirect
    } else {
        echo "
            <script>
                alert('Login Gagal! Periksa kembali email dan password.');
            </script>
        ";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="login_style.css">
</head>

<body>
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-wrap p-4 p-md-5">
                        <div class="icon d-flex align-items-center justify-content-center">
                            <span class="fa fa-user-o"></span>
                        </div>
                        <h3 class="text-center mb-4">LOGIN</h3>
                        <form action="" class="login-form" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control rounded-left" placeholder="Email" name="email"
                                    required>
                            </div>
                            <div class="form-group d-flex">
                                <input type="password" class="form-control rounded-left"
                                    placeholder="Password (minimal 5 karakter)" name="hashPassword" required
                                    pattern=".{5,}">
                            </div>
                            <div class="form-group">
                                <button type="submit" name="submit"
                                    class="btn btn-primary rounded submit p-3 px-5">SUBMIT</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script defer
        src="https://static.cloudflareinsights.com/beacon.min.js/v84a3a4012de94ce1a686ba8c167c359c1696973893317"
        integrity="sha512-euoFGowhlaLqXsPWQ48qSkBSCFs3DPRyiwVu3FjR96cMPx+Fr+gpWRhIafcHwqwCqWS42RZhIudOvEI+Ckf6MA=="
        data-cf-beacon='{"rayId":"85003d3418de85d7","version":"2024.2.0","token":"cd0b4b3a733644fc843ef0b185f98241"}'
        crossorigin="anonymous"></script>
</body>

</html>