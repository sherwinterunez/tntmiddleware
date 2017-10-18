<?php
   error_reporting( E_ALL );
   
   function handleError($errno, $errstr,$error_file,$error_line) {
      echo "<b>Error:</b> [$errno] $errstr - $error_file:$error_line";
      echo "<br />";
      echo "Terminating PHP Script";
      
      //die();

      return false;
   }
   
   //set error handler
   set_error_handler("handleError");
   
   //trigger error
   //myFunction()

   trigger_error("An error has occured!",E_USER_NOTICE);

   echo $hello;


