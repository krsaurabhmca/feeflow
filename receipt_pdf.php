<?php
// receipt_pdf.php - Simple wrapper for receipt print
require_once 'includes/config.php';
if (!is_logged_in())
    redirect('index.php');

$fee_id = $_GET['id'] ?? null;
if (!$fee_id)
    die("Invalid Request");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Downloading Receipt...</title>
</head>
<body onload="window.print(); window.location.href='receipt.php?id=<?php echo $fee_id; ?>';">
    <p>Preparing your receipt for download/print...</p>
</body>
</html>
