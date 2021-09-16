<?php
session_start();

require_once(dirname(__FILE__) . '/../config/config.php');
require_once(dirname(__FILE__) . '/../programs/class/class_twitter.php');

//インスタンス化
$TWgetreq = new Twitter();

//アクセストークンの取得に必要なリクエストトークンを取得する
$TWreqtoken = $TWgetreq->getrequesttoken();
//リクエストトークンとリクエストトークンシークレットをcallback.php で利用する
$_SESSION['oauth_token'] = $TWreqtoken['oauth_token'];
$_SESSION['oauth_token_secret'] = $TWreqtoken['oauth_token_secret'];

//header("Location: https://api.twitter.com/oauth/authorize?oauth_token=" . $TWreqtoken["oauth_token"]);

$loginUrl = "https://api.twitter.com/oauth/authorize?oauth_token=" . $TWreqtoken["oauth_token"];

echo '<a href=' . $loginUrl . '>Log in with Twitter!</a></br >';

echo "<a href='index.html'>はじめのページへ</a>";
//use Abraham\TwitterOAuth\TwitterOAuth;

//$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

//$request_token = $connection->oauth('oauth/request_token', ['oauth_callback' => OAUTH_CALLBACK]);

// // callback.php で利用する
// $_SESSION['oauth_token'] = $request_token['oauth_token'];
// $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
// $_SESSION['consumer_key'] = CONSUMER_KEY;
// $_SESSION['consumer_secret'] = CONSUMER_SECRET;

// // twitter.com上の認証画面のURLを取得してリダイレクト
// $url = $connection->url('oauth/authenticate', ['oauth_token' => $request_token['oauth_token']]);
// header('location: ' . $url);
