<?php

class DBManager{
	var $conect;
  
	var $BaseDatos;
	var $Servidor;
	var $Usuario;
	var $Clave;
	
	function DBManager(){
		$this->BaseDatos = "memorieswebsite";
		$this->Servidor = "memorieswebsite.db.12222564.hostedresource.com";
		$this->Usuario = "memorieswebsite";
		$this->Clave = "Memories@123";	
	}
	
	 function conectar(){
		if(!($con=@mysql_connect($this->Servidor,$this->Usuario,$this->Clave))){
			echo $this->Servidor,$this->Usuario,$this->Clave, "<h1> [:(] Error</h1>";	
			exit();
		}
		if (!mysql_select_db($this->BaseDatos,$con)){
			echo "<h1> [:(] Error db</h1>";  
			exit();
		}
		
		$this->conect=$con;
		return true;	
	}
}
?>

