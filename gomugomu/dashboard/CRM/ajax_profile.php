<?php
include("include/config.php");
include("include/class.SHARE.php");
include("include/class.CLIENT.php");

$CLIENT  = new CLIENTS();

if (is_ajax()) {
  if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
    $action = $_POST["action"];
    switch($action) { //Switch case for value of action
      case "test": 
      	$return=$CLIENT->updateClient($_POST);
      	$return["json"] = json_encode($return);
      	echo json_encode($return);
      break;
      case "imagen": 
      	$return['mensaje']=$_FILES["myFile"]["tmp_name"];//=$CLIENT->upload($_POST,$_FILES); 
      	$return["json"] = json_encode($return);      	
      	echo json_encode($return);
      break;
    }
  }
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
/*
function test_function(){  
  $CLIENT  = new CLIENTS();
 
  	$return=$CLIENT->updateClient($_POST);
   

  $return["json"] = json_encode($return);
  echo json_encode($return);
}*/
?>