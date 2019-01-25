<?php
function EncodeJSON($data){
  header('Content-type:application/json; charset=utf-8');
  $json_str = json_encode($data);
  return $json_str;
}
?>
