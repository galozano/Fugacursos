<?php 

if(isset($_REQUEST['amount']))
{
	try 
	{
		echo uploadDB();
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}
	
}

?>

<script type="text/javascript">

var amount = 0;

function send()
{
	document.getElementById("amount").value = amount ;
	document.forms["formUpload"].submit();
}

/**
 * Agrega una nueva opcion de categorias
 */
function add()
{
	if(amount < 10)
	{
		amount = amount + 1;
		agregar();
	}
}

/**
 * Elimina una opcion de categorias si existen mas de 0 
 */
function erase()
{
	if(amount > 0)
	{
		borrar();
		amount = amount - 1;
	}
}

function agregar()
{
	$.ajax(
			{
				url: "./code/uploadCategory.php?q="+amount ,
			    success: function(msg)
			    {
				    cual = '#cat'+amount;
			        //add the content retrieved from ajax and put it in the #content div
			        $(cual).html(msg);		             
			    }
		});		
}

function borrar()
{
	 cual = '#cat'+amount;
	 $(cual).html("");		
}

</script>

	<div id="page-top">
			<div id="content-complet">
				<a href="index.php?content=User"><?php echo $upload["GoBack"];?></a>
				<div id="forms" align="center">
					<form id="formUpload" action="index.php" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="content" value="Upload"/>
						<table border=0 cellpadding=1 cellspacing=2>
							<tr>
								<td colspan="2"><?php echo $upload["FileName"];?>
								<input name="fileName" type="text" class="casilla" size="35" maxlength="90" /></td>
							</tr>
						
							<tr>
								<td colspan="2"><select name="categoryId0">
								<?php
						
									$con = connectDB();
										
									$sql = "SELECT categoryNameS,idCategory FROM fugacursos.Category";
							
									$result = mysql_query($sql);
										
									while($row = mysql_fetch_array($result))
									{
										$temp = $row[$all["categoryName"]];
										$idCategory = $row['idCategory'];
							
										echo "<option value=\"". $idCategory. "\">".$temp."</option>";
									}
							
									close_connection($con);
						
								?>
								</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<input name="categoryDes0" type="text" size="35" maxlength="90" /></td>
							</tr>
						
							<tr>
								<td colspan="2">
									<div id="cat1"></div>
									<div id="cat2"></div>
									<div id="cat3"></div>
									<div id="cat4"></div>
									<div id="cat5"></div>
									<div id="cat6"></div>
									<div id="cat7"></div>
									<div id="cat8"></div>
									<div id="cat9"></div>
									<div id="cat10"></div>
								</td>
							</tr>
						
							<tr>
								<td colspan="2" >
									<input type="button" value=<?php echo $upload["Add"]; ?> onclick="add()" />
									<input type="button" value=<?php echo $upload["Erase"]; ?> onclick="erase()" />
								</td>
							</tr>
							<tr>
								<td>
								Tipo de Archivo:
								</td>
								<td>
									<select name="fileType">
										<option value="1">Publico</option>
										<option value ="2">Privado</option>
									</select>
								</td>
							<tr>
							<tr>
								<td><input name="uploadFile" type="file" class="casilla" id="uploadFile" size="35" />(.doc, .pdf, .ppt)</td>
							<tr>
						</table>
						<input type="hidden" name="amount" id="amount" />
					</form>
				
					<div align="right">
						<input name="send" type="button" value=<?php echo $upload["Upload"]; ?> onclick="send()" />
					</div>
				</div>
			</div>
	</div>

