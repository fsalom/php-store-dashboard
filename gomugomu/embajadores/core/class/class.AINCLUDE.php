<?php
class includes{
	function includes($include){
		$exit=0;
		$n=0;
		while($exit!=1){
			if(file_exists('../'.$include[$n]."/admin/function.php")){			
				include_once('../'.$include[$n]."/admin/function.php");
			}
			if(file_exists('../'.$include[$n]."/admin/index.php")){			
				include_once('../'.$include[$n]."/admin/index.php");
			}
			

						
				$n++;
			if($include[$n]=='') $exit=1;
		}
	}
}


?>