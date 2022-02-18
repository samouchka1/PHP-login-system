<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
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
    <title>Reset Password</title>

    <!--Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
        <div class="font-bold text-lg text-center mt-6">Reset Password<br/>
            <span class="font-normal text-base">Please fill out this form to reset your password.</span>
        </div>
        <div class="w-full md:w-3/5 lg:w-2/5 border-solid border-2 text-left mx-auto my-10">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="text-left flex flex-col gap-3 pl-20"> 
                    <label>New Password</label>
                        <input type="password" name="new_password" value="<?php echo $new_password; ?>" class="w-3/4 mx-4  border-solid border-2">
                            <span class="text-sm ml-5 text-red-500"><?php echo $new_password_err; ?></span>
                    <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="w-3/4 mx-4  border-solid border-2">
                            <span class="text-sm ml-5 text-red-500"><?php echo $confirm_password_err; ?></span>
                <div class="flex mx-4 mt-2 gap-3">
                    <input type="submit" value="Submit" class="border-solid border-2 border-neutral-900 w-20 mr-2 bg-gray-300 hover:bg-neutral-100">
                    <a class="text-center border-solid border-2 border-neutral-900 w-20 mr-2 bg-gray-300 hover:bg-neutral-100" href="welcome.php">Cancel</a>
                </div>
            </form>
</body>
</html>