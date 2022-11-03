<?php
    class SELLER {
        public $id;
        public $name;
        public $color;
        
        public function __construct($id, $name, $color)
        {
            $this->id= $id;
            $this->name = $name;
            $this->color = $color;
        }
    }
    
    class SELLERTICKET {
        public $id;
        public $idTicket;
        public $idSeller;
        public $numProducts;
        public $numTickets;
        
        public function __construct($id, $idTicket, $idSeller, $numProducts = 0, $numTickets = 0)
        {
            $this->id= $id;
            $this->idTicket = $idTicket;
            $this->idSeller = $idSeller;
            $this->numProducts = $numProducts;
            $this->numTickets = $numTickets;
        }
    }

    class SELLERS extends SHARE  {
        private $msg;
        public function getStats($id, $year, $month){            
            $this->connectMYSQL();
            
            if($year=='' && $month==''){
                $query = "SELECT SUM(TICKET.`items`), TICKET.`id_ticket`
                          FROM `dash_ticket` AS TICKET
                          INNER JOIN `dash_seller_ticket` AS SELLERTICKET ON SELLERTICKET.`id_ticket` = TICKET.`id_ticket`
                          WHERE SELLERTICKET.`id_seller` = $id AND TICKET.`description` != 'Waardebon'
                          GROUP BY TICKET.`id_ticket`";
            }else{
                $query = "SELECT SUM(TICKET.`items`), TICKET.`id_ticket`
                          FROM `dash_ticket` AS TICKET
                          INNER JOIN `dash_seller_ticket` AS SELLERTICKET ON SELLERTICKET.`id_ticket` = TICKET.`id_ticket`
                          WHERE SELLERTICKET.`id_seller` = $id AND TICKET.`description` != 'Waardebon' AND TICKET.`date` LIKE '$year-$month%'
                          GROUP BY TICKET.`id_ticket` ";
            }
            if($result =  $this->query($query)){
                while($data=$result->fetch_assoc()){
                    $items += $data['SUM(TICKET.`items`)'];
                    $tickets += 1;
                }
            }

            if($year=='' && $month==''){
                $query = "SELECT SUM(PAYMENT.`quantity`)
                          FROM `dash_payment` AS PAYMENT
                          INNER JOIN `dash_seller_ticket` AS SELLERTICKET ON SELLERTICKET.`id_ticket` = PAYMENT.`id_ticket`
                          WHERE SELLERTICKET.`id_seller` = $id";
            }else{
                $query = "SELECT SUM(PAYMENT.`quantity`)
                          FROM `dash_payment` AS PAYMENT
                          INNER JOIN `dash_seller_ticket` AS SELLERTICKET ON SELLERTICKET.`id_ticket` = PAYMENT.`id_ticket`
                          WHERE SELLERTICKET.`id_seller` = $id AND PAYMENT.`date` LIKE '$year-$month%'";
            }

            if($result =  $this->query($query)){
                while($data=$result->fetch_assoc()){
                    $payment = $data['SUM(PAYMENT.`quantity`)'];
                }
            }

            $value['items'] = $items;
            $value['tickets'] = $tickets;
            $value['total'] = $payment;

            $this->closeMYSQL();
            return $value;
        }

        public function getAllSellers(){            
            $this->connectMYSQL();
            $query = "SELECT * FROM `dash_seller` WHERE date_last_day IS NULL";
            $sellers = array();
            if($result =  $this->query($query)){
                while($data=$result->fetch_assoc()){
                    $sellers[] = NEW SELLER($data['id'],$data['name'], $data['color']);
                }
            }
            $this->closeMYSQL();
            return $sellers;
        }

        public function getTicketsSellersFor($date){            
            $this->connectMYSQL();
            $query = "SELECT * FROM `dash_seller_ticket` WHERE date= '$date'";
            $tickets = array();
            if($result =  $this->query($query)){
                while($data=$result->fetch_assoc()){
                    $tickets[] = NEW SELLERTICKET($data['id'],$data['id_ticket'],$data['id_seller']);
                }
            }
            $this->closeMYSQL();
            return $tickets;
        }

        public function containsID($id, $ticketID, $tickets){            
            $return = false;
            foreach ($tickets as $ticket) { 
                if ($id == $ticket->idSeller && $ticketID == $ticket->idTicket) {
                    $return = true;
                }
            }
            return $return;
        }

        public function existsTicketSeller($id_ticket, $id_seller, $date){
            $this->connectMYSQL();
            $query = "SELECT * FROM `dash_seller_ticket` WHERE date = '$date' AND id_seller = '$id_seller' AND id_ticket = '$id_ticket'";
            $return = false;
            if($result =  $this->query($query)){
                $return  =  $this->num_rows($result) == 0 ? false : true;
            }
            $this->closeMYSQL();
            return $return;
        }


        public function add_or_remove($idTicket,$idSeller,$date,$color){
            if ($this->existsTicketSeller($idTicket,$idSeller,$date)){
                $this->actionTicketSeller('REMOVE',$idTicket,$idSeller,$date);
                return "";
            }else{
                $this->actionTicketSeller('INSERT',$idTicket,$idSeller,$date);
                return $color;
            }
        }

        public function actionTicketSeller($action,$idTicket,$idSeller,$date){
            if ($action == 'REMOVE'){
                $this->connectMYSQL();
                $query = "DELETE FROM `dash_seller_ticket` WHERE 
                        id_ticket = '$idTicket' AND
                        id_seller = '$idSeller' AND
                        date      = '$date'" ;
                $this->query($query);
                $this->closeMYSQL();
            }
            if ($action == 'INSERT'){
                $this->connectMYSQL();
                $query = "INSERT INTO `dash_seller_ticket` (
                    `id_ticket`,
                    `id_seller`,
                    `date`
                    )VALUES(
                            '".$idTicket."',
                            '".$idSeller."',
                            '".$date."'
                            )" ;
                $this->query($query);
                $this->closeMYSQL();
            }
        }

        
        
        public function getSeller($id){
            $this->connectMYSQL();
            $query = "SELECT * FROM `dash_seller` WHERE id=" . $id  ;
            if($result =  $this->query($query)){
                $data=$result->fetch_assoc();
                
                $return= NEW SELLER($data['id'],
                                    $data['name']);
            }
            $this->closeMYSQL();
            return $return;
        }
       
        public function insertClient($POST){
            if($this->checkClientExist($POST["email"])){
                $return['mensaje']    ="Este usuario ya existe";
                $return['ok']         = "1";
                return $return;
            }else{
                $this->connectMYSQL();
                if($this->checkFields($POST)){
                    $newDate = "";
                    if($POST['birthdate']){
                        $newDate = preg_replace("/(\d+)\D+(\d+)\D+(\d+)/","$3-$2-$1",$POST['birthdate']);}
                    $query =  "INSERT INTO `crm_client` (
                    `name`,
                    `surname`,
                    `birthdate`,
                    `gender`,
                    `email`,
                    `phone`,
                    `details`,
                    `address`,
                    `province`,
                    `zipcode`,
                    `city`,
                    `from`
                    )VALUES(
                            '".$POST['name']."',
                            '".$POST['surname']."',
                            '".$newDate."',
                            '".$POST['gender']."',
                            '".$POST['email']."',
                            '".$POST['phone']."',
                            '".$POST['details']."',
                            '".$POST['address']."',
                            '".$POST['province']."',
                            '".$POST['zipcode']."',
                            '".$POST['city']."',
                            '".$POST['from']."'
                            )" ;
                    if ($result = $this->query($query)) {
                        $return['mensaje']    ="Cliente Guardado";
                        $return['ok']         = "0";
                    }else{
                        $return['mensaje']    = "Se ha producido un error al guardar el usuario: <br/>". $this->error();
                        $return['ok']         = "2";
                    }
                }else{
                    $return['mensaje']    = $this->msg;
                    $return['ok']         = "2";
                }
                $this->closeMYSQL();
                
                return $return;
            }
        }
        
        public function disableClient($id){
            $this->connectMYSQL();
            //STATUS 0 -> allowed
            //STATUS 1 -> disabled
            $return=false;
            $query = "UPDATE `crm_client` set status=1 WHERE id=" . $id  ;
            if($result =  $this->query($query)){
                $return=true;
            }
            $this->closeMYSQL();
            //return $return;
        }
    }
    ?>
