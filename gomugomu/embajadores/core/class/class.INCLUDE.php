<?php
class includes{
	function includes($include){
		$exit=0;
		$n=0;
		
		while($exit!=1){
			//echo $n;
			if(file_exists($include[$n]."/function.php"))
				include_once($include[$n]."/function.php");
			//echo $n;
			if(file_exists($include[$n]."/index.php"))
				include_once($include[$n]."/index.php");
			
				
			$n++;
			if(!isset($include[$n])) $exit=1;
		}
	}
}

?>