<?php
require_once('include/initialize.php');

if (isset($_POST['signup'])) {
    $contactPerson = trim($_POST['fullname']);  // Assuming fullname is contact person
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $companyName = trim($_POST['companyname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $logo = '';

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM tblemployers WHERE USERNAME = ?");
        $stmt->bind_param("s", $username);  // Bind the username parameter as a string
        $stmt->execute();
        $stmt->store_result();  // Store the result for row count
        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
        } else {
            // Upload logo if uploaded
            if (!empty($_FILES['logo']['name'])) {
                $targetDir = "uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $logo = $targetDir . basename($_FILES['logo']['name']);
                move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
            }

            // Insert employer
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO tblemployers (COMPANYNAME, CONTACTPERSON, USERNAME, EMAIL, PHONE, PASSWORD, ADDRESS, LOGO, DATEJOINED, IS_ACTIVE) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)");
            $insert->bind_param("ssssssss", $companyName, $contactPerson, $username, $email, $phone, $hashed_password, $address, $logo); // Bind parameters
            $insert->execute();

            $success = "Account created successfully. You can now login.";
        }
    }
}


?>

<!-- HTML FORM BELOW -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employer Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var confirmInput = document.getElementById("confirm_password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                confirmInput.type = "text";
            } else {
                passwordInput.type = "password";
                confirmInput.type = "password";
            }
        }
    </script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow p-4">
                <h2 class="text-center mb-4">Employer Signup</h2>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } elseif (isset($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Company Name</label>
                        <input type="text" name="companyname" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Company Address</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Company Contact number</label>
                        <input type="number" name="phone" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Company Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="showPassword" onclick="togglePassword()">
                        <label class="form-check-label" for="showPassword">Show Password</label>
                    </div>

                    <div class="mb-3">
                        <label>Company Logo (optional)</label>
                        <input type="file" name="logo" class="form-control">
                    </div>

                    <button type="submit" name="signup" class="btn btn-primary w-100">Sign Up</button>
                </form>

                <p class="mt-3 text-center">
                    Already have an account? <a href="login.php">Login</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
