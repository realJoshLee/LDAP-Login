<?php
error_reporting(0);

$ldap_baseDN = '';
  // This is the base DN that all of the users are in who are allowed to log in.
  // Example: OU=Staff,DC=domain,DC=local
  
$ldap_adminDN = '';
  // This is the base DN of the admin account used to perform lookups.
  // Example: CN=LDAP_Bind,OU=Service,DC=domain,DC=local

$ldap_adminPass = '';
  // Password for the admin account

$ldap_serverIP = '';
  // IP of your LDAP server
  // Example: 10.0.1.55

$ldap_domain = '';
  // LDAP Domain
  // Example: domain.local

?>
<!DOCTYPE html>
<html>

  <head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="text/html; charset=UTF-8; X-Content-Type-Options=nosniff" http-equiv="Content-Type" />

    <!--Scripts-->
    <link href="app/fa/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
  </head>
  <body>
    <div class="main">
      <div class="overlay-color">
        <div class="container">
          
          <div class="row">
            <div class="column left">
              <div class="left-content">
              </div>
            </div>
            
            <div class="column right">
              <div class="login-container">
                <div class="login-form">

                    <?php
                    if (isset($_POST["login"])) {
                      if (empty($_POST["username"]) || empty($_POST["password"])) {
                        // What happens if none or just one of the fields are entered.
                        echo '<script>alert("Both Fields are required")</script>';
                      } else {
                        $username = $_POST["username"];
                        $password = $_POST["password"];
                    
                    
                        //We just need six varaiables here
                        $baseDN = $ldap_baseDN;
                        $adminDN = $ldap_adminDN;//this is the admin distinguishedName
                        $adminPswd = $ldap_adminPass;
                        //$username = 'john.doe';//this is the user samaccountname
                        $userpass = $password;
                        $ldap_conn = ldap_connect("$ldap_serverIP");//I'm using LDAPS here
                    
                        if (! $ldap_conn) {
                          //echo ("<p style='color: red;'>Couldn't connect to LDAP service</p>");
                        }else{    
                          //echo ("<p style='color: green;'>Connection to LDAP service successful!</p>");
                        }
                        //The first step is to bind the administrator so that we can search user info
                        $ldapBindAdmin = ldap_bind($ldap_conn, $adminDN, $adminPswd);
                    
                        if ($ldapBindAdmin){
                            //echo ("<p style='color: green;'>Admin binding and authentication successful!!!</p>");
                            // userPrincipalName
                            // sAMAccountName
                            
                            // Uncomment this for the user to be able to just type in their username
                            $filter = '(sAMAccountName='.$username.')';

                            // Uncomment this for te user to be able to type in their full username user@domain.local
                            //$filter = '(userPrincipalName='.$username.')';

                            $attributes = array("givenName", "sn", "name", "telephonenumber", "mail", "samaccountname");
                            $result = ldap_search($ldap_conn, $baseDN, $filter, $attributes);
                    
                            $entries = ldap_get_entries($ldap_conn, $result);  
                            $userEmail = $entries[0]["mail"][0];  
                            $userDN = $entries[0]["name"][0];  
                            $userFN = $entries[0]["givenName"][0];  
                            $userLN = $entries[0]["sn"][0];  
                            //echo ('<p style="color:green;">I have the user DN: '.$userEmail.'</p>');
                    
                            //Okay, we're in! But now we need bind the user now that we have the user's DN
                            $ldapBindUser = ldap_bind($ldap_conn, $userDN, $userpass);
                    
                            if($ldapBindUser){
                                // What happens if the credentials were correct.
                                echo ("<p style='color: green;'>User authentication successful.</p>");

                                echo $userEmail;
                    
                                ldap_unbind($ldap_conn); // Clean up after ourselves.
                            } else {
                                echo ("<p style='color: red;'>Login info incorrect.</p>");   
                            }     
                    
                        } else {
                            echo ("<p style='color: red;'>Login info incorrect.</p>");   
                        } 
                    
                        
                      }
                    }
                    ?>

                    <h2 class="maintxt">Sign in</h2>

                    <br>

                    <form method="post">
                      
                      <div class="input-section">
                        <label for="username" class="form-label">Email</label><br>
                        <input type="text" name="username" required class="form-control-new"/>
                      </div>              
                      
                      <br><br>
                      
                      <div class="input-section">
                        <label for="password" class="form-label">Password</label><br>
                        <input type="password" name="password" required class="form-control-new"/>
                      </div>

                      <br>

                      <div class="align-right-container">
                        <div class="align-right">
                          <input type="submit" name="login" value="Login" class="btn btn-info" />
                        </div>
                      </div>
                    </form>

                    <br><br><br><br>

                    <p>Please login with your Active Directory/LDAP account provided to you by your admin.</p>
                    <p>If you forgot your password, please contact your domain admin.</p>
                    <p>Accounts managed by: <?php echo $ldap_domain; ?></p>

                </div>
              </div>
            </div>
          </div>

          <div class="notice">
            <p><i class="fab fa-unsplash"></i>&nbsp;&nbsp;Backgrounds from Unsplash</p>
          </div>

        </div>
      </div>
    </div>
  </body>
