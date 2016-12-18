<?php

require_once 'functions.php';

/*
 * jQuery File Upload Plugin PHP Example 4.2.4
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

   function post($id)
    {
  		$nameall = $_FILES["file"]["name"];
    	$name = substr($nameall,0,strrpos($nameall,"."));
    	$infoS = "";
     
    	try 
    	{
    		 $infoS = uploadDBP($id,$name,$_FILES["file"]["name"],-1,2, $_FILES["file"]['size'],$_FILES["file"]['tmp_name'],"../u/");
    	} 
    	catch (Exception $e) 
    	{
    		$info = '{"name":"'.$e.'","type":"image/jpeg","size":"123456789"}';
       	 	echo $info;
    	}
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        
        $info = '{"name":"'.$infoS.'","type":"image/jpeg","size":"123456789"}';
        echo $info;
       
    }

if(!isset($_REQUEST['userAdentro']))
{

	$username = $_REQUEST["usernameUpload"];
	$password = $_REQUEST["passwordUpload"];
	
	if(loginP($username,$password))
	{	   
		post($_SESSION["id"]);
	}
	else 
	{
		$info = '{"name":"Login Invalido","type":"image/jpeg","size":"123456789"}';
        echo $info;
	}
}   
else 
{
	post($_REQUEST['userAdentro']);
}








?>