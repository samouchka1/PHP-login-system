<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";

        //FILTER VAR
    } elseif(!filter_var($_POST["username"], FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9_]+$/")))){
                                                                    //regexp should be in an options array
        //PREG MATCH
    // } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){

        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // PASSWORD HASH <<<<<<<<<<<<
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
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
    <title>Sign Up</title>
    <!--Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body>
    <div class="font-bold text-lg text-center mt-6">Sign up<br/>
        <span class="font-normal text-base">Please fill this form to create an account.</span>
    </div>
    <div class="w-full md:w-3/5 lg:w-2/5 border-solid border-2 text-left mx-auto my-10 bg-stone-100">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="text-left flex flex-col gap-3 pl-20">
                <label>Username</label>
                    <input type="text" name="username" class="w-3/4 mx-4  border-solid border-2" value="<?php echo $username; ?>">
                        <span class="text-sm ml-5 text-red-500"><?php echo $username_err; ?></span> 
                <label>Password</label>
                    <input type="password" name="password" class="w-3/4 mx-4  border-solid border-2" value="<?php echo $password; ?>">
                        <span class="text-sm ml-5 text-red-500"><?php echo $password_err; ?></span>
                <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="w-3/4 mx-4  border-solid border-2" value="<?php echo $confirm_password; ?>">
                        <span class="text-sm ml-5 text-red-500"><?php echo $confirm_password_err; ?></span>

            <div class="flex mx-4 mt-2 gap-3">
                <input type="submit" class="border-solid border-2 border-neutral-900 w-20 mr-2 bg-gray-300 hover:bg-neutral-100" value="Submit">
                <input type="reset" class="border-solid border-2 border-neutral-900 w-20 mr-2 bg-gray-300 hover:bg-neutral-100" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php" class="text-blue-500 no-underline hover:underline">Login here</a>.</p>
        </form>
    </div>

</body>
</html>