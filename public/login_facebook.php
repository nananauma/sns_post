<?php
session_start();

//設定ファイル
require_once(dirname(__FILE__) . '/../config/config_facebook_insta.php');


//$helper = $fb->getRedirectLoginHelper();

//オプションによって認証画面の文言が変わる
//$permissions = ['email', 'user_likes','user_posts']; //あなたの公開プロフィール、メールアドレス、タイムライン投稿、いいね！。
//$permissions = ['email', 'user_likes']; //あなたの公開プロフィール、メールアドレス、いいね！。
//$permissions = ['email', 'user_posts'];//あなたのタイムライン投稿。
//$permissions = ['email','user_friends'];//あなたの公開プロフィール、友達リスト、メールアドレス。
//$permissions = ['email'];//あなたの公開プロフィール、メールアドレス。
//$permissions = ['email', 'pages_read_engagement', 'ads_management', 'business_management', 'pages_manage_posts']; // optional
//$loginUrl = $helper->getLoginUrl('https://cedd3ecb9ac1.ngrok.io/callback_facebook.php', $permissions);
//パーミッション
$permissions = ['email', 'pages_read_engagement', 'ads_management', 'business_management', 'pages_manage_posts']; // optional
//カンマ区切りに
$permissions = implode(",", $permissions);

//CSRF対策ランダムな文字列を生成
$token_byte = openssl_random_pseudo_bytes(16);
$csrf_token = bin2hex($token_byte);

$url = "https://www.facebook.com/v11.0/dialog/oauth?client_id={$app_id}&redirect_uri={$FBcallback_url}&state={$csrf_token}&scope=$permissions";

echo '<a href=' . $url . '>Log in with Facebook!</a></br >';

echo "<a href='index.html'>はじめのページへ</a>";
