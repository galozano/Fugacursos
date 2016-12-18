<?php

/**
 * id: downloader.php v3.0 
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

require_once './functions.php';


//-----------------------------------------------------------------
//   Funciones
//-----------------------------------------------------------------

function download2()
{
	// local file that should be send to the client
	$local_file = 'test-file.zip';

	// filename that the user gets as default
	$download_file = 'your-download-name.zip';

	// set the download rate limit (=> 20,5 kb/s)
	$download_rate = 500;
	if(file_exists($local_file) && is_file($local_file))
	{
		// send headers
		header('Cache-control: private');
		header('Content-Type: application/octet-stream');
		header('Content-Length: '.filesize($local_file));
		header('Content-Disposition: filename='.$download_file);

		// flush content
		flush();
		// open file stream
		$file = fopen($local_file, "r");
		while(!feof($file))
		{
			// send the current file part to the browser
			print fread($file, round($download_rate * 1024));

			// flush the content to the browser
			flush();

			// sleep one second
			sleep(1);
		}

		// close file stream
		fclose($file);
	}
	else
	{
		die('Error: The file '.$local_file.' does not exist!');
	}
}


function output_file($file, $name )
{
	$mime_type='';

	if(!is_readable($file))
	{
		die('File not found or inaccessible!');
	}

	$size = filesize($file);
	$name = rawurldecode($name);

	$known_mime_types=array(
	 	"pdf" => "application/pdf",
	 	"txt" => "text/plain",
	 	"html" => "text/html",
	 	"htm" => "text/html",
		"zip" => "application/zip",
		"doc" => "application/msword",
		"xls" => "application/vnd.ms-excel",
		"ppt" => "application/vnd.ms-powerpoint",
		"gif" => "image/gif",
		"png" => "image/png",
		"jpeg"=> "image/jpg",
		"jpg" =>  "image/jpg",
		"php" => "text/plain"
	 );

	 if($mime_type=='')
	 {
		 $file_extension = strtolower(substr(strrchr($file,"."),1));
		 if(array_key_exists($file_extension, $known_mime_types))
		 {
		 	$mime_type=$known_mime_types[$file_extension];
		 }
		 else
		 {
		 	$mime_type="application/force-download";
		 }
	 }

	 // required for IE, otherwise Content-Disposition may be ignored
	 if(ini_get('zlib.output_compression'))
	 ini_set('zlib.output_compression', 'Off');

	 header('Content-Type: ' . $mime_type);
	 header('Content-Disposition: attachment; filename="'.$name.'"');
	 header("Content-Transfer-Encoding: binary");
	 header('Accept-Ranges: bytes');
	 header("Cache-control: private");

	 // multipart-download and download resuming support
	 if(isset($_SERVER['HTTP_RANGE']))
	 {
	 	list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
	 	list($range) = explode(",",$range,2);
	 	list($range, $range_end) = explode("-", $range);
	 	$range=intval($range);

	 	if(!$range_end)
	 	{
	 		$range_end=$size-1;
	 	}
	 	else
	 	{
	 		$range_end=intval($range_end);
	 	}

	 	$new_length = $range_end-$range+1;
	 	header("HTTP/1.1 206 Partial Content");
	 	header("Content-Length: $new_length");
	 	header("Content-Range: bytes $range-$range_end/$size");
	 }
	 else
	 {
	 	$new_length=$size;
	 	header("Content-Length: ".$size);
	 }

	 /* output the file itself */
	 $chunksize = 1*(1024*1024); //you may want to change this
	 $bytes_send = 0;
	 if ($file = fopen($file, 'r'))
	 {
	 	if(isset($_SERVER['HTTP_RANGE']))
			fseek($file, $range);

			while(!feof($file) && (!connection_aborted()) && ($bytes_send<$new_length))
			{
				$buffer = fread($file, $chunksize);
				print($buffer); //echo($buffer); // is also possible
				flush();
				$bytes_send += strlen($buffer);
			}

			fclose($file);
	 }
	 else
	 {
	 	die('Error - can not open file.');
	 }
	 die();

}

//-----------------------------------------------------------------
//   Funciones
//-----------------------------------------------------------------


	$idFile = $_GET["id"];
	$con = connectDB();
	
	$sql = "SELECT * FROM fugacursos.Files WHERE idFile = ". $idFile;
	
	$result = mysql_query($sql);
	
	if($row = mysql_fetch_array($result))
	{
		$file_path= $row["filePath"];
		$file_name = $row["fileName"];
	}

	$sql = "SELECT downloads FROM fugacursos.AuditDownloader WHERE idFile = ".$idFile;
	$result = mysql_query($sql);
	if($row = mysql_fetch_array($result))
	{
		$temp = $row['downloads'];
		$temp = $temp + 1;
	}
	
	$sql = "UPDATE fugacursos.AuditDownloader SET downloads =". $temp ." WHERE idFile = ".$idFile;
	mysql_query($sql);
	
	
	/*
	 Make sure script execution doesn't time out.
	 Set maximum execution time in seconds (0 means no limit).
	 */
	set_time_limit(0);
	$file_path="../u/".$file_path;

	output_file($file_path, $file_name);

?>