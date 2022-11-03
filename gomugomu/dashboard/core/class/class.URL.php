<?php
class url{
	var $_array;	
	function url($array){
		$this->_array=$array;
	}
	function build_htaccess(){

		$CLOCK=new CLOCK("HTACCESS");
		if($CLOCK->getTimeMod()){
		
		$file= fopen(".htaccess", "w");
		
		$line="#FECHA DE ACTUALIZACION : ".date("d-m-Y H:i:s",time())."\r\n";
		$line.="<IfModule mod_rewrite.c>\r\n";
		$line.="RewriteEngine on\r\n";
		$line.="RewriteBase /\r\n";
		$line.="#DEFINIDO PARA SPAM-BOTS\r\n";		
		$line.="RewriteCond %{HTTP_REFERER} (sex) [NC,OR]\r\n";
		$line.="RewriteCond %{HTTP_REFERER} (drugs) [NC,OR]\r\n";
		$line.="RewriteCond %{HTTP_REFERER} (rock\&roll) [NC]\r\n";
		$line.="RewriteRule . http://www.microsoft.com [L]\r\n";
		$line.="RewriteRule ^inicio/$ index.php [L]\r\n";
		$line.="RewriteRule ^quienes-somos/$ index.php?go=about [L]\r\n";
		$line.="RewriteRule ^blog/$ index.php?go=news [L]\r\n";
		$line.="RewriteRule ^visitanos/$ index.php?go=visit [L]\r\n";
		$line.="RewriteRule ^que-hacemos/$ index.php?go=info [L]\r\n";
		$line.="RewriteRule ^biocombustible/$ index.php?go=info&do=biocombustible [L]\r\n";
		$line.="RewriteRule ^localizacion/$ index.php?go=where [L]\r\n";
		$line.="RewriteRule ^formacion/$ index.php?go=info&do=formacion [L]\r\n";
		$line.="RewriteRule ^produccion/$ index.php?go=info&do=produccion [L]\r\n";
		$line.="RewriteRule ^consultoria/$ index.php?go=info&do=consultoria [L]\r\n";
		$line.="RewriteRule ^visitas-guiadas/$ index.php?go=info&do=visit [L]\r\n";
		$line.="RewriteRule ^exito/$ index.php?go=suscribe&do=success [L]\r\n";
		$line.="RewriteRule ^fallo/$ index.php?go=suscribe&do=fail [L]\r\n";
		$line.="RewriteRule ^eventos/$ index.php?go=info&do=events  [L]\r\n";
		fputs($file,$line);
		include($this->_array);
		$num=count($_modules);
		for ($i=0; $i < $num; $i++){
			
			if(file_exists($_modules[$i].'/htaccess/url.php')){
				
				if($aux= fopen($_modules[$i].'/htaccess/url.php', "r")){
					//echo $_modules[$i];
					while(!feof($aux)){
						$buffer = fgets($aux,4096); 
						fputs($file,$buffer);
						$line="\r\n";
						fputs($file,$line);
					}
					fclose($aux);
				}else{
					
				}
				
			}else{
				//echo "asd2";
			}
		}
		$line="\r\n</IfModule>";
		fputs($file,$line);
		
		fclose($file);
		}
	}

}
?>