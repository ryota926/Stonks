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


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["user_name"]); ?></b>. Welcome to Stonks.</h1>
    </div>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
    <p>
        <a href="stocks.php" class="btn btn-info">View Stock Info</a>
        <a href="sell.php" class="btn btn-info">View Current Sell Requests</a>
        <a href="bank.php" class="btn btn-info">Add Bank</a>
        <a href="funds.php" class="btn btn-info">Add Funds</a>
        <a href="sale.php" class="btn btn-info">Create Sell Request</a>
        <a href="buy.php" class="btn btn-info">Buy</a>
    </p>
</body>
<div class="wrapper">

<h3>Current Stocks </h3>
<table class="table">
  <tr style="font-weight:bold">
    <td>Stock</td>
    <td>Quantity</td>
    <td>Current Value</td>
  </tr>

<?php
$sql = "Select stock_symbol, quantity, current_value From shares Where user_id = $userid AND quantity != 0";
$result = $link->query($sql);
while($row = $result->fetch_assoc()) {
  echo "<tr><td>". $row['stock_symbol'] . "</td><td>" . $row['quantity'] . "</td><td>" . $row['current_value'] . "</td></tr>";
}
?>
</table>
<h3>Your Current Funds</h3>
<table class="table">
  <tr style="font-weight:bold">
    <td>Amount</td>
  </tr>

<?php
$sql2 = "Select funds from stock_account where user_id = $userid";
$result2 = $link->query($sql2);
while($row2 = $result2->fetch_assoc()) {
  echo "<tr><td>". $row2['funds'] . "</td></tr>";
}
?>
</table>

<h3>Your Current Active Sale Post</h3>
<table class="table">
  <tr style="font-weight:bold">
    <td>Stock</td>
    <td>Quantity</td>
    <td>Price</td>
    <td>Request Date</td>
  </tr>

<?php
$sql2 = "Select stock_symbol, quantity, price, req_date from sales_request where user_id = $userid AND accept_date is NULL";
$result2 = $link->query($sql2);
while($row2 = $result2->fetch_assoc()) {
  echo "<tr><td>". $row2['stock_symbol'] . "</td><td>" . $row2['quantity'] . "</td><td>" . $row2['price'] . "</td><td>" . $row2['req_date'] . "</td></tr>";
}
?>
</table>


</div>
</html>
