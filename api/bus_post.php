<?php
require '../core/database/bootstrap.php';

  $json = file_get_contents('php://input');
  $data = json_decode($json);
  $postmode = $data->postmode;

//Switch Case for updating data
try{
  switch($postmode){
    case "p1":
      $result = $app['database']->UpdateBusData($data);
      var_dump($result);
      break;
    default:
      throw new error("Update Error");
      break;
  }
}
catch(Exception $e){
  echo $e;
}
?>
