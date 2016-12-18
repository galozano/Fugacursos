<?php
include_once("User.php");
class User
{
	private $firstname;
	private $email;
	private $lastname;
	
	private $files;
	
	
}

class Main
{

	
	public function connectDB()
	{
		/**$con = mysql_connect("fugacursos.db.7389753.hostedresource.com", "fugacursos", "Gusti1989");*/
		$con = mysql_connect("127.0.0.1", "todos", "todos");
	
		if (!$con)
		{
			throw new Exception("Could not connect: " );
		}
	
		mysql_select_db("fugacursos", $con);
			
		return $con;
	}
	
	public function connectDB2()
	{
		try
		{
			return $con = new PDO("mysql:host=fugacursos.db.7389753.hostedresource.com;dbname=fugacursos", "fugacursos", "Gusti1989");
		}
		catch(PDOException $e)
		{
			throw new Exception($e->getMessage());
		}
	}
	
	public function close_connection($con)
	{
		if(!mysql_close($con))
		throw new Exception("Error Closing Conection");
	}
	
	public function signup( )
	{
		$firstname = $_POST["firstname"];
		$lastname = $_POST["lastname"];
		$email = $_POST["email"];
		$password = $_POST["password"];
	
		$con = connectDB2();
		 
		if(!$con->beginTransaction())
		{
			die("Error in the transaction");
		}
	
		$sql = "INSERT INTO fugacursos.Users (email,password,name,lastname)
					Values ( ?,?,?,?)";
	
		$stm = $con->prepare($sql);
		if(!$stm->execute(array($email,$password,$firstname,$lastname)))
		{
			$con->rollBack();
			return "There where problems signing up, the email maybe already registered";
		}
	
		$idUser = $con->lastInsertId();
	
		if(mkdir("./u/".$idUser, 0777))
		{
			if(!$con->commit())
			{
				die("Error finishing the transaction");
			}
				
			return $firstname." ".$lastname. " sign up successfully";
		}
		else
		{
			$con->rollBack();
			return "There where problems signing up, try again";
		}
	}
	
	public function upload($userN, $idFileN)
	{
		$fileSize = $_FILES["uploadFile"]['size'];
		$fileType = $_FILES["uploadFile"]['type'];
		$fileNameOrg = $_FILES["uploadFile"]['name'];
		$fileName = $_POST["fileName"];
			
		$known_mime_types=array(
					 	"pdf" => "application/pdf",
					 	"txt" => "text/plain",
					 	"html" => "text/html",
					 	"htm" => "text/html",
						"zip" => "application/zip",
						"doc" => "application/msword",
						"docx" => "application/vnd.openxmlformats",
						"xls" => "application/vnd.ms-excel",
						"xlsx" => "application/vnd.openxmlformats",
						"ppt" => "application/vnd.ms-powerpoint",
						"gif" => "image/gif",
						"png" => "image/png",
						"jpeg"=> "image/jpg",
						"jpg" =>  "image/jpg",
						"php" => "text/plain"
					 );
					 	
					 	
					 if ($fileName != "")
					 {
					 	if($mime_type=='')
					 	{
					 		$file_extension = strtolower(substr(strrchr($fileNameOrg,"."),1));
	
					 		if(array_key_exists($file_extension, $known_mime_types))
					 		{
					 			if($fileSize <= 500000000)
					 			{
					 				$mime_type=$known_mime_types[$file_extension];
	
					 				$destino="./u/".$userN."/".$idFileN.".".$file_extension;
					 					
					 				if (copy($_FILES["uploadFile"]['tmp_name'], $destino))
					 				{
					 					$status = "Archivo subido: ".$fileName;
					 				}
					 				else
					 				{
					 					throw new Exception( "There was an unexpected error uploading the file");
					 				}
					 			}
					 			else
					 			{
					 				throw new Exception( "The file size is too big");
					 			}
					 		}
					 		else
					 		{
					 			throw new Exception("The file extension is invalid");
					 		}
					 	}
					 }
					 else
					 {
					 	throw new Exception("There was an unexpected error uploading the file");
					 }
					 	
					 return $status;
	}
	
