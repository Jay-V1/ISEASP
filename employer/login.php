<?php
require_once('include/initialize.php');

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user by username
    $stmt = $conn->prepare("SELECT * FROM tblemployers WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);  // Correctly bind the username
    $stmt->execute();
    $result = $stmt->get_result();      // Get the result set
    $user = $result->fetch_assoc();     // Fetch as associative array

    if ($user) {
        if (password_verify($password, $user['PASSWORD'])) {
            if ($user['IS_ACTIVE'] == 1) {
                // Login success
                $_SESSION['EMPLOYER_ID'] = $user['EMPLOYERID'];
                $_SESSION['EMPLOYER_NAME'] = $user['COMPANYNAME']; // Adjust field
                header("Location: index.php");
                exit;
            } else {
                $error = "Account not activated. Please check your email.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Username not found.";
    }
}
?>


<!-- HTML FORM BELOW -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employer Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <h2 class="text-center mb-4">Employer Login</h2>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="mt-3 text-center">
                    Don't have an account? <a href="signup.php">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
