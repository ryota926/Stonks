<?php
// Initialize the session
session_start();
include "../inc/dbinfo.inc"; 

// Check connection
if ($link->connect_error) {
  die("Connection failed: " . $link->connect_error);
} 
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
        <h1>Stocks</h1>
    </div>
    <p>
        <a href="login-usr.php" class="btn btn-warning">Home</a>
    </p>
</body>
<div class="wrapper">

<h3>Current Stocks </h3>
<table class="table">
  <tr style="font-weight:bold">
    <td>Stock</td>
    <td>Company</td>
    <td>CEO</td>
    <td>Year Founded</td>
    <td>Current Value</td>
    
  </tr>

<?php
$sql = "select stock_symbol,stock_name,ceo,founded_year,current_value from stock_info";
$result = $link->query($sql);
while($row = $result->fetch_assoc()) {
  echo "<tr><td>". $row['stock_symbol'] . "</td><td>" . $row['stock_name'] . "</td><td>" . $row['ceo'] . "</td><td>" . $row['founded_year'] . "</td><td>$" . $row['current_value'] . "</td></tr>";
}
?>
</table>




</div>
</html>

