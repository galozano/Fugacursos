	<div id="page-top">				
				<div id="login-all">
				<table width="100%">
				<tr>
				<td>
					<div id="login">
							<form name="formEntrar" action="index.php" method="post">
								<input type="hidden" name="content" value="Home"/> 
								<table align="center" cellpadding="5" cellspacing="5">
									<tr><td><?php echo $home["User"];?>: </td><td><input id="textUsername" name="username" type="text" size="30" onchange="cambiaPonerValor()" maxlength="45"/></td></tr>
									<tr><td><?php echo $home["Password"];?>:</td><td> <input id="textPassword" name="password" type="password"  size="30" maxlength="45" onchange="cambiaPonerValor()"/></td></tr>
									<tr>
										<td align="center"><a href="index.php?content=SignUp" class="signup"><?php echo $home["Create"];?></a></td>
										<td align="right"><input type="submit" id="submit" value="Login" /></td>
									</tr>
								</table>
								</form>
							<br>
							<div align="center">
							
								 <div class="fb-login-button">Login with Facebook</div>
							
							</div>
					</div>	
					
				</td>
				<td>
					<div id="dragndrop">
							<div align="center">
								<form id="file_upload" action="./code/uplodInstantaneo.php" method="POST" enctype="multipart/form-data">
									<input id="usernameUpload" name="usernameUpload" type="hidden" value="">
									<input id="passwordUpload" name="passwordUpload" type="hidden" value="">
								    <input type="file" name="file">
								    <button>Upload</button>
								    <div class="file_upload_label" align="center">Drag n Drop 
								    <br>
								   
								    </div>
								</form>
								<table id="files"></table>
							</div>	
					</div>
				</td>	
				</tr>
				</table>
				</div>
		