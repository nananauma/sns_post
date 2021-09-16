<?php
session_start();
require_once(dirname(__FILE__) . '/../programs/class/class_twitter.php');

// 「連携アプリを認証」をクリックして帰ってきた時
if (isset($_GET["oauth_token"]) && isset($_GET["oauth_verifier"])) {
    // アクセストークンを取得するための処理
    //インスタンス化
    $TWgetac = new Twitter();

    //アクセストークンとアクセストークンシークレットを取得する
    $TWactoken = $TWgetac->getaccesstoken();
    //var_dump($TWactoken);
    $_SESSION['TWaccess_token'] = $TWactoken["oauth_token"];
    $_SESSION['TWaccess_token_secret'] = $TWactoken["oauth_token_secret"];

    //echo $_SESSION['access_token'] . "<br />";
    //echo $_SESSION['access_token_secret'];

    // ユーザーを投稿画面へ飛ばす
    header('location: ../public/app.php');

    exit;

    // 「キャンセル」をクリックして帰ってきた時
} elseif (isset($_GET["denied"])) {
    // エラーメッセージを出力して終了
    //echo "連携を拒否しました。";
    header('Location: ../public/login.php');
    exit;
}


//require_once 'vendor/autoload.php';

//use Abraham\TwitterOAuth\TwitterOAuth;

// Twitterから返されたOAuthトークンの検証
// if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
//     die('OAuth token invalid');
// }

// // OAuthトークンも用いてTwitterOAuthをインスタンス化
// $connection = new TwitterOAuth($_SESSION['consumer_key'], $_SESSION['consumer_secret'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

// // アクセストークンの取得
// $_SESSION['access_token'] = $connection->oauth("oauth/access_token", ["oauth_verifier" => $_REQUEST['oauth_verifier']]);

// session_regenerate_id();

// header('location: /app.php');