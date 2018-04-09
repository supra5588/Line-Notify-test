<?php
include __DIR__ . "/settings.php";
include __DIR__ . "/LineNotifySimpleLib.php";
session_start();
$line_notify = new \Uzulla\Net\LineNotifySimpleLib(LINE_NOTIFY_CLIENT_ID, LINE_NOTIFY_CLIENT_SECRET, CALLBACK_URL);
$access_token = $line_notify->requestAccessToken($_GET);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>

<?php
if ($access_token === false) {
    echo "<h1>認証失敗</h1>";
    echo "<p>The following is debugging information (it is not necessary to display normally)</p>";
    echo "<textarea style='width:100%; height:500px'>";
    echo htmlspecialchars(print_r($line_notify->getLastError(), 1), ENT_QUOTES);
    echo "</textarea>";
} else {
/*
    echo "<h1>認証成功</h1>";
    echo "<p>access_token <input type='text' style='width:400px' value='" . htmlspecialchars($access_token, ENT_QUOTES) . "'></p>";
    echo "<p>※Please copy and paste the above access_token</p>";
*/
    $_SESSION['access_token']= $access_token;

    header("location:/callme.php");
}
?>
