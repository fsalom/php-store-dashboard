<?php
class AdminError 
{ 
    /** 
     * Atributo 
     * 
     **/ 
     
    var $_contexto; 

    function AdminError( $contexto ) 
    { 
        $this->_contexto =& $contexto; 
        $GLOBALS['_OBJETO_CONTEXTO'] =& $this->_contexto; 
        $this->activo(false); 
    } 
     
    function iniciar() 
    { 
        if(!function_exists('adm_error')) 
        { 
            function adm_error($numero, $mensaje, $archivo, $linea, $contexto, $retorna=false) 
            { 
                $objContexto =& $GLOBALS['_OBJETO_CONTEXTO']; 
                $objContexto->inicializar($numero, $mensaje, $archivo, $linea, $contexto); 
                if($retorna) 
                    return $objContexto->leer(); 
                else 
                { 
                    print $objContexto->leer(); 
                    print_r($contexto); 
                } 
            } 
        } 
         
        if(!function_exists('errorFatal')) 
        { 
            function errorFatal($buffer) 
            { 
                $buffer_temporal = $buffer; 
                $texto = strip_tags($buffer_temporal); 
                if(preg_match('/Parse error: (.+) in (.+)? on line (\d+)/', $texto, $c)) 
                    return adm_error(E_USER_ERROR, $c[1], $c[2], $c[3], "", true); 
                if(preg_match('/Fatal error: (.+) in (.+)? on line (\d+)/', $texto, $c)) 
                    return adm_error(E_USER_ERROR, $c[1], $c[2], $c[3], "", true); 
                return $buffer; 
            } 
        } 

        if( $this->activo() ) 
        { 
            error_reporting(E_ALL); 
            ob_start('errorFatal'); 
            set_error_handler('adm_error'); 
        } else 
            error_reporting(0); 
    } 
     
    function activo() 
    { 
        switch(func_num_args()) 
        { 
            case 1: $this->_activo = func_get_arg(0); $this->iniciar(); break; 
            case 0: return $this->_activo; 
        } 
    } 
     
} 


class Contexto 
{ 
     
    var $_numero = ""; 
     
    var $_mensaje = ""; 
     
    var $_lineas = 5; 

    /** 
     * Constructor 
     * @access protected 
     */ 
    function inicializar($numero, $mensaje, $archivo, $linea, $contexto) 
    { 
        $this->_mensaje = " 
        <b>Error:</b> $mensaje<br><hr> 
        <b>Archivo:</b> $archivo<br><hr> 
        <b>Línea:</b> $linea<br><hr> 
        <b>Contexto del Código:</b><br><pre>". 
        $this->obtenerContexto($archivo, (int) $linea)."</pre><hr>"; 
    } 
     
    /** 
     * 
     * @access public 
     * @return void  
     **/ 
    function leer() 
    { 
        return $this->_mensaje; 
    } 
     
    /** 
     * 
     * @access public 
     * @return void  
     **/ 
    function obtenerContexto($archivo, $linea) 
    { 
        if (!file_exists($archivo))  
        {  
            //  Nos fijamos que el archivo exista 
            return "El contexto no puede mostrarse - ($archivo) no existe";  
        } elseif ((!is_int($linea)) OR ($linea <= 0)) {  
            //  Verificamos que el numero de linea sea válido 
            return "El contexto no puede mostrarse - ($linea) es un número inválido de linea";  
        } else { 
            //  leemos el codigo 
            $codigo = file( $archivo ); 
            $lineas = count($codigo); 

            //  calculamos los numeros de linea 
            $inicio = $linea - $this->_lineas;  
            $fin = $linea + $this->_lineas;  
            //  verificaciones de seguridad 
            if ($inicio < 0) $inicio = 0; 
            if ($fin >= $lineas) $fin = $lineas; 
            $largo_fin= strlen($fin) + 2; 
             
            for ($i = $inicio-1; $i < $fin; $i++) 
            {  
                //  marcamos la linea en cuestion. 
                $color=($i==$linea-1?"red":"black"); 
                $salida[] = "<span style='background-color: lightgrey'>".($i+1). 
                            str_repeat("&nbsp;", $largo_fin - strlen($i+1)). 
                            "</span><span style='color: $color'>". 
                            htmlentities($codigo[$i]).'</span>'; 
            }  
            return trim(join("", $salida));  
        } 
    } 
} 
?>