</html>
<style>
html, body {
  color: #111;
  font-family: 'Montserrat', Arial, Helvetica, sans-serif;
  height: 100%;

  background-color: grey;

  padding: 0;
  margin: 0;
}

div.main {
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  background-color: #f5f5f5;
  background-image: url('https://source.unsplash.com/1920x1080/?nature,mountain');
  height: 100%;
}

div.overlay-color {
  background: rgba(17, 17, 17, 0.70);
  height: 100%;
  z-index: 2;
}

div.container {
  padding-top: 100px;
  padding-bottom: 100px;
}

* {
  box-sizing: border-box;
}

.column {
  float: left;
}

.left {
  width: 60%;
}

.right {
  width: 40%;
}

.row:after {
  content: "";
  display: table;
  clear: both;
}

img.panel-logo {
  display: none;
}

.notice {
  position: fixed;
  bottom: 0;
  padding-left: 20px;
  color: #fff;
}









/*Left*/
div.left {
  text-align: center;
  padding-top: 100px;
}

div.left img.logo {
  width: auto;
  height: 50px;
}









/*Right*/
div.right div.login-container {
  background: #fff;
  height: 650px;
  font-size: 16px;
  width: 470px;

  padding-top: 20px;
  padding-bottom: 50px;
  padding-left: 50px;
  padding-right: 50px;
  border-radius: 5px;
}

div.right div.login-container h2.maintxt {
  font-size: 30px;
  color: #333;
}

div.right div.login-container label {
  font-size: 12px;
}

div.right div.login-container .form-control-new {
  /*Remove all of the background shadows*/
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  outline: none;
  
  background: none;
  color: #111;
  outline: none;
  border: transparent;
  border-bottom: 1px solid grey;
  border-radius: 0px;
  width: 100%;
  font-size: 16px;
}

div.right div.login-container .form-control-new:focus {
  border-bottom: 2px solid #5460fe;
  transition-duration: 0.1s;
}

div.right div.login-container div.align-right-container {
  float: right;
}

div.right div.login-container div.align-right-container input.btn {
  /*Remove all of the background shadows*/
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  outline: none;
  
  background: #5460fe;
  color: #fff;
  font-size: 16px;
  outline: none;
  border: transparent;

  padding: 5px;
  width: 100px;
  border-radius: 50px;
}

div.right div.login-container div.align-right-container input.btn:hover {
  cursor: pointer;
  opacity: 0.8;
  transition-duration: 0.1s;
}

a.link {
  color: #5460fe;
  text-decoration: none;
}









/*Responsive*/
@media screen and (max-width: 1464px) {
  div.left-content {
    display: none;
  }
  
  div.left {
    width: 0px;
    display: none;
  }

  div.column {
    float: center;
  }

  div.right {
    width: 100%;
  }

  div.right div.login-container {
    margin: auto;
  }

  img.panel-logo {
    display: initial;
    height: 20px;
    width: auto;
  }
}

@media screen and (max-width: 1188px) {
  div.left {
    width: 0px;
    display: none;
  }

  div.column {
    float: center;
  }

  div.right {
    width: 100%;
  }

  div.right div.login-container {
    margin: auto;
  }

  img.panel-logo {
    display: initial;
    height: 20px;
    width: auto;
  }
}

@media screen and (max-width: 470px) {
  div.overlay-color {
    background: rgba(255, 255, 255, 1);
  }

  div.main {
    background-image: none;
  }

  div.right div.login-container {
    width: 100%;
    height: auto;
  }

  .notice {
    display: none;
  }

  div.container {
    padding-top: 20px;
    padding-bottom: 20px;
  }
}

@media screen and (max-width: 430px) {
  div.right div.login-container {
    padding-left: 20px;
    padding-right: 20px;
  }
}
</style>