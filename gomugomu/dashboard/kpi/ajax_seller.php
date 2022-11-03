<?php
include("include/config.php");
include("include/class.SHARE.php");
include("sellers/class.SELLER.php");


if (is_ajax()) {
  if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
    $action = $_POST["action"];
    switch($action) { //Switch case for value of action
      case "click": seller_add_or_remove(); break;
    }
  }
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function seller_add_or_remove(){  
  $SELLER  = new SELLERS();
  
  $idTicket = $_POST['idTicket'];
  $idSeller = $_POST['idSeller'];
  $date     = $_POST['date'];
  $color    = $_POST['color'];
  $return['color']= $SELLER->add_or_remove($idTicket,$idSeller,$date,$color);  
  
  
  
  $return["json"] = json_encode($return);
  echo json_encode($return);
}
?>
