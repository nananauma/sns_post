<?php
session_start();
require_once(dirname(__FILE__) . '/../programs/class/class_twitter.php');

if (isset($_SESSION['TWaccess_token'])) {
    //セッションのアクセストークンを変数へ
    //$TWaccess_token = $_SESSION['TWaccess_token'];
    //セッションのアクセストークンシークレットを変数へ
    //$TWaccess_token_secret = $_SESSION['TWaccess_token_secret'];
    //echo $access_token_secret . "<br />";

    //インスタンス化
    $TWgetuser = new Twitter();

    //ユーザー情報取得
    $TWuser = $TWgetuser->getuser($_SESSION['TWaccess_token'], $_SESSION['TWaccess_token_secret']);
} else {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Twitter App</title>
</head>

<body>
    <div class="container mt-4 mb-4">
        <h4>Twitterアカウント</h4>
        <div class="row">
            <div class="col-sm-8">
                <span>ユーザー名：<?php echo $TWuser['name'] ?>（@<?php echo $TWuser['screen_name'] ?>）</span>
            </div>
        </div>
    </div>

    <div class="container mb-4">
        <h4>ツイート</h4>
        <form action="../programs/post_sns.php" method="POST" enctype="multipart/form-data">
            <textarea rows="3" cols="100" name="tweet"></textarea><br />
            <input type="file" name="picture"><br />
            <input type="submit" value="送信" />
        </form>
    </div>
    <?php
    if (isset($_SESSION['TWpostresult'])) {
        echo "投稿完了" . $_SESSION['TWpostresult'];
    } else {
        echo "まだ投稿はありません";
    }
    ?>
    <p><a href='logout.php'>ログアウト</a></p>
</body>

</html>