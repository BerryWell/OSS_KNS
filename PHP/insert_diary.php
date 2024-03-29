<?php

// error_reporting(E_ALL);
// ini_set('display_errors',1);

// Connect to the databse
include('dbcon.php');
include('./php-vadersentiment/vadersentiment.php');

    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
    
    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) {
      $UserID=(int)$_POST['ID'];
      //$DateRegistered=$_POST['Date'];
      $Contents=$_POST['Contents'];
      $Contents_translated=$_POST['english_contents'];
      //$Value=$_POST['Value'];
      $sentimenter = new SentimentIntensityAnalyzer();
      $result = $sentimenter->getSentiment($Contents_translated);
      $Value = (int)($result['compound']*100);

      try {
        // timestamp: YYYY-MM-DD HH:MM:SS
        // codes for checking referential integrity.
        $stmt = $con->prepare("SELECT UserID FROM User WHERE UserID = :UserID");
        $stmt->bindParam(':UserID', $UserID);
        $stmt->execute();
        if( !($stmt->fetch(PDO::FETCH_ASSOC))) {
          echo("No such userID with UserID");
          $e = "UserID not found.";
          throw($e);
        } else {
          $stmt = $con->prepare("INSERT INTO Diary (UserID, Contents, Value) VALUES (:UserID, :Contents, :Value)");
          $stmt->bindParam(':UserID', $UserID);
          // $stmt->bindParam(':Date', $DateRegistered);
          $stmt->bindParam(':Contents', $Contents);
          $stmt->bindParam(':Value', $Value);
          if($stmt->execute()){
            echo("Inserting diary data successful!");
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
