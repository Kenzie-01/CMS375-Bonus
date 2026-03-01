<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login Page</h2>

    <form method="POST">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>

    <?php
    $con = new mysqli("localhost", "root", "", "SocialMediaDB");
    
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    
    $message = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        
        $stmt = $con->prepare("SELECT * FROM Users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $message = 'Login Successful';
                
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
                
            } else {
                $message = 'Login Not Successful - Invalid password';
            }
        } else {
            $message = 'Login Not Successful - Username not found';
        }
        $stmt->close();
    }
    
    $con->close();
    ?>

    <p style="color: red;">
        <?php echo $message; ?>
    </p>

<?php
$con = new mysqli("localhost", "root", "", "SocialMediaDB");

$hashed1 = password_hash('676767', PASSWORD_DEFAULT);
$stmt = $con->prepare("UPDATE Users SET password = ? WHERE username = 'mackenzie'");
$stmt->bind_param("s", $hashed1);
$stmt->execute();

$hashed2 = password_hash('123456', PASSWORD_DEFAULT);
$stmt = $con->prepare("UPDATE Users SET password = ? WHERE username = 'studentA'");
$stmt->bind_param("s", $hashed2);
$stmt->execute();

echo "Passwords updated successfully!";
$con->close();
?>

</body>

</html>