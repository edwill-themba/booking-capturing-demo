<?php
 
 /**
  * The purpose of the file is to connect the application to the database
 */

 function getConnection(){
  // sets up up connection variables
  $host = 'localhost';
  $dbname = 'bookingdb';
  $user = 'root';
  $password = '';

   // sets up DSN
   $dsn = 'mysql:host='.$host.';dbname='.$dbname;
  
   $connection = '';
  
   try{
    // sets up a connection
    $connection = new PDO($dsn,$user,$password); 
    //check if button is clicked
  
   } catch(PDOException $ex){
      echo 'error:'.'  '. $ex->getMessage();
   }
   return $connection;
}