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
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        
        $naturalJoin = "SELECT * FROM Users NATURAL JOIN Posts LIMIT 10";
        $con->query($naturalJoin);
        
        $innerJoin = "SELECT * FROM Users INNER JOIN Posts ON Users.user_id = Posts.user_id LIMIT 10";
        $con->query($innerJoin);
        
        $leftJoin = "SELECT * FROM Users LEFT JOIN Posts ON Users.user_id = Posts.user_id LIMIT 10";
        $con->query($leftJoin);
        
        $rightJoin = "SELECT * FROM Users RIGHT JOIN Posts ON Users.user_id = Posts.user_id LIMIT 10";
        $con->query($rightJoin);
        
        $fullJoin = "(SELECT * FROM Users LEFT JOIN Posts ON Users.user_id = Posts.user_id) 
                    UNION 
                    (SELECT * FROM Users RIGHT JOIN Posts ON Users.user_id = Posts.user_id) 
                    LIMIT 10";
        $con->query($fullJoin);
    }
    
    $con->close();
    ?>

    <p style="color: red;">
        <?php echo $message; ?>
    </p>

    
</body>
</html>