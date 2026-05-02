<?php 

    session_start();
        // If POST then check submitted username and password
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        require "./database.php";
        $db = new Database;

        //Get values submitted from the form
        $username = $_POST["userName"];
        $password = $_POST["passWord"];

        $error ="";

        $name =  $db->getUser($username, $password);

        
        
        if ($name === null) {
            $error = "Incorrect username or password";
        }else {
            $user = $db->getUserByUsername($username);

            $_SESSION["userName"] = $username;
            $_SESSION["fullName"] = $name;
            $_SESSION["email"] = $user["email"];
            header("Location: index.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- <meta http-equiv="refresh" content="2"> -->
    <link rel="stylesheet" href="/Styles/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Log In</title>
</head>
<body style="margin:0; font-family:Segoe UI, sans-serif; color:white; background: linear-gradient(-60deg, #4ade5b, #e25858, #d8d332, #7873f5 ); background-size:400% 400%; animation: gradient 10s ease infinite;">

    <div class="background-shapes">
        <div class="backElements" id="formBelement1"></div>                <div class="backElements" id="formBelement2"></div>
        <div class="backElements" id="formBelement3"></div>
        <div class="backElements" id="formBelement4"></div>
        <div class="backElements" id="formBelement1A"></div>
        <div class="backElements" id="formBelement2A"></div>
        <div class="backElements" id="formBelement3A"></div>
        <div class="backElements" id="formBelement4A"></div>
    </div>
    
    <div class="container">  
        <form id="logInForm" method="POST" action="login.php">
            <h3 class="text-center mb-4">Appointment Setting</h3>        
            <div class="mb-3">
                <label for="userName" class="form-label">Username</label>
                <input id="userName"  name="userName" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="passWord" class="form-label">Password</label>
                <input id="passWord" name="passWord" type="password" class="form-control" required>
            </div>
            <?php if (!empty($error)) echo "<p style='color:red; background-color: rgb(189, 187, 207);'>$error</p>"; ?>

            <button type="submit" class="btn btn-primary">Log In</button> <br><br>
            <a href="/forgotPassword.php" style="color:white" onmouseover="this.style.color='darkblue'" onmouseout="this.style.color='white'">Forgot Password?</a><br>
            <a href="/createAccount.php" style="color:white" onmouseover="this.style.color='darkblue'" onmouseout="this.style.color='white'">Create Account</a>

            
        </form>
        
    </div>
    <?php if (isset($_SESSION["success"])): ?>
        <script>
            alert("<?php echo $_SESSION["success"]; ?>");
        </script>
        <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>

</body>
</html>
