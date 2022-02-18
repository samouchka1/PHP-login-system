<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!--Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="font-bold text-lg text-center mt-6">Login<br/>
        <span class="font-normal text-base">Please fill in your credentials to login.</span>
    </div>

    <div class="w-full md:w-3/5 lg:w-2/5 border-solid border-2 text-left mx-auto my-10">

        <?php if(!empty($login_err)){ echo '<div class="font-bold text-base text-center mt-6">' . $login_err . '</div>';}?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="text-left flex flex-col gap-3 pl-20">
                <label>Username</label>
                    <input type="text" name="username" class="w-3/4 mx-4  border-solid border-2" value="<?php echo $username; ?>">
                        <span class="text-sm ml-5 text-red-500"><?php echo $username_err; ?></span>

                <label>Password</label>
                    <input type="password" name="password" class="w-3/4 mx-4  border-solid border-2" value="<?php echo $password; ?>">
                        <span class="text-sm ml-5 text-red-500"><?php echo $password_err; ?></span>
                <div class="flex mx-4 mt-2 gap-3">
                    <input type="submit" class="border-solid border-2 border-neutral-900 w-20 mr-2 bg-gray-300 hover:bg-neutral-100" value="Login">
                    <span class="font-normal text-base">Don't have an account? <a href="register.php" class="text-blue-500 no-underline hover:underline">Sign up now</a>.</span>
                </div>
        </form>
    </div>
</body>
</html>