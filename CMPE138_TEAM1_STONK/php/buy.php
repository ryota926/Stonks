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
        $an_err = "Please enter a active sales request number.";     
    } else{
        $ann = trim($_POST["an"]);
    }

        // Validate rn
    if(empty(trim($_POST["rn"]))){
        $rn_err = "Please enter a quantity.";     
    } else{
        $rnn = trim($_POST["rn"]);
    }
    
    // Check input errors before inserting in database
    if(empty($an_err) && empty($rn_err)){
        $sql = "select stock_symbol from sales_request where sales_req_id = $ann";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            $stmtan = $row['stock_symbol'];
        }
        $sql = "select price, quantity from sales_request where sales_req_id = $ann";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            $stmtrn = $row['price'];
            $qty = $row['quantity'];
        }
        //remove funds from user
        $sql = "select funds from stock_account where user_id = $userid";
        $result = $link->query($sql);
        while($row = $result->fetch_assoc()) {
            $val = $row['funds'];
        }
        $bq = trim($_POST["rn"]);
        $fundValue = $val - $bq * $stmtrn;
        echo $fundValue;
        $sql = "update stock_account set funds = $fundValue where user_id = $userid";
        $result = $link->query($sql);
        // Prepare an insert statement
        $sql = "INSERT INTO shares (user_id,stock_symbol,current_value,quantity) VALUES (?, ?, ?, ?)";
        //"insert into shares (user_id,stock_symbol,current_value,quantity) VALUES ($user_id,(select stock_symbol from sales_request where sales_req_id = $ann),(select price from sales_request where sales_req_id = $ann), $rnn) 
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "dsdd", $param_id, $param_an, $param_rn, $param_rnn);
            
            // Set parameters
            $param_an = $stmtan;
            $param_rn = $stmtrn;
            $param_id = $userid;
            $param_rnn = $rnn;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page

                //run queery to get quantity from sales req
                //if quanity - buy quantity is 0 then delete requestelse
                $fqty = $qty-$bq;
                if (($qty-$bq) == 0){
                    //delete sales request by filling in the accept date
                    $sql = "update sales_request set accept_date = now() where sales_req_id = $ann";
                    $result = $link->query($sql);
                }
                else{
                    //update quantity of the sales request.
                    $sql = "update sales_request set quantity = $fqty where sales_req_id = $ann";
                    $result = $link->query($sql);
                }
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
    <title>Buy</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Buy</h1>
    </div>
    <p>
        <a href="login-usr.php" class="btn btn-warning">Home</a>
    </p>
</body>
<body>
    <div class="wrapper">
        <h2>Enter Info From Active Sales Request</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($an_err)) ? 'has-error' : ''; ?>">
                <label>Request ID</label>
                <input type="text" name="an" class="form-control" value="<?php echo $ann; ?>">
                <span class="help-block"><?php echo $an_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($rn_err)) ? 'has-error' : ''; ?>">
                <label>Quantity</label>
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

