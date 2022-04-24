<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <!--Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>
<body>
    <div class="flex flex-col text-lg text-center mt-6">
            Hi, <span class="font-bold"><?php echo htmlspecialchars($_SESSION["username"]); ?>.</span> Welcome to our site.
    </div>

    <div class="flex justify-around text-center text-sm w-full md:w-3/5 lg:w-2/5 mx-auto my-4">
        <a href="reset-password.php" class="text-blue-500 no-underline hover:underline">Reset Password</a>
        <a href="logout.php" class="text-blue-500 no-underline hover:underline">Sign Out</a>
    </div>

    <!--CONTENT GOES HERE -->
    <div class="w-full md:w-3/5 lg:w-1/2 border border-solid border-2 bg-stone-100 py-2 mx-auto">
        <img src="cat.jpg" class="mx-auto h-96 rounded-full shadow-lg shadow-neutral-500">
    </div>
</body>
</html>