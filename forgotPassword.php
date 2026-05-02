<?php
    session_start();
    
    if (!isset($_SESSION["step"])) {
        $_SESSION["step"] = 0;
    }
?>
<?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $error = "";

        require "./database.php";
        $db = new Database;
        

        if($_SESSION["step"] == 0){

            $emailAd = trim($_POST["email"]);
            $dbuser = $db->getUserByEmail($emailAd);

            if (!$dbuser) {
                $error = "Email not found.";
            }else {
                $_SESSION["email"] = $emailAd;
                $_SESSION["step"] = 2;
            }

        } elseif($_SESSION["step"] == 2){

            $secQuestion = isset($_POST["secQuestion"])? trim($_POST["secQuestion"]):"";
            $email = $_SESSION['email'];
            $storedAnswer = $db->getSecurityAnswer($email);

            if($storedAnswer !== $secQuestion){  
                $error = "Incorrect, try again.";
            }else{
                $_SESSION["step"] = 3;
            }

        }elseif($_SESSION["step"] == 3){
            $newPassword = trim($_POST["newPassword"]);
            $cnewPassword = trim($_POST["cnewPassword"]);

            if(!($newPassword == $cnewPassword)){
                $error = "Passwords do not match.";
            }else {
                $dbuser = $db->changePassword($_SESSION["email"], $newPassword);
                 
                $_SESSION["success"] = "Password successfully reset!";

                $_SESSION["step"] = 0;
                unset($_SESSION["email"]);

                header("Location: login.php");
                exit();
            }
            
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
    <title>Forgot Password</title>
</head>
<body>
    <?php if($_SESSION["step"] == 0 || $_SESSION["step"] == 1): ?>
        <div class="container">
        
            <form action="forgotPassword.php" method="POST">
                <H1>Recuperate Password</H1>
                <div class="form-group">
                    <label for="email">Enter your email</label>
                    <input type="email" name="email" id="email" class="form-control" required><br>

                    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="login.php" class="btn" style="color:black" onmouseover="this.style.backgroundColor='grey', this.style.color='white'" onmouseout="this.style.backgroundColor='white', this.style.color='black'">Go Back</a>
                </div>
            </form>

        </div>
    <?php endif; ?>

    <?php if($_SESSION["step"] == 2): ?>
        <div class="container">
        
            <form action="forgotPassword.php" method="POST">
                <H1>Recuperate Password</H1>
                <div class="form-group">
                    <label for="secQuestion">What is you first pet's name?</label>
                    <input type="text" name="secQuestion" id="secQuestion" class="form-control" required><br>

                    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="login.php" class="btn" style="color:black" onmouseover="this.style.backgroundColor='grey', this.style.color='white'" onmouseout="this.style.backgroundColor='white', this.style.color='black'">Go Back</a>
                </div>
            </form>

        </div>
    <?php endif; ?>

    <?php if($_SESSION["step"] == 3): ?>
        <div class="container">
        
            <form action="forgotPassword.php" method="POST">
                <H1>Recuperate Password</H1>
                <div class="form-group">
                    <label for="newPassword">Enter new password:</label>
                    <input type="password" name="newPassword" id="newPassword" class="form-control" required><br>
                    <label for="cnewPassword">Confirm new password:</label>
                    <input type="password" name="cnewPassword" id="cnewPassword" class="form-control" required><br>

                    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="login.php" class="btn" style="color:black" onmouseover="this.style.backgroundColor='grey', this.style.color='white'" onmouseout="this.style.backgroundColor='white', this.style.color='black'">Go Back</a>
                </div>
            </form>

        </div>
    <?php endif; ?>
    
</body>
</html>

