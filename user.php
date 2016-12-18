<?php 

$error = "";

if (!isset($_SESSION["id"]))
{
	// User not logged in, redirect to login page
	echo "Tienes que logearte";
}
else 
{	
	if(isset($_REQUEST["action"]))
	{
		if ($_REQUEST["action"] == "delete")
		{
			$error= delete();
		}
		else if($_REQUEST["action"] == "share")
		{
			$error = share();
		}
	}

?>

<script type="text/javascript">

$(document).ready(function()
{	
    $('#erase').click(function()
   	{
    	if($("input:checked").length==0)
        {
            $('#error').html('Seleccione un archivo Privado \n');
        }
    	else
    	{
    		$("#action").val('delete');
    		document.forms["formUser"].submit();
    	}
         
    });

    $('#share').click(function()
    {

    	if($("input:checked").length==0)
        {
            $('#error').html('Seleccione un archivo Privado \n');
        }
        else
        {
    	  	var win = $('<div><p>Email de la otra persona:</p></div>');
    	    var userInput = $('<input type="text" style="width:100%"></input>');
    	    userInput.appendTo(win);

    	    // Display dialog
    	    $(win).dialog(
    	    {
    	        'modal': true,
    	        'buttons': 
        	     {
    	            'Ok': function() 
    	            {
    	                $(this).dialog('close');
    	                $("#action").val('share');
    	                $("#email").val($(userInput).val());
    	             
    	                document.forms["formUser"].submit();
    	                
    	            },
    	            'Cancel': function() 
    	            {
    	                $(this).dialog('close');
    	            }
    	        }
    	   });

        }
    });
});

</script>
			<div id="page-top">	
				<div  class="sidebar">
					<div align="center" style="padding-top:20px;">
						Si quieres subir un archivo privado rapidamente solo arrastralo a la caja
					<br><br>
							<form id="file_upload" action="./code/uplodInstantaneo.php" method="POST" enctype="multipart/form-data">
								<input id="userAdenrtro" name="userAdentro" type="hidden" value=<?php echo $_SESSION["id"];?>/>
							    <input type="file" name="file">
							    <button>Upload</button>
							    <div class="file_upload_label">Drag n Drop</div>
							</form>
						<table id="files"></table>
					</div>	
				</div>
				
				<div class="content">
					<div id="menu">
						<div class="buttons">
							<a href="index.php?content=Upload" class="negative"><?php echo $user["Upload"];?> </a> 
							<a id="erase" class="negative"><?php echo $user["Remove"];?></a>
							<a id="share" class="negative">Compartir</a>
						</div>
						<div id="error" style="color:red"><?php echo $error;?></div>
					</div>
					<form action="index.php?content=User" method="get" id="formUser" enctype="multipart/form-data">
						<div id="list-user">
							<input type="hidden" name="action" id="action" value="" />
							<input type="hidden" name="email" id="email" value="" />
							Archivos Publicos
							<ul>
									<?php
										$con = connectDB();
										$id = mysql_real_escape_string($_SESSION['id']);
										
										$sql = "SELECT idFile,fileName FROM fugacursos.Files WHERE idUser = $id AND idFileType = 1";
										
										$result = mysql_query($sql);
										
										if(mysql_num_rows($result) > 0)
										{
											while($row = mysql_fetch_array($result))
											{
												echo "<li class= 'lista'><input type='checkbox' id='files[]' name='files[]' value='".$row["idFile"]."' >".$row["fileName"].
												"<div id='list-download'> <a  href= './code/downloader.php?id=".$row["idFile"]."'>".$home["Download"]."</a>
												---<a href= './index.php?content=Archivo&id=".$row["idFile"]."'> Ver Archivo</a></div></li>";
												
			
											}
										}
										else 
										{
											echo "No existen archivos";
										}
										
										close_connection($con);
									?>
							</ul>
						</div>
						
						<div id="list-user-2">
						Archivos Privados
							<ul>
									<?php
										$con = connectDB();
										$id = mysql_real_escape_string($_SESSION['id']);
										
										$sql = "SELECT idFile,fileName 
												FROM fugacursos.Files 
												WHERE idUser = $id AND idFileType = 2";
										
										$result = mysql_query($sql);
										
										if(mysql_num_rows($result) > 0)
										{
											while($row = mysql_fetch_array($result))
											{
												
												echo "<li class= 'lista'><input type='checkbox' id='files[]' name='files[]' value='".$row["idFile"]."' >".$row["fileName"].
												"<div id='list-download'> <a  href= './code/downloader.php?id=".$row["idFile"]."'>".$home["Download"]."</a>
												---<a href= './index.php?content=Archivo&id=".$row["idFile"]."'> Ver Archivo</a></div></li>";
											}
										}
										else 
										{
											echo "No existen archivos";
										}
										
										close_connection($con);
									?>
							</ul>
							
						<div id="list-user-2">
							Archivos Compartidos
							<ul>
									<?php
										$con = connectDB();
										$id = mysql_real_escape_string($_SESSION['id']);
										
										$sql = "SELECT idFile,fileName 
												FROM fugacursos.Files 
												WHERE idFile IN (SELECT idFile FROM fugacursos.Sharing WHERE idUser = $id)
												OR (idUser = $id AND idFileType = 3)";
										
										$result = mysql_query($sql);
										
										if(mysql_num_rows($result) > 0)
										{
											while($row = mysql_fetch_array($result))
											{
												echo "<li class= 'lista'><input type='checkbox' id='files[]' name='files[]' value='".$row["idFile"]."' >".$row["fileName"].
												"<div id='list-download'> <a  href= './code/downloader.php?id=".$row["idFile"]."'>".$home["Download"]."</a>
												---<a href= './index.php?content=Archivo&id=".$row["idFile"]."'> Ver Archivo</a></div></li>";
											}
										}
										else 
										{
											echo "No existen archivos";
										}
										

										close_connection($con);
									?>
							</ul>
						</div>
					</form>
				</div>
			</div>

<?php 
}
?>
