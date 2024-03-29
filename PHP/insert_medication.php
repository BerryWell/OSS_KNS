<?php

// error_reporting(E_ALL);
// ini_set('display_errors',1);

// Connect to the databse
include('dbcon.php');
// include('pbkdf2.compat.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) {
      $UserID=(int)$_POST['ID'];
//      $Date=$_POST['Date'];
      $MedicationName=$_POST['MedicationName'];
      $MedicationDose=$_POST['MedicaionDose'];      

      try {
        // timestamp: YYYY-MM-DD HH:MM:SS
        // codes for checking referential integrity.
        $stmt = $con->prepare("SELECT UserID FROM User WHERE UserID = :UserID");
        $stmt->bindParam(':UserID', $UserID);
        $stmt->execute();
        if( !($stmt->fetch(PDO::FETCH_ASSOC))) {
          echo("No such userID with ". $UserID);
          $e = "UserID not found.";
          throw($e);
        } else {
          $stmt = $con->prepare("INSERT INTO Medication (UserID, MedicationName, MedicationDose) 
              VALUES (:UserID, :MedicationName, :MedicationDose)");
          $stmt->bindParam(':UserID', $UserID);
//          $stmt->bindParam(':Date', $DateRegistered);
          $stmt->bindParam(':MedicationName', $MedicationName);
          $stmt->bindParam(':MedicationDose', $MedicationDose);
          if($stmt->execute()){
            echo("Inserting medication data successful!");
          } else {
            echo("Error inserting data values!");
          }
        }
        
      }
      catch(PDOException $e) {
        die("Error!" . $e->getMessage());
      }
    }
?>