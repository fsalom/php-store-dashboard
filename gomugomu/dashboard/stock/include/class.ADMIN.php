<?php
class ADMIN extends FUNCIONES  { 
	public $week;
	public $year;
	public $target;
	
	public function __construct($week, $year)
	{
		$this->week	 		= $week;
		$this->year	 		= $year;
		$this->getWeekTarget();		
	}
	public function getWeekTarget()
	{
		$this->connectMYSQL();
		$query = mysql_query("SELECT value FROM `dash_target` WHERE `week` = '".$this->week."' AND `year` = '".$this->year."'") or die(mysql_error());
		$data  = mysql_fetch_array($query);
		$this->target=$data['value'];
		$this->closeMYSQL();
				
	}	
}
?>