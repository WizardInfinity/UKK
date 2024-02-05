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
                document.location.href = 'login.php';
            </script>
        ";
        return 0;
    }

    $hashPassword = password_hash($hashPassword, PASSWORD_DEFAULT);

    $query = "INSERT INTO user (username, email, hashPassword) VALUES ('$username', '$email', '$hashPassword')";

    mysqli_query($connect, $query);

    return mysqli_affected_rows($connect);
}

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

// Cek apakah tombol register sudah ditekan atau belum
if (isset($_POST["input"])) {
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
                document.location.href = 'login.php';
            </script>
            ";
    }
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="loginregister_style.css">
</head>

<body>


    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-0 pb-3"><span>Log In </span><span>Register</span></h6>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
                        <label for="reg-log"></label>
                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">
                                <div class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <form action="" method="post">
                                                <h4 class="mb-4 pb-3">Log In</h4>
                                                <div class="form-group">
                                                    <input type="email" name="email" class="form-style"
                                                        placeholder=" Email" id="email" autocomplete="off" required
                                                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" name="hashPassword" class="form-style"
                                                        placeholder=" Password (minimal 5 karakter)" id="hashPassword"
                                                        autocomplete="off" required pattern=".{5,}">
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="submit" name="submit" class="btn mt-4">Submit</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">Register</h4>
                                            <form action="" method="post">
                                                <div class="form-group">
                                                    <input type="text" name="username" class="form-style"
                                                        placeholder=" Username" id="username" autocomplete="off"
                                                        required>
                                                    <i class="input-icon uil uil-user"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="email" name="email" class="form-style"
                                                        placeholder=" Email" id="email" autocomplete="off" required>
                                                    <i class="input-icon uil uil-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" name="hashPassword" class="form-style"
                                                        placeholder=" Password (minimal 5 karakter)" id="hashPassword"
                                                        autocomplete="off" required pattern=".{5,}">
                                                    <i class="input-icon uil uil-lock-alt"></i>
                                                </div>
                                                <button type="input" name="input" class="btn mt-4">Input</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>

</html>