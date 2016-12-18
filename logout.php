<?php
	
		$_REQUEST["username"] = null;
		session_unset();
		session_destroy();
		
		header("Location: index.php" );

?>
	