	public function uploadDB( )
	{
		$id = $_SESSION['id'];
		$email = $_SESSION['email'];
		$fileName = $_POST["fileName"];
		$fileNameOrg = $_FILES["uploadFile"]['name'];
		$amount = $_POST["amount"];
		$fileType = 1;
		$searcher = "";
			
		/*Se conecta con la base de datos*/
		$con =connectDB2();
	
		if(!$con->beginTransaction())
		{
			die("Error in the transaction");
		}
			
		/*Inserta el archivo en la tabla files */
		$sql = "INSERT INTO fugacursos.Files (fileName,filePath,searcher,idUser,idFileType)
						VALUES (' ', ' ', ' ', ?,?)";
			
		$stm = $con->prepare($sql);
		if(!$stm->execute(array($id,$fileType)))
		{
			$con->rollBack();
			die("Error in database 1");
		}
			
		$idFile = $con->lastInsertId();
			
		/*Actualizar el nombre del archivo */
		$file_extension = strtolower(substr(strrchr($fileNameOrg,"."),1));
		$filePath= $id."/".$idFile.".".$file_extension;
		$fileName = $fileName.".".$file_extension;
			
		$searcher = $searcher.$fileName;
			
		$i  = 0;
		while($i <= $amount)
		{
			$categoryDes = $_POST["categoryDes".$i];
			$categoryId = $_POST["categoryId".$i];
			$searcher = $searcher.$categoryDes;
	
			$sql2 = "INSERT INTO fugacursos.FileConnection (idFile,idCategory,description)
							VALUES (?,?,?)";
	
			$stm = $con->prepare($sql2);
			if(!$stm->execute(array($idFile,$categoryId,$categoryDes)))
			{
				$con->rollBack();
				die("Error in database 2");
			}
				
			$i++;
		}
			
		/* Se actualiza el searcher y en nombre del archivo*/
		$searcher = $searcher.$_SESSION["email"];
		$sql = "UPDATE fugacursos.Files SET fileName= ?, searcher= ?, filePath = ? WHERE idFile= ?";
			
		$stm = $con->prepare($sql);
		if(!$stm->execute(array($fileName,$searcher,$filePath,$idFile)))
		{
			$con->rollBack();
			die("Error in database 3");
		}
			
			
		$sql = "INSERT INTO fugacursos.AuditDownloader (idFile,downloads,dateCreated) VALUES (?,'0',(SELECT NOW()));";
		$stm = $con->prepare($sql);
		if(!$stm->execute(array($idFile)))
		{
			$con->rollBack();
			die("Error in database 4");
		}
			
			
		try
		{
			$status = upload($id, $idFile);
			if(!$con->commit())
			{
				die("Error finishing the transaction");
			}
		}
		catch (Exception $e)
		{
			$con->rollBack();
			return "ERROR:".$e->getMessage();
		}
			
		return "File ".$fileName." was successfully uploaded";
	}
	
	public function delete()
	{
		$aDoor = $_GET['files'];
	
		if(empty($aDoor))
		{
			return "You didn't select any files.";
		}
		else
		{
			$N = count($aDoor);
	
			$con = connectDB2();
	
			if(!$con->beginTransaction())
			{
				die("Error in the transaction");
			}
			 
			for($i=0; $i < $N; $i++)
			{
				$sql = "DELETE FROM fugacursos.Files WHERE idFile = ?";
				$sql2 = "SELECT filePath from fugacursos.Files WHERE idFile = ?";
	
				$stm = $con->prepare($sql2);
				if(!$stm->execute(array($aDoor[$i])))
				{
					$con->rollBack();
					die("Error in database");
				}
				 
				$result = $stm->fetchAll();
				 
				$filename = "../u/". $result[0][0];
				unlink($filename);
	
	
				$stm = $con->prepare($sql);
				if(!$stm->execute(array($aDoor[$i])))
				{
					$con->rollBack();
					die("Error in database");
				}
				 
			}
			
			if(!$con->commit())
			{
				die("Error finishing the transaction");
			}
		}
	
	
	
		return "Upload successfully";
	
	}
	
	public function  checkEmail($email) 
	{
		 if (!preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9\._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9\._-] +)+$/" , $email)) 
		 {
		  	return false;
		 }
		 
		 return true;
	}
	
}


?>