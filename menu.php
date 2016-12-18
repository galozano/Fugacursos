<?php 

/* ------------------------------------------------------------
 * Page:Menu
 * Author: Gustavo Lozano
 * Description: Manages the menu of all the webpage by an array
 * 
 **------------------------------------------------------------*/

?>

<?php

/**
 * 
 * Enter description here ...
 * @var unknown_type
 */
$menu_options = array (
     "Home" => array("Home","home.php",true),
     "Searcher" => array("","searcher.php",false),
     "SignUp" => array("Sign Up","signUp.php",true),
     "SignUpConfirm" => array("","signUpConfirm.php",true),
     "Upload" => array("","upload.php",true),
	 "User" => array("","user.php",false),
 	 "Archivo" => array("","verArchivo.php",false),
 	 "Logout" => array("","logout.php",false)
);

/**
 * 
 * Enter description here ...
 */
function display_menu()
{
    global $menu_options;
    foreach ($menu_options as $key => $value)
    {
       if ($value[2])
          echo "<li><a href=\"index.php?content=$key\">".$value[0]."</a></li>";
    }
}

?>
