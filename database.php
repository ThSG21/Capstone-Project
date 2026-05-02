<?php 

class Database {
   private $pdo;  
 
   function __construct() {
      $dbHost = "127.0.0.1";
      $dbName= "cap_stone_cis2910c";
      $dbUsername = "root";
      $dbPassword = "";
      $this->pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
   }

   function addUser($username, $password, $fullName, $email, $secAnswer) {

        // Create a bcrypt hash for the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        

        // Insert a new row into the user table
        $sql = "INSERT INTO users (username, password, fullname, email, securityQuestion) VALUES (?, ?, ?, ?, ?)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $username);
        $statement->bindValue(2, $passwordHash);
        $statement->bindValue(3, $fullName);
        $statement->bindValue(4, $email);
        $statement->bindValue(5, $secAnswer);
        $statement->execute();
   }

   function changePassword($email, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $passwordHash);
        $statement->bindValue(2, $email);
        $statement->execute();
   }

   function getUser($username, $password) {

        // Select the user from the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $username);
        $statement->execute();

        // Verify password is correct
        while ($row = $statement->fetch()) {
            if (password_verify($password, $row["password"])) {
                return $row["fullname"];
            }
        }

        // Either username does not exist or password is wrong
        return null;
    }

    function getUserByUsername($username) {

        // Select the user from the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $username);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        // Either username does not exist or password is wrong
        return $row;
    }

    function getUserById($id) {

        $sql = "SELECT * FROM users WHERE id = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $id);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row? $row : null;
    }

    function getUserByEmail($email) {

        // Select the user from the database
        $sql = "SELECT * FROM users where email = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $email);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        // Either username does not exist or password is wrong
        return $row ? $row : null;
    }

    function getSecurityAnswer($email) {

        $sql = "SELECT securityQuestion FROM users WHERE email = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $email);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['securityQuestion'] : null;
    }

    function getStylistId($stylist) {

        $sql = "SELECT id FROM  stylist WHERE name = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $stylist);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row? $row['id'] : null;

    }


    function setAppointment($userId, $stylistId, $timeSlot, $date){

        $sql = "INSERT INTO appointments (userId, stylistId, time_slot, date) VALUES (?, ?, ?, ?)";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $userId);
        $statement->bindValue(2, $stylistId);
        $statement->bindValue(3, $timeSlot);
        $statement->bindValue(4, $date);
        $statement->execute();

    }

    function getAppointmentsByUserId($userId) {
        $sql = "
            SELECT a.date, a.time_slot, s.name AS stylist
            FROM appointments a
            JOIN stylist s ON a.stylistId = s.id
            WHERE a.userId = ?
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function getBookedTimeSlots($stylistId) {

        $sql = "SELECT time_slot, date FROM appointments WHERE stylistId = ?";
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $stylistId);
        $statement->execute();
        $row = $statement->fetchALL(PDO::FETCH_ASSOC);

        return $row;

    }


}

?>