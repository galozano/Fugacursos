<?php
/**
 * FugaCursos
 * id: Functions.php v 3
 * 
 * Nombre:Gustavo Lozano
 * Fecha Creada:
 * Ultima Modificacion: Mayo 6 2011
 * Description: Contiene todas las funciciones necesarias
 */

	//Iniciamos la session
	session_start();
	
//-----------------------------------------------------------------
//   Librerias
//-----------------------------------------------------------------
	
	include "menu.php";
	require_once './languages/esp.php';
	require_once './code/functions.php';
	require_once './code/facebook.php';
	
	
//-----------------------------------------------------------------
//   Variables
//-----------------------------------------------------------------	

	//Declaramos vacio la option escogida actual para el contenido
	$current_option = "";
		

//-----------------------------------------------------------------
//   Declaraciones
//-----------------------------------------------------------------	
	
	//Verificamos si existe un objecto de facebook creados o no, si existe simplemente lo sacamos de la session
	if(!isset($_SESSION["facebook"]))
	{	
		$facebook = new Facebook(array(
  		'appId'  => APPID,
  		'secret' => APPSECRET,
		));
		
		$_SESSION["facebook"] = serialize($facebook);
	}
	else 
	{
	
		$facebook = unserialize($_SESSION["facebook"]);	
	}
		
	// Get User ID
	$userFacebook = $facebook->getUser();
	
	//Se mira si existe la session y se manda o no 
	if ($userFacebook) 
	{
		loginFacebook($facebook);
	}
	else
	{
   		$userFacebook= null;
	}
			
	//Si existe una session mandamos al usurio a la pagina user.php
	if (isset($_SESSION["id"]))
	{		
		$current_option="User";
	}	
	
	//Si mandaron el contenido se pone el contenido que se quiere usar
	if (isset($_REQUEST['content']))
	{
	   $current_option=$_REQUEST['content'];
	}
	else if($current_option == "")
		$current_option="Home";
		

	if(isset($_REQUEST['username']) && isset($_REQUEST['password']))
	{	
		if(login( ))
			$current_option="User";
		else 
			$current_option="Home";
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>

<?php 

	
			echo  "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/config.css\" />";
		
		
?>
			<!--[if IE]>
					<link rel="stylesheet" type="text/css" href="./css/iespecificonfig.css" />
			<![endif]-->


	<meta name="Description" content="Pagina para compartir archivos con otros usuarios con facilidad"/>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>

	<link rel="shortcut icon" href="favicon.ico"/>
	<link rel="stylesheet" href="css/jquery-ui-1.8.12.custom.css">
	<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js" type="text/javascript"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/jquery-ui.min.js" type="text/javascript"></script>
	<script src="lib/application.js"></script>
</head>

<body>
<?php
//Se incluye la libreria de google analytics para poder llevar analisis de a pagina 
include_once("lib/googleanalytics.php"); 
?>

 <div id="fb-root"></div>
      <script>
        window.fbAsyncInit = function() {
          FB.init({
            appId      : '194879367202615',
            status     : true, 
            cookie     : true,
            xfbml      : true,
            oauth      : true,
          });

          FB.Event.subscribe('auth.login', function(response) {
              window.location.reload();
            });
        };
        (function(d){
           var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
           js = d.createElement('script'); js.id = id; js.async = true;
           js.src = "//connect.facebook.net/en_US/all.js";
           d.getElementsByTagName('head')[0].appendChild(js);
         }(document));
      </script>

	<div id="wrapper">
		<div id="header-wrapper">
			<div id="header">
					<div id="sidebar-search">
						<div id="search">
							<form action="index.php" method="get" enctype="multipart/form-data">
								<input type="text" name="q" id="search-text" value="" /> 
								<input type="hidden" name="content" value="Searcher"/>
								<input type="submit" id="submit" value=<?php echo $home["Search"];?> />
							</form>
						</div>
					</div>
			<?php 		
						
					if($current_option != "Home" && isset($_SESSION["id"]))
					{
						if($userFacebook)
						{
					 		$logoutUrl = $facebook->getLogoutUrl( );
						}
						else 
						{
					 	 	$logoutUrl ="./index.php?content=Logout";
						}
					
			?>
								<div id="logout">
									<a href=<?php echo $logoutUrl; ?>><?php echo $logo["logout"];?></a>
								</div>
			<?php 			
					}
			?>		
			</div>
		</div>
		<div id="page">
				<div style="float:right;">
					<iframe src="http://www.facebook.com/plugins/like.php?href=www.fugacursos.com&amp;send=false&amp;layout=standard&amp;width=500&amp;show_faces=true&amp;action=like&amp;colorscheme=light&amp;font=lucida+grande&amp;height=80" 
					scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:60px;" allowTransparency="true"></iframe>
				</div>
				<div id="logo">
					<h1><a href="index.php"><span>fuga</span>cursos</a></h1>
					<p><?php echo $logo["logoName"];?></p>
				</div>
		<?php 
				include $menu_options[$current_option][1]; 
		?>
		</div>


	</div>

	<div id="footer">
		<?php  if($current_option == "Searcher") echo "<p>$_pagi_navegacion</p>";?>
		<p align="center">@Copyright fugacursos</p>
	</div>	
	<script src="lib/jquery.fileupload.js"></script>
	<script src="lib/jquery.fileupload-ui.js"></script>
</body>
</html>