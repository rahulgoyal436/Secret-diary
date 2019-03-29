<?php 
	session_start();
	$error="";
	if(array_key_exists("logout",$_GET)) {
    	unset($_SESSION);
      	setcookie("id","",time()-60*60);
      	$_COOKIE["id"]="";
      	session_destroy();
    }
	else if((array_key_exists("id",$_SESSION) and $_SESSION['id']) or (array_key_exists("id",$_COOKIE) and $_COOKIE['id'])) {
    	header("location:loggedinpage.php");
    }
	if(array_key_exists("submit",$_POST)) {
      	include("connection.php");
      	if(!$_POST['email']) {
        	$error.="An email address is required.<br>";
        }
      	if(!$_POST['password']) {
        	$error.="A password is required.<br>";
        }
      	if($error!="") {
        	echo "";
       } 
      	else {
          	if($_POST['signup']=='1') {
                $query="select id from `users` where email='".mysqli_real_escape_string($link,$_POST['email'])."' limit 1";
                $result=mysqli_query($link,$query);
                if(mysqli_num_rows($result)>0) {
                    $error="That email is already taken.";
                }
                else {
                    $query="insert into `users`(`email`,`password`) values('".mysqli_real_escape_string($link,$_POST['email'])."','".mysqli_real_escape_string($link,$_POST['password'])."')";
                    if(!mysqli_query($link,$query)) {
                        $error="couldn't sign up please try again.";
                    }
                    else {
                        $query="update `users` set password='".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' where id=".mysqli_insert_id($link)." limit 1";
                        mysqli_query($link,$query);
                        $_SESSION['id']=mysqli_insert_id($link);
                        if(isset($_POST['stayloggedin'])) {
                            setcookie("id",mysqli_insert_id($link),time()+60*60);
                        }
                        header("location:loggedinpage.php");
                    }
                }
            }
          	else {
            	$query="select * from `users` where email='".mysqli_real_escape_string($link,$_POST['email'])."'";
              	$result=mysqli_query($link,$query);
              	$row=mysqli_fetch_array($result);
              	if(isset($row)) {
                	$hashedpassword=md5(md5($row['id']).$_POST['password']);
                  	if($hashedpassword==$row['password']) {
                    	$_SESSION['id']=$row['id'];
                      	if(isset($_POST['stayloggedin']) and $_POST['stayloggedin']=='1') {
                        	setcookie("id",$row['id'],time()+60*60);
                        }
                        header("location:loggedinpage.php");
                    }
                  	else {
                    $error="That combination of email/password is incorrect.";
                    }
                }
              	else {
                	$error="That combination of email/password is incorrect.";
                }
            }
        }
    }
?>
<?php include("header.php"); ?>
	<div class="container" id="homepagecontainer">
    	<h1>Secret Diary</h1>
      	<p><strong>Store your thoughts permanently and securely.</strong></p>
        <div id="error"><?php if($error!="") {
			echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
		} ?></div>
        <form method="POST" id="signupform">
          <p>Interested? Sign up now.</p>
          <div class="form-group">
            <input class="form-control" type="email" name="email" placeholder="Your Email">
          </div>
          <div class="form-group">
            <input class="form-control" type="password" name="password" placeholder="Your Password">
          </div>
          <div class="form-group">
            <input type="checkbox"  name="Stayloggedin" value=1>
            <label class="form-check-label">Stay logged in</label>
          </div>
          <div class="form-group">
            <input type="hidden" name="signup" value="1" >
            <input class="btn btn-success" type="submit" name="submit" value="signup!">
          </div>
          <p><a class="toggleform">Log in</a></p>
        </form>
        <form method="POST" id="loginform">
          <p>Login using your username and password.</p>
          <div class="form-group">
            <input class="form-control" type="email" name="email" placeholder="Your Email">
           </div>
          <div class="form-group">
            <input class="form-control" type="password" name="password" placeholder="Your Password">
          </div>
          <div class="form-group">
            <input type="checkbox" name="Stayloggedin" value=1>
            <label class="form-check-label">Stay logged in</label>
          </div>
          <div class="form-group">
            <input type="hidden" name="signup" value="0">
            <input class="btn btn-success" type="submit" name="submit" value="login!">
          </div>
          <p><a class="toggleform">Sign up</a></p>
        </form>
	</div>
<?php include("footer.php"); ?>  