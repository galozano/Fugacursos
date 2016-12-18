

<script type="text/javascript">

$(document).ready(
		function ()
		{
			$('#send').click(function () 
			{
				$("#firstnameError").html("");
				
					if(document.getElementById("firstname").value == "")
					{
						$("#firstnameError").html("Requerido");
					}
					else if(document.getElementById("lastname").value == "")
					{
						$("#lastnameError").html("Requerido");
					}
					else if(document.getElementById("email").value == "" || document.getElementById("email").value.indexOf("@",0) == -1)
					{
						$("#emailError").html("Email Invalido");
					}
					else if($("#password").val() == "" || $("#passwordConfirm").val() == "")
					{
						$("#passwordError").html("No pueden estar vacias");
					}
					else if(document.getElementById("password").value != document.getElementById("passwordConfirm").value)
					{
						$("#passwordError").html("Constrase–a no son iguales");
					}
					else
					{
						document.forms["formSignUp"].submit();
					}
			    });
		});

</script>
		<div id="page-top">	
				<div id="content-complet">
				<a href="index.php?content=Home"><?php echo $upload["GoBack"];?></a>
					<div id="forms">
					
						<p><?php echo $signUp["SignUpTo"];?></p>
						<form id="formSignUp" action="index.php?content=SignUpConfirm&form_submitted=1" method="POST" enctype="multipart/form-data">
							<table border=0 cellpadding=1 cellspacing=2 align="center">
								<tr>
									<td><?php echo $signUp["FirstName"];?><span style="color:red">*</span></td>
									<td><input type="text" name="firstname" id="firstname" maxlength="40" ></td>
									<td><div id="firstnameError"></div></td>
								</tr>
									
								<tr>
									<td><?php echo $signUp["LastName"];?><span style="color:red">*</span></td>
									<td><input type="text" name="lastname" id="lastname" maxlength="40"></td>
									<td><div id="lastnameError"></div></td>
								</tr>
							
								<tr>
									<td><?php echo $signUp["Email"];?><span style="color:red">*</span></td>
									<td><input type="text" name="email" id="email" maxlength="40"></td>
									<td><div id="emailError"></div></td>
								</tr>
								
								
								<tr>
									<td><?php echo $signUp["Password"];?><span style="color:red">*</span></td>
									<td><input type="password" name="password" id="password" maxlength="40"></td>
									<td><div id="passwordError"></div></td>
								</tr>
								
								<tr>
									<td><?php echo $signUp["Confirm"];?><span style="color:red">*</span></td>
									<td><input type="password" name="passwordConfirm" id="passwordConfirm" maxlength="40"></td>
									<td><div id="passwordConfirmError"></div></td>
								</tr>
							
							</table>
						</form>
						<div align="right">
							<input name="send" id="send" type="button" value=<?php echo $signUp["SignUp"];?> />
						</div>
					</div>
				</div>
			</div>

