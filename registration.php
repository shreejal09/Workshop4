<?php
$name = $email = "";
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validation
    if ($name === '') {
        $errors['name'] = "Name is required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email";
    }

    if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if ($password !== $confirm) {
        $errors['confirm'] = "Passwords do not match";
    }

    // JSON handling ONLY if no errors
    if (empty($errors)) {

        $file = 'users.json';

        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
        }

        $users = json_decode(file_get_contents($file), true);

        if (!is_array($users)) {
            $users = [];
        }

        $users[] = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        if (file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT))) {
            $success = "Registration Successful";
            $name = $email = "";
        } else {
            $errors['file'] = "Failed to write to file";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
</head>
<body>
<div class="container">
<h2>User Registration</h2>
<link rel="stylesheet" href="style.css">

<?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST">

    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
    <div class="error"><?php echo $errors['name'] ?? ''; ?></div><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
    <div class="error"><?php echo $errors['email'] ?? ''; ?></div><br>

    <label>Password:</label><br>
    <input type="password" name="password">
    <div class="error"><?php echo $errors['password'] ?? ''; ?></div><br>

    <label>Confirm Password:</label><br>
    <input type="password" name="confirm">
    <div class="error"><?php echo $errors['confirm'] ?? ''; ?></div><br>

    <button type="submit">Register</button>
</form>
</div>

</body>
</html>
