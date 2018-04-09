<?php
include __DIR__ . "/settings.php";
include __DIR__ . "/LineNotifySimpleLib.php";

session_start();
$is_execute = false;
$is_success = false;
if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SESSION['csrf_token'] === $_POST['csrf_token']) {
    $line_notify = new \Uzulla\Net\LineNotifySimpleLib(LINE_NOTIFY_CLIENT_ID, LINE_NOTIFY_CLIENT_SECRET, CALLBACK_URL);
    $is_success = $line_notify->sendMessage($_POST['access_token'], $_POST['message'], $_POST['imageThumbnail'], $_POST['imageFullsize']);
    $is_execute = true;
}
if (function_exists('random_bytes')) {
    $random_bytes = random_bytes(32);
} else {
    $random_bytes = openssl_random_pseudo_bytes(32);
}
$_SESSION['csrf_token'] = bin2hex($random_bytes);
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>

<h1>LINE Notify API</h1>
<p>※Send to the LINE Notify API Since we are losing access_token here, let's set it up on https site if you want to publish</p>
<form action="callme.php" method="post">
<!--    <label>access_token<input name="access_token"></label><br> -->
<!--    <label>access_token<textarea name="access_token"><?= $_SESSION['access_token'] ?></textarea></label><br>-->
    <label>access_token<input name="access_token" type="hidden" value="<?= $_SESSION['access_token'] ?>"></label><br>
    <label>message<textarea name="message">輸入您想發送的訊息</textarea></label><br>
<!--<label>imageThumbnail(URL)<input name="imageThumbnail"></label><br>
    <label>imageFullsize(URL)<input name="imageFullsize"></label><br>-->
    <input name="csrf_token" type="hidden" value="<?= $_SESSION['csrf_token'] ?>">
    <button type="submit">送信</button>
</form>

<?php
if ($is_execute) { // 実行したか（結果を表示するか
    if ($is_success) { // 成功したか
        echo "<h1>送信成功</h1>";
    } else {
        echo "<h1>送信失敗</h1>";
        echo "<p>以下はデバッグ情報です（通常表示する必要はありません）</p>";
        echo "<textarea style='width:100%; height:500px'>";
        echo htmlspecialchars(print_r($line_notify->getLastError(), 1), ENT_QUOTES);
        echo "</textarea>";
    }
    if(!is_null($line_notify->getLastRatelimitRemaining())) {
        echo "<p>APIのrate limitはあと{$line_notify->getLastRatelimitRemaining()}回のこっています。</p>";
        $rate_limit_reset_date_str = date('Y-m-d H:i:s', $line_notify->getLastRateLimitResetDateEpoch());
        echo "<p>APIのrate limitは{$rate_limit_reset_date_str}(UNIX秒:{$line_notify->getLastRateLimitResetDateEpoch()})に回復します。</p>";
    }
}
?>

<p><a href='/'>Back to top</a></p>
</body>
</html>
