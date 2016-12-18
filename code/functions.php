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

require_once 'dbSettings.php';

//-----------------------------------------------------------------
//   Librerias
//-----------------------------------------------------------------

/**
 * 
 * Enter description here ...
 */
function login( )
{
	return loginP( $_REQUEST["username"],$_REQUEST["password"]);
}

function loginP( $username, $password)
{
	$con = connectDB();

	$email = mysql_real_escape_string( $username );
	$password  = mysql_real_escape_string($password);
	//AND status='activated'
	$sql = "SELECT idUser FROM fugacursos.Users WHERE email = '". $email . "' AND password = '".$password. "' AND uid =''";	
	$result = mysql_query($sql);
	
	if(mysql_num_rows( $result) == 0)
	{
		mysql_close($con);
		return false;
	}
	else
	{
		$row = mysql_fetch_array($result);
		
		$_SESSION["id"] = $row["idUser"];
		$_SESSION["email"] = $email;
		mysql_close($con);
		return true;
	}

}

/**
 * 
 * Enter description here ...
 * @param unknown_type $uid
 */
function existeContactFacebook($uid)
{
	$con = connectDB();
	
	$sql = "SELECT idUser,email FROM fugacursos.Users WHERE uid = '$uid'";	
	$result = mysql_query($sql);
	
	if(mysql_num_rows( $result) == 0)
	{
		
		mysql_close($con);
		return false;
	}
	else
	{
		$row = mysql_fetch_array($result);
		$_SESSION['email'] = $row["email"];
		$_SESSION["id"] = $row["idUser"];
		mysql_close($con);
		return true;
	}
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $facebook
 */
function loginFacebook($facebook)
{	
		try
		{
		
			$uid = $facebook->getUser(); 
			$me = $facebook->api('/me'); 
			
			if(!existeContactFacebook($uid))
			{						
//				$jsonurl = "https://graph.facebook.com/$uid?access_token=".$facebook->getAccessToken();
//				$json = file_get_contents($jsonurl,0,null,null);
//				$json_output = json_decode($json);
				
				$name= $me['first_name'];
				$lastname = $me['last_name'];
				$username = $me['username'];;	
				$con = connectDB2();
				
				if(!$con->beginTransaction())
				{
					throw new Exception("Error in the transaction");
				}
	
				$sql = "INSERT INTO fugacursos.Users (email,password,name,lastname,activationKey,status,uid)
							Values ( ?,'',?,?,'','activated',?)";
	
				$stm = $con->prepare($sql);
				if(!$stm->execute(array($username,$name,$lastname,$uid)))
				{
					$con->rollBack();
					return "Problemas registrandote, el email puede ya estar registrado";
				}
				
				$idUser = $con->lastInsertId();
			
				if(mkdir("./u/".$idUser, 0777))
				{
					if(!$con->commit())
					{
						throw new Exception("Error finishing the transaction");
					}
				
				}
				else
				{
					$con->rollBack();
					return "Problemas registrandote, intenta de nuevo";
				}
						
			}

			
			$_SESSION["id"] = $uid;
			
		} 
		catch (FacebookApiException $e)
		{ 
			echo $e;
		} 
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $errno
 * @param unknown_type $errstr
 */
function customError($errno, $errstr)
{
  echo "<b>Error:</b> [$errno] $errstr<br />";
  echo "Ending Script";
  die();
}

/**
 * Conection normal a la base de datos 
 * @return: retorna 
 */
function connectDB()
{
	try 
	{
		$con = mysql_connect(HOST, USERNAME, PASSWORD);
	}
	catch(Exception $e)
	{
		throw new Exception($e->getMessage());
	}
		
	if (!$con)
	{
		customError(1, "Problem Conecting to the database");
	}

	mysql_select_db(DB, $con);
		
	return $con;
}

/**
 * 
 * Enter description here ...
 */
function connectDB2()
{
	try
	{
		return $con = new PDO("mysql:host=".HOST.";dbname=".DB, USERNAME, PASSWORD);
	}
	catch(PDOException $e)
	{
		
		throw new Exception($e->getMessage());
	}
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $con
 * @throws Exception
 */
function close_connection($con)
{
	if(!mysql_close($con))
		throw new Exception("Error Closing Conection");
}

/**
 * 
 * Enter description here ...
 */
function signup( )
{
	$firstname = $_REQUEST["firstname"];
	$lastname = $_REQUEST["lastname"];
	$email = $_REQUEST["email"];
	$password = $_REQUEST["password"];
	$activationKey =  mt_rand() . mt_rand() . mt_rand() . mt_rand() . mt_rand();
		
	$con = connectDB2();
	 
	if(!$con->beginTransaction())
	{
		throw new Exception("Error en la base datos");
	}

	$sql = "INSERT INTO fugacursos.Users (email,password,name,lastname,activationKey,status)
				Values ( ?,?,?,?,?,'verify')";

	$stm = $con->prepare($sql);
	if(!$stm->execute(array($email,$password,$firstname,$lastname,$activationKey)))
	{
		$con->rollBack();
		return "Problemas registrandote, el mail puede ya estar registrado";
	}

	$idUser = $con->lastInsertId();

	if(mkdir("./u/".$idUser, 0777))
	{
		if(!$con->commit())
		{
			throw new Exception("Error finishing the transaction");
		}
		//Mandar un Mail
		$to      = $email;
		$subject = " fugacursos.com Registro";
		$message = "Bienvenidos a fugacursos!\r\r. Puedes terminar de registrarte undiendo el siguiente link:\r
		http://www.fugacursos.com/index.php?content=SignUpConfirm&act=$activationKey \r\r".
		"Si no te sirve trata de copiar y pegar el link en tu browser \r\r  
		fugacursos.com \r\r";
		
		$headers = 'From: noreply@fugacursos.com' . "\r\n" .	
		    	   'Reply-To: noreply@fugacursos.com' . "\r\n" .
		    	   'X-Mailer: PHP/' . phpversion();
		
		mail($to, $subject, $message, $headers);
				
		return $firstname." ".$lastname. " mira tu email para activar la cuenta";
		
	}
	else
	{
		$con->rollBack();
		return "Problemas registrandote, intenta de nuevo";
	}
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $act
 */
function verifyActivation($act)
{
	$con = connectDB();
	 
	$sql = "SELECT idUser,name,activationkey FROM fugacursos.Users";
	$result = mysql_query($sql);
	
	while($row = mysql_fetch_array($result))
	{
		if ($act == $row["activationkey"])
		{
			$id = $row["idUser"];
			$sql="UPDATE fugacursos.Users SET activationKey = '', status='activated' WHERE (idUser = $id)";
		
			if (!mysql_query($sql))
			{
				die('Error: ' . mysql_error());
			}
			return  "Felicidades!" . $row["name"] . " ya eres parte de fugacursos.";
		}
  }
	
}



function upload($userN, $idFileN,$fileSize,$fileNameOrg,$fileName,$fileTempName,$lugar)
{
		//Las extenciones validas para subir archivos
		$known_mime_types=array(
				 	"pdf" => "application/pdf",
				 	"txt" => "text/plain",
					"doc" => "application/msword",
					"xls" => "application/vnd.ms-excel",
					"ppt" => "application/vnd.ms-powerpoint"
				 );
				 	
				 	
				 if ($fileName != "")
				 {
				 		$file_extension = strtolower(substr(strrchr($fileNameOrg,"."),1));

				 		//Vemos si la la extencion del archivo subido es valido
				 		if(array_key_exists($file_extension, $known_mime_types))
				 		{
				 			//Verificamos que es menor que el tamano establezido
				 			if($fileSize <= 500000000)
				 			{
				 				$mime_type=$known_mime_types[$file_extension];

				 				$destino=$lugar.$userN."/".$idFileN.".".$file_extension;
				 			
				 				if (is_uploaded_file($fileTempName))
				 				{
				 					move_uploaded_file ($fileTempName, $destino);
				 		
				 					$status = "Archivo subido: ".$fileName;
				 				}
				 				else
				 				{
				 					throw new Exception( "Archivo es invalido");
				 				}
				 			}
				 			else
				 			{
				 				throw new Exception( "El archivo es my grande");
				 			}
				 		}
				 		else
				 		{
				 			throw new Exception("El archivo es invalido");
				 		}
				 }
				 else
				 {
				 	throw new Exception("Hubo un problema inespareado subiendo el archivo");
				 }
				 	
				 return $status;
}

/**
 * 
 * Sube el archivo a la base de datos
 */
function uploadDB(  )
{
	$id = $_SESSION['id'];
	//$email = $_SESSION['email'];
	$fileName = $_POST["fileName"];
	$fileNameOrg = $_FILES["uploadFile"]['name'];
	$amount = $_POST["amount"];
	$fileType = $_POST["fileType"];
	
	$fileSize = $_FILES["uploadFile"]['size'];
	$fileTempName = $_FILES["uploadFile"]['tmp_name'];
	$lugar ="./u/";
	
	
	return  uploadDBP($id,$fileName,$fileNameOrg,$amount,$fileType,$fileSize,$fileTempName,$lugar);
}


function uploadDBP($id,$fileName,$fileNameOrg,$amount,$fileType,$fileSize,$fileTempName,$lugar)
{
		/*Se conecta con la base de datos*/
	$searcher = "";
	$con =connectDB2();
	
	if(!$con->beginTransaction())
	{
		throw new Exception("Error en la base datos");
	}
		
	/*Inserta el archivo en la tabla files */
	$sql = "INSERT INTO fugacursos.Files (fileName,filePath,searcher,idUser,idFileType)
			VALUES (' ', ' ', ' ', ?,?)";
	
	$stm = $con->prepare($sql);
	if(!$stm->execute(array($id,$fileType)))
	{
		$con->rollBack();
		throw new Exception("Error en la base datos");
	}
		
	$idFile = $con->lastInsertId();
		
	/*Actualizar el nombre del archivo */
	$file_extension = strtolower(substr(strrchr($fileNameOrg,"."),1));
	$filePath= $id."/".$idFile.".".$file_extension;
	$fileName = $fileName.".".$file_extension;
		
	$searcher = $searcher.$fileName;
	$i  = 0;
	
	// Se va por todas las categorias ingresadas y las anade a la base de datos
	while($i <= $amount)
	{
		$categoryDes= " ";
		
		$categoryDes = $_POST["categoryDes".$i];
		$categoryId = $_POST["categoryId".$i];
		$searcher = $searcher.$categoryDes;

		$sql2 = "INSERT INTO fugacursos.FileConnection (idFile,idCategory,description)
						VALUES (?,?,?)";

		$stm = $con->prepare($sql2);
		
		if(!$stm->execute(array($idFile,$categoryId,$categoryDes)))
		{
			
			$con->rollBack();
			throw new Exception("Error subiendo el archivo: No puede haber dos categorias con la misma informacion" );
		}
			
		$i++;
	}
		
	/* Se actualiza el searcher(el campo para la busqueda) y el nombre del archivo*/
	//$searcher = $searcher.$_SESSION["email"];
	$sql = "UPDATE fugacursos.Files SET fileName= ?, searcher= ?, filePath = ? WHERE idFile= ?";
		
	$stm = $con->prepare($sql);
	if(!$stm->execute(array($fileName,$searcher,$filePath,$idFile)))
	{
		$con->rollBack();
		throw new Exception("Error en la base datos");
	}
			
	$sql = "INSERT INTO fugacursos.AuditDownloader (idFile,downloads,dateCreated) VALUES (?,'0',(SELECT NOW()));";
	$stm = $con->prepare($sql);
	if(!$stm->execute(array($idFile)))
	{
		$con->rollBack();
		throw new Exception("Error en la base datos");
	}
		
	try
	{
		$status = upload($id, $idFile,$fileSize,$fileNameOrg,$fileName,$fileTempName,$lugar);
		if(!$con->commit())
		{
			throw new Exception("Error Terminando la transaccion");
		}
	}
	catch (Exception $e)
	{
		$con->rollBack();
		return "ERROR:".$e->getMessage();
	}
		
	return "El archivo ".$fileName." fue subido con exito";
}
/**
 * Eliminar un archivo
 * @throws Exception
 */
function delete()
{
	if(!isset($_GET['files'])  || empty($_GET['files']))
	{
		return "Selecciona un archivo";
	}
	else
	{
		$aDoor = $_GET['files'];
		$idUser = $_SESSION["id"];
		
		$N = count($aDoor);

		$con = connectDB2();

		if(!$con->beginTransaction())
		{
			throw new Exception("Error in the transaction");
		}
		 
		for($i=0; $i < $N; $i++)
		{
			
			$sql = "DELETE FROM fugacursos.Files WHERE idFile = ?";
			$sql2 = "SELECT filePath from fugacursos.Files WHERE idFile = ?";
			$sql3 = "SELECT COUNT('Existe')  AS Numero FROM fugacursos.Files WHERE idFile = ? AND idUser = ?";
			
			
			$stm = $con->prepare($sql3);
			if(!$stm->execute(array($aDoor[$i],$idUser)))
			{
				$con->rollBack();
				throw new Exception("Error Inesperado, Intente de nuevo");
			}
			
			$result = $stm->fetch(PDO::FETCH_ASSOC);
			
			if($result["Numero"] == 0)	
				return "Tienes que ser el propietario del archivo para borrarlo";
			
		
			$stm = $con->prepare($sql2);
			if(!$stm->execute(array($aDoor[$i])))
			{
				$con->rollBack();
				throw new Exception("Error en la base datos");
			}
			 
			$result = $stm->fetchAll();
			 
			$filename = "./u/". $result[0][0];
			
			if(file_exists($filename))
			{
				unlink($filename);
	
				$stm = $con->prepare($sql);
				if(!$stm->execute(array($aDoor[$i])))
				{
					$con->rollBack();
					throw new Exception("Error en la base datos");
				}
			}
			else 
			{
				$con->rollBack();
				throw new Exception("El archivo no existe");
			}
			
		}
		
		if(!$con->commit())
		{
			throw new Exception("Error finishing the transaction");
		}
	}

	return "Se borro el archivo";

}

/**
 * Encargado de compartir archivos con otros usuarios
 * @return: un String que muestra que el programa corrio satifactoriamente
 */
function share( )
{	
	if(!isset($_REQUEST['files'])  || empty($_REQUEST['files']))
	{
		return "Selecciona un archivo privado";
	}
	else
	{
		$listaSeleccionados = $_GET['files'];
		$email = $_REQUEST['email'];
		$idUserCompartir = "";
		$N = count($listaSeleccionados);

		$con = connectDB2();

		if(!$con->beginTransaction())
		{
			throw new Exception("Error en la base datos");
		}
		
		$sql = "SELECT idUser FROM fugacursos.Users WHERE TRIM(email) = TRIM('$email')";
			
		$stm = $con->prepare($sql);
		if(!$stm->execute())
		{
			$con->rollBack();
			throw new Exception("Error Inesperado, Intente de nuevo");
		}
		
		if(	$result = $stm->fetch(PDO::FETCH_ASSOC))
		{
			$idUserCompartir = $result['idUser'];
		}
		else 
			return "Escriba un email valido";
	
	
		for($i=0; $i < $N; $i++)
		{
			$sql = "INSERT INTO fugacursos.Sharing (idFile,idUser) VALUES (?,?)";
			
			$stm = $con->prepare($sql);
			if(!$stm->execute(array($listaSeleccionados[$i],$idUserCompartir)))
			{
				$con->rollBack();
				return "El archivo ya esta compartido con ese usuario";
			}
						
			$sql = "UPDATE fugacursos.Files 
					SET idFileType = 3
					WHERE idFile = ?";
					
			$stm = $con->prepare($sql);
			if(!$stm->execute(array($listaSeleccionados[$i])))
			{
				$con->rollBack();
				return "El archivo ya esta compartido con ese usuario";
			}
		}
		
		if(!$con->commit())
		{
			throw new Exception("Error finishing the transaction");
		}
	}

	return "Archivo compartido con $email";
		
	
}
?>