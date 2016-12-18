		<div id="page-top">	
			<div id="content-complet">
			
<?php 

if (isset($_REQUEST['form_submitted']) && $_REQUEST['form_submitted'] == '1')
{
	echo signup();
} 
else if(isset($_REQUEST['act']))
{
	$activationKey = $_REQUEST['act'];
	echo verifyActivation($activationKey);
}

?>

				
				<a href="index.php?content=Home"><?php echo $signUp["GoBack"];?></a>
			</div>
		</div>

	
