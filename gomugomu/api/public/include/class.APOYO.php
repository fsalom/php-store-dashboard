<?php
/***********************************************************
 *	DAY - almacenara los datos del día 
***********************************************************/
class DAY {
	public $date;
	public $total;
	public $totalOriginal;
	public $nTickets;
	public $nArticles;
	public $perTicket;
	public $averageTicket;
	public $margin;
	public $desc;


	public function __construct($date, $total = 0, $totalOriginal = 0, $nTickets = 0, $nArticles = 0, $perTicket = 0, $averageTicket = 0, $margin = 0, $desc = 0)
	{
		$this->date	 		= $date;
		$this->total 	 	= number_format($total,2,',','.');
		$this->totalOriginal= number_format($totalOriginal,2,',','.');
		$this->nTickets		= $nTickets;
		$this->nArticles	= $nArticles;
		$this->perTicket 	= number_format($perTicket,2,',','.');
		$this->averageTicket= number_format($averageTicket,0,',','.');
		$this->margin		= number_format($margin,0,',','.');
		$this->desc		 	= number_format($desc,0,',','.');
	}
}
/***********************************************************
 *	ARTICLE - Almacena los datos de cada uno de los articulos
***********************************************************/
class ARTICLE {
	public $info;
	public $return;
	public $description;
	public $price;
	public $originalPrice;
	public $numPieces;
	public $margin;
	public $percentage;


	public function __construct($info, $return, $description, $price, $originalPrice, $numPieces, $margin, $percentage)
	{
		$info['name'] = str_replace(
									array("Superdry",
									"SUPERDRY",
									" XXS ",
									" XS ",
									" S ",
									" M ",
									" L ",
									" XL ",
									" XXL ",
									//Buscamos en el nombre toda la parte del codigo del color hasta el final para quitarlo del nombre
									substr($info['name'],
									strpos($info['name'],substr($info['colour'],0,3)),
									strlen($info['name'])))
									,'', $info['name']
									);
		$this->info 	 	= $info;
		$this->return 	 	= $return;			
		$this->description  = $description;
		$this->price 		= number_format($price,2,',','.');
		$this->originalPrice= $originalPrice;
		$this->numPieces 	= $numPieces;
		$this->margin	 	= $margin;
		$this->percentage 	= $percentage;
	}
}
/***********************************************************
 *	MARGIN - Almacena los calculos de margen en dinero y porcentage
***********************************************************/
class MARGIN {
	public $money;
	public $percentage;

	public function __construct($money, $percentage)
	{
		$this->money 	 = number_format($money,2,',','.');
		$this->percentage= number_format($percentage,0,',','.');
	}
}
/***********************************************************
 *	ITEM - Almacena los datos adicionales del artículo
***********************************************************/
class ITEM {
	public $name;
	public $colour;
	public $size;
	public $reference;
	public $season;
	public function __construct($name, $colour, $size, $reference, $season)
	{
		$this->name 	 = $name;
		$this->colour 	 = $colour;
		$this->size 	 = $size;
		$this->reference = $reference;
		$this->season 	 = $season;
	}
}

?>