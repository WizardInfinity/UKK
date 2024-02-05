<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "ukk");

function isEmailUnique($email)
{
    global $connect;

    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($connect, $query);

    return mysqli_num_rows($result) === 0;
}

function register($data)
{
    global $connect;

    $username = htmlspecialchars($data["username"]);
    $email = htmlspecialchars($data["email"]);
    $hashPassword = htmlspecialchars($data["hashPassword"]);

    // Cek keunikan email
    if (!isEmailUnique($email)) {
        echo "
            <script>
                alert('Email sudah terdaftar!');
                document.location.href = 'register.php';
            </script>
        ";
        return 0;
    }

    $hashPassword = password_hash($hashPassword, PASSWORD_DEFAULT);

    $query = "INSERT INTO user (username, email, hashPassword) VALUES ('$username', '$email', '$hashPassword')";

    mysqli_query($connect, $query);

    return mysqli_affected_rows($connect);
}


// Cek apakah tombol register sudah ditekan atau belum
if (isset($_POST["submit"])) {
    if (register($_POST) > 0) {
        echo "
            <script>
                alert('Register Berhasil!');
                document.location.href = 'login.php';
            </script>
            ";
    } else {
        echo "
            <script>
                alert('Register Gagal!');
                document.location.href = 'register.php';
            </script>
            ";
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Register</title>
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
                            <span class="fa fa-user"></span>
                        </div>
                        <h3 class="text-center mb-4">REGISTER</h3>
                        <form action="" class="login-form" method="post">
                            <div class="form-group">
                                <input type="text" class="form-control rounded-left" placeholder="Username"
                                    name="username" required>
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control rounded-left" placeholder="Email"
                                    autocomplete="off" name="email" required>
                            </div>
                            <div class="form-group d-flex">
                                <input type="password" class="form-control rounded-left"
                                    placeholder="Password (min 5 karakter)" name="hashPassword" required
                                    pattern=".{5,}">
                            </div>
                            <div class="form-group d-flex">
                                <select class="form-control rounded-left" name="role" required>
                                    <option value="admin">Admin</option>
                                    <option value="petugas">Petugas</option>
                                </select>
                            </div>

                            <div class="form-group d-md-flex">
                                <div class="w-100 text-md-right">
                                    <a href="login.php">Sudah Memiliki Akun?</a>
                                </div>
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