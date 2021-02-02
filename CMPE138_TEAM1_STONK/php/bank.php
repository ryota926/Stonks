<?php
// Include config file
include "../inc/dbinfo.inc"; 
session_start();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login-usr.php");
    exit;
}
$userid = htmlspecialchars($_SESSION["user_id"]);
// Define variables and initialize with empty values
$ann = $rnn = "";
$an_err = $rn_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate an
    if(empty(trim($_POST["an"]))){
        $an_err = "Please enter an account number.";     
    } else{
        $ann = trim($_POST["an"]);
    }

        // Validate rn
    if(empty(trim($_POST["rn"]))){
        $rn_err = "Please enter a routing number.";     
    } else{
        $rnn = trim($_POST["rn"]);
    }
    
    // Check input errors before inserting in database
    if(empty($an_err) && empty($rn_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO bank_account (account_number, routing_number, user_id) VALUES (?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ddd", $param_an, $param_rn, $param_id);
            
            // Set parameters
            $param_an = $ann;
            $param_rn = $rnn;
            $param_id = $userid;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                echo "Success.";
            } else{
                echo "Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Account Info</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <p>
        <a href="login-usr.php" class="btn btn-warning">Home</a>
    </p>
</body>
<body>
    <div class="wrapper">
        <h2>Enter Account info</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($an_err)) ? 'has-error' : ''; ?>">
                <label>Account Number</label>
                <input type="text" name="an" class="form-control" value="<?php echo $ann; ?>">
                <span class="help-block"><?php echo $an_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($rn_err)) ? 'has-error' : ''; ?>">
                <label>Routing Number</label>
                <input type="text" name="rn" class="form-control" value="<?php echo $rnn; ?>">
                <span class="help-block"><?php echo $rn_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
        </form>
    </div>    
</body>
</html>

