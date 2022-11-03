<?php
    class STAT {
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

    class STATS extends SHARE  {
        private $msg;
        public function getTcuentoBy($date){   
            $dateNew = str_replace("-", "", $date);      
            $this->connectMYSQL();
            $query = "SELECT people FROM `dash_tcuento` WHERE day = '$dateNew'";
            $total = 0;
            if($result =  $this->query($query)){
                while($data=$result->fetch_assoc()){
                    $total += $data['people'];
                }
            }
            $this->closeMYSQL();
            return $total;
        }

        public function getTcuentoByCurrentMonth(){   
            $month = date("Ym", time());
            $this->connectMYSQL();
            $query = "SELECT people FROM `dash_tcuento` WHERE day LIKE '$month%'";
            $total = 0;
            if($result =  $this->query($query)){
                while($data=$result->fetch_assoc()){
                    $total += $data['people'];
                }
            }
            $this->closeMYSQL();
            return $total;
        }

        public function getNumTickets($date){            
            /********************************************************
            ACCEDEMOS A BECOSOFT PARA CALCULAR LA CANTIDAD QUE LLEVAMOS
            *********************************************************/
            $link = mssql_connect(_MSSQL_SERVER, _MSSQL_USERNAME, _MSSQL_PASSWORD);
            if (!$link)
                die('get_data: Error al conectar al servidor para obtener la cantidad que llevamos MSSQL: '.mssql_get_last_message());
            
            $selected = mssql_select_db( _MSSQL_DB, $link) 
            or die("Couldn't open database $myDB ." .mssql_get_last_message() ); 
            
            $token = explode('-',$date);
            $dy = $token[0];
            $dm = $token[1];
            $dd = $token[2];
                
            $queryDetail = 'SELECT Factuurnummer FROM VerkoopBetaling WHERE  
      (DATEPART(yy, Datum) = "'.$dy.'"
AND    DATEPART(mm, Datum) = "'.$dm.'"
AND    DATEPART(dd, Datum) = "'.$dd.'")';
                
            $resultDetail = mssql_query($queryDetail) 
                or die('get_data: Error al hacer la query para saber la cantidad que llevamos MSSQL '.mssql_get_last_message());
             
            $data =mssql_fetch_array($resultDetail);
            return $data;
            //return $dy.' '.$dm.' '.$dd;
        }

        
    }
    ?>
