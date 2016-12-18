<?php
	$idFile = $_GET["id"];
	
	$url = "http://docs.google.com/viewer?url=http%3A%2F%2Ffugacursos.com%2Fcode%2Fdownloader.php%3Fid%3D".$idFile."&embedded=true";
	
?>
<a href="index.php">Atras</a>
<br/><br/>
<iframe src=<?php echo  $url;?> width="100%" height="750px" style="border: none;"></iframe>