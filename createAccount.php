<?php 

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $error = "";
        
        $username = trim($_POST["userName"]);
        $fullName = trim($_POST["fullName"]);
        $emailAd = trim($_POST["emailAd"]);
        $secQuestion = trim($_POST["secAnswer"]);
        $password = trim($_POST["passWord"]);

        require "./database.php";
        $db = new Database;
        $user = $db->getUserByUsername($username);
        $userEmail = $db->getUserByEmail($emailAd);
        
        if($user){
            $error = "Username Already Exists";
        }elseif($userEmail){
            $error = "A user with this email already exists";
        }
        else{

            $_SESSION["success"] = "Account created successfully!";
            $db->addUser($username, $password, $fullName, $emailAd, $secQuestion);
            header("Location: login.php");
            exit;
        }
          
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Styles/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Create Account</title>
</head>
<body style="margin:0; font-family:Segoe UI, sans-serif; color:white; background: linear-gradient(-45deg, #ff6ec4, #7873f5, #4ADEDE, #C774E8); background-size:400% 400%; animation: gradient 10s ease infinite;">
    
    <div class="container">
        
        <form method="POST" action="createAccount.php" id="createAccount">
            <h1>Create Account</h1>
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input id="fullName" type="text" name="fullName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="emailAd">Email Address</label>
                <input id="emailAd" class="form-control" type="email" name="emailAd" required>
            </div>
            <div class="form-group">
                <label for="userName">Username</label>
                <input id="userName" type="text" name="userName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="passWord">Password</label>
                <input id="passWord" type="password" name="passWord" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="secAnswer">What is your first pet's name?</label>
                <input id="secAnswer" type="text" name="secAnswer" class="form-control" required>
            </div> 
            <br>
            <button type="submit" class="btn btn-primary" onsubmit="">Create Account</button><br>
            <a href="/login.php" style="color:white" onmouseover="this.style.color='darkblue'" onmouseout="this.style.color='white'">Back To Login</a>

            <?php if (!empty($error)) echo "<p style='color:red; background-color: rgb(189, 187, 207);'>$error</p>"; ?>

        </form>        
    </div>

    
    
</body>
</html>