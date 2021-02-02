<?php
// Initialize the session
session_start();
include "../inc/dbinfo.inc"; 

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login-usr.php");
    exit;
}
// Check connection
if ($link->connect_error) {
  die("Connection failed: " . $link->connect_error);
}
$userid = htmlspecialchars($_SESSION["user_id"]);



$price = $qa = $share = "";
$price_err = $qa_err = $share_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate an
    if(empty(trim($_POST["price"]))){
        $price_err = "Please enter a sale price.";     
    } else{
        $price = trim($_POST["price"]);
    }
    if(empty(trim($_POST["qa"]))){
        $qa_err = "Please enter a quantity.";     
    } else{
        // Prepare a select statement
        $sql = "SELECT quantity FROM shares WHERE user_id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $userid);
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if($stmt < trim($_POST["qa"])){
                    $qa_err = "You don't have that many shares";
                } else{
                    $qa = trim($_POST["qa"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    if(empty(trim($_POST["share"]))){
        $share_err = "Please enter a stock symbol of the share.";     
    } else{
        // Prepare a select statement
        $sql = "SELECT stock_symbol FROM shares WHERE user_id = ? AND stock_symbol = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $userid, trim($_POST["share"]));
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt); 
                
                if(mysqli_stmt_num_rows($stmt) == 1){

                    $share = trim($_POST["share"]);
                } else{
                    $share_err = "You don't own any of this stock."; 
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Check input errors before inserting in database
    if(empty($price_err) && empty($share_err) && empty($qa_err)){
        
        // Prepare an insert statement
        $sql2 = "INSERT INTO sales_request (user_id, price, quantity, stock_symbol, req_date) VALUES (?,?,?,?,?)";
        if($stmt2 = mysqli_prepare($link, $sql2)){
            mysqli_stmt_bind_param($stmt2, "dddsd", $param_id, $param_price, $param_quantity, $param_sym, $param_date);
            // Set parameters
            $param_id = $userid;
            $param_price = $price;
            $param_quantity = $qa;
            $param_sym = $share;
            $param_date =  'now()';
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt2)){
                // Redirect to login page
                echo "Success.";
                //add funds from user
                $sql2 = "DELETE FROM shares WHERE stock_symbol = '$param_sym' AND user_id = $userid";
                echo $sql2;
                $result2 = $link->query($sql2);
                if($result2){
                    echo "works";
                }
                $sql = "select funds from stock_account where user_id = $userid";
                $result = $link->query($sql);
                while($row = $result->fetch_assoc()) {
                    $val = $row['funds'];
                }
                $fundValue = $val + $qa * $price;
                $sql = "update stock_account set funds = $fundValue where user_id = $userid";
                $result = $link->query($sql);

                //remove share from their account

            } else{
                echo "Something went wrong. Please try again later2.";
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
    <title>Sell Requests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Sell Requests</h1>
    </div>
    <p>
        <a href="login-usr.php" class="btn btn-warning">Home</a>
    </p>
</body>
<div class="wrapper">

<h3>Create a sell request</h3>
<body>
    <div class="wrapper">
        <h2>Enter info from sell request to buy shares</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($share_err)) ? 'has-error' : ''; ?>">
                <label>Share</label>
                <input type="text" name="share" class="form-control" value="<?php echo $share; ?>">
                <span class="help-block"><?php echo $share_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($qa_err)) ? 'has-error' : ''; ?>">
                <label>Quantity</label>
                <input type="text" name="qa" class="form-control" value="<?php echo $qa; ?>">
                <span class="help-block"><?php echo $qa_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                <label>Price</label>
                <input type="text" name="price" class="form-control" value="<?php echo $price; ?>">
                <span class="help-block"><?php echo $price_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
        </form>
    </div>    
</body>


</div>
</html>

