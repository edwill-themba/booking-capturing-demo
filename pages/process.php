<?php

 // gets the connection file
 require_once 'secure/conn.php';

  if(isset($_POST['submit'])){
   try{
    	$conn = getConnection();
      // gets data from user inputs
  	   $name = $_POST['name'];
      $phone = $_POST['phone'];
      $email = $_POST['email'];
      $type = $_POST['room_type'];
      $checkIn = $_POST['checkin'];
      $checkOut = $_POST['checkout'];
      // calls the validate input function
      validateInput($name, $phone, $email, $type, $checkIn, $checkOut);
      // calls the validate date function
      validateDates($checkIn, $checkOut);
      // gets number of days to stay
      $noOfDays = getNumberOfDays($checkIn, $checkOut);
      // checks if number of days are greater than 7
      if($noOfDays > 7){
        echo 'the number of days a guest is permitted to stay is 7';
        exit;
      }
      $pending_booking = checkBooking($checkIn,$checkOut,$email);
      // check if user have pending bookings on this days
      if(is_array($pending_booking)){
      if($checkIn >= $pending_booking['checkIn'] && $checkIn <= $pending_booking['checkOut']){
        echo 'you have a pending booking on between this days';
        exit;
       }
      }
      // calculate the amount 
       $amount = $noOfDays * $type;
       $insert_query = "INSERT INTO booking(name,email,phone,checkIn,checkOut,
        type,noOfDays,amount) VALUES(?,?,?,?,?,?,?,?)";
        $statement = $conn->prepare($insert_query);
        $booking = $statement->execute([$name,$email,$phone,$checkIn,$checkOut,$type,$noOfDays,$amount]);
        echo "<h3>Your Booking"."   ".$name ."</h3>";
        echo "<h3>Your email is"."   ".$email ."</h3>";
        echo "<h3>Your phone is"."   ".$phone ."</h3>";
        echo "<h3>Check In"."   ".$checkIn ."</h3>";
        echo "<h3>Check Out"."   ".$checkOut ."</h3>";
        echo "<h3>Number of days is"."   ".$noOfDays ."</h3>";
        echo "<h3>The amount is"."   R".$amount ."</h3>";
        
    }Catch(PDOException $ex){
       echo $ex->getMessage();
       exit;
   }
  }
 /*
  * validates input entered by user and takes neccessary actions if
  * invalid data was entered
  */ 
  function validateInput($name,$phone,$email,$type,$checkIn,$checkOut){

    if(empty($name) || empty($phone) || empty($email) || empty($type) || empty($checkIn) || empty($checkOut)){
     	 echo 'please make sure that all fields on the form are filled';
         exit;
     }
     // check if name contain special charactes or numbers
     if((preg_match('/[\'^£$%&*()}{@#~?.:;""!`><>,|=_+¬-]/', $name)) || (preg_match('~[0-9]+~', $name))){
        echo 'the name must not contain special characters or numbers';
        exit;   
    }
    // check if name is length is greater than one
     if(strlen($name) <= 1){
        echo 'the name must not be a single character';
        exit;   
     }
     // check if phone number lengths is 10 charactes
     if(strlen($phone) != 10){
        echo 'the phone must be 10 numbers';
        exit;   
     }
     // check if phone number is not numeric
     if(!is_numeric($phone)){
      echo 'the phone number must be numeric';
      exit;
     }
   }

   /**
    * Checks if date entered by the user are valid
    * 
    */
   function validateDates($checkIn,$checkOut){

      $today = date('Y-m-d');
      if($checkIn > $checkOut){
         echo 'the check in date must be before check out date';
         exit;
      }
      // check if check in date has passed
      if ($checkIn < $today){
         echo 'the date you have selected has already passed or is today s date, please choose another date';
         exit;
      }
      
   }
   /**
    * gets the number of days 
    * @return number of days
    */
   function getNumberOfDays($checkIn,$checkOut){
     $date1 = new DateTime($checkIn);
     $date2 = new DateTime($checkOut);
     $nrOfDays = $date2->diff($date1)->format('%a');
     return $nrOfDays;
   }

   /*
    * checks if user has another pedding booking between
    *
    */
   function checkBooking($checkIn,$checkOut,$email){
      $found = false;
    $conn = getConnection();
    $booking = [];
    try{
        $query = "SELECT * FROM booking where email =:email";
        $statement = $conn->prepare($query);
        $statement->execute([':email'=> $email]);
        $booking = $statement->fetch(PDO::FETCH_ASSOC);
    }catch(PDOException $ex){
        echo $ex->getMessage();
        exit;
    }
      return $booking;
   }