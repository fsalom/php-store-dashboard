<?php
class render{
	
	var $frontend;
	var $backend;
	var $url_template;
	var $name_template;
	var $name_templateAdmin;
	
	function imprimir($frontend,$backend){		
		$this->name_template=$name;
		$this->name_templateAdmin=$admin;		
	}

	function web($content){
	
		$render['url']=_FRONTENDDIR._FRONTENDNAME;	
		
		if(!isset($_SESSION['systemCSS']))
			$_SESSION['systemCSS']="modules/".$_SESSION['moduleName']."/style/style.css";		
	
		$render['css']='<link href="'._URLCORE.'function/ICSS.php?moduleName='.$_SESSION['moduleName'].'" rel="stylesheet" type="text/css" />';
		$render['js']=$_SESSION['templateJS'];
		$render['title']=_webNAME.$_SESSION['template_title'];
		$_SESSION['template_title']="";
		$render['login']=$_SESSION['templateLogin'];
		$render['content']=$content;
		$render['category']=$_SESSION['category'];
		$render['left']=$_SESSION['left'];
		
		/*----------------------------------------
		EXTRA esto hay que cambiarlo en el futuro para que acepte otros tags
		*/
		switch($_SESSION['login_level']){
			case '10':
				$render['opt1']='';
				$render['opt2']='';
				$render['opt3']='';
				$render['opt4']='';
				$render['opt5']='';
				$render['opt6']='';
			break;
			case '5':
				$render['opt1']='';
				$render['opt5']='';
				$render['opt2']='class="hide"';
				$render['opt3']='class="hide"';
				$render['opt4']='class="hide"';
				$render['opt6']='';
			break;
			case '1':
				$render['opt5']='';
				$render['opt1']='class="hide"';
				$render['opt2']='class="hide"';
				$render['opt3']='class="hide"';
				$render['opt4']='class="hide"';
				$render['opt6']='class="hide"';

			break;
		}	
			
			
				
		$_SESSION['left']="";
		
			$template=_FRONTENDDIR._FRONTENDNAME."index.html";
		
			
		$code=$this->template($template,$render);
		$result = eval("?>" . $code . "<?");
		echo $resutl;
		
		
		
	}	

	function admin(){
	
			switch($_SESSION['login_level']){
			case '10':
				$print['opt1']='';
				$print['opt2']='';
				$print['opt3']='';
			break;
			case '5':
				$print['opt1']='';
				$print['opt2']='class="hide"';
				$print['opt3']='class="hide"';
			break;
			case '1':
				$print['opt1']='class="hide"';
				$print['opt2']='class="hide"';
				$print['opt3']='class="hide"';
			break;
		}	
	
		$print['url']="../"._BACKENDDIR._BACKENDNAME;	
		
		if($_SESSION['left']!="")
			$print['left']=$_SESSION['left'];
		else
			$print['left']="";
			
		if($_SESSION['content']!="")	
			$print['content']=$_SESSION['content'];
		else
			$print['content']="";
		
		if($_SESSION['right']!="")	
			$print['right']=$_SESSION['right'];
		else
			$print['right']="";
		
		if($_SESSION['top']!="")	
			$print['top']=$_SESSION['top'];
		else
			$print['top']="";
		
		if($_SESSION['footer']!="")	
			$print['footer']=$_SESSION['footer'];
		else
			$print['footer']="";
		
		if($_SESSION['panel']!=""){
			$print['panel']=$_SESSION['panel'];
			$_SESSION['panel']="";
		}else{
			$print['panel']="";
		}
		if(($_GET['go']=="days")&&($_GET['do']=="control"))
			$print['current1']='class="current"';
		if(($_GET['go']=="days")&&($_GET['do']==""))
			$print['current2']='class="current"';
		if(($_GET['go']=="days")&&($_GET['do']=="stats"))
			$print['current3']='class="current"';
		if(($_GET['go']=="days")&&($_GET['do']=="tickets"))
			$print['current4']='class="current"';
		if(($_GET['go']=="user"))
			$print['current5']='class="current"';
		
		$print['css']='<link href="../core/function/AICSS.php?moduleName='.$_SESSION['moduleName'].'" rel="stylesheet" type="text/css" />';
		//$print['css']='<style>'.file_get_contents("../modules/".$_SESSION["moduleName"]."/admin/style/style.css").'</style>';		
		
		$print['js']=$_SESSION['templateJS'];
		
		$nombre="";
		if(file_exists("../modules/".$_SESSION['moduleName'].'/admin/nav/nav.php')){
				$nombre.=file_get_contents("../modules/".$_SESSION['moduleName']."/admin/nav/nav.php");
		}
		
		$print['submenu']=$nombre;
		$print['whereIam']=$_SESSION['template_whereIam'];
		$template="../"._BACKENDDIR._BACKENDNAME."index.html";	
		$print['user']= utf8_encode($_SESSION['login_username']);
		$imprimir=$this->template($template,$print);
		echo $imprimir;
	}
	
	function showerror($error){
	
		$render['url']='template/system/';	
		$render['error']=$error;
		$template='template/system/index.html';	
		
		echo $this->template($template,$render);
		
	}	
	
	function alogin(){
		$print['action']="";
		$print['url']="../"._BACKENDDIR._BACKENDNAME;
		
		$template=$print['url']."login.html";
	
		$imprimir=$this->template($template,$print);
		echo $imprimir;
	}
	function login(){
		$print['action']="";
		$print['url']=_FRONTENDDIR._FRONTENDNAME;
		$template=_FRONTENDDIR._FRONTENDNAME."login.html";
	
		$imprimir=$this->template($template,$print);
		echo $imprimir;
	}
	function template($template,$config){
		$s=file_get_contents($template);
		foreach ($config as $i=>$v) {
			$s=str_replace("{".$i."}",$v,$s);
		}
		
		return $s;		
	}
	
	function ver(){
		echo "algo";
	}
}
?>
