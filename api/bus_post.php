<?php
require '../core/database/bootstrap.php';

  $json = file_get_contents('php://input');
  $utf_json = utf8_encode($json); 
  $data = json_decode($utf_json);
  $postmode = $data->postmode;
   try{
     switch($postmode){
       case "p1":
         $result = $app['database']->UpdateBusData($data);
         echo($result);
         break;
       default:
         throw new error("Update Error");
         break;
     }
   }
   catch(Exception $e){
     echo($e);
   }
?>
