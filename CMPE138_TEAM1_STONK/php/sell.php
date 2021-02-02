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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Requests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
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

<h3>Current Sell Requests </h3>
<table class="table">
  <tr style="font-weight:bold">
    <td>Stock</td>
    <td>Request ID</td>
    <td>Price</td>
    <td>Quantity</td>
    <td>Request Date</td>
    
  </tr>

<?php
$sql = "select stock_symbol,sales_req_id,price,quantity,req_date from sales_request WHERE accept_date IS NULL";
$result = $link->query($sql);
while($row = $result->fetch_assoc()) {
  echo "<tr><td>". $row['stock_symbol'] . "</td><td>" . $row['sales_req_id'] . "</td><td>" . $row['price'] . "</td><td>" . $row['quantity'] . "</td><td>" . $row['req_date'] . "</td></tr>";
}
?>
</table>




</div>
</html>

