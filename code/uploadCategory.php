<?php

/**
 * id: Functions.php v3.0 
 * FugaCursos
 * 
 * Nombre:Gustavo Lozano
 * Fecha Creada:
 * Ultima Modificacion: Mayo 6 2011
 * Description: Contiene todas las funciciones necesarias
 */

//-----------------------------------------------------------------
//   Librerias
//-----------------------------------------------------------------

require_once 'functions.php';
require_once '../languages/language.php';


//-----------------------------------------------------------------
//   Main
//-----------------------------------------------------------------

$put = $_GET["q"];
$con = connectDB();
	
	$sql = "SELECT categoryNameS,idCategory FROM fugacursos.Category";
	$result = mysql_query($sql);
		
	echo "<select name=\"categoryId".$put."\">";
	
	/* Crea todoas las opciones de categorias que el usuario esta pidiendo */
	while($row = mysql_fetch_array($result))
	{
		$temp = $row[$all["categoryName"]];
		$idCategory = $row['idCategory'];

		echo "<option value=\"". $idCategory. "\">".$temp."</option>";
	}
	echo "</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		
	echo "<input name=\"categoryDes".$put."\" type=\"text\" size=\"35\" maxlength=\"90\"/> <br>";


mysql_close($con);
?>
