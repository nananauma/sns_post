<?php
session_start();
//設定ファイル
require_once(dirname(__FILE__) . '/../programs/class/class_instapost.php');
require_once(dirname(__FILE__) . '/../programs/class/class_facebookpost.php');
require_once(dirname(__FILE__) . '/../programs/class/class_twitter.php');

//投稿の処理(insta,facebook,Twitterの投稿をここに書いて後で処理分ける)
if (isset($_POST['IG-text'])) {

    //instapostをインスタンス化
    //$IGpost = new Instapost($accessToken);
    $IGpost = new Instapost($_SESSION['facebook_access_token']);

    //instaを投稿する
    $IGpostresult = $IGpost->postinsta();
    //echo $IGpostresult;
    //投稿結果をセッションに保存する
    $_SESSION['IGpostresult'] = $IGpostresult;

    //Instagramの投稿画面に戻る
    header('Location: ../public/app_insta.php');
}

if (isset($_POST['page-text'])) {
    $FBpost = new Facebook($_SESSION['facebook_access_token']);

    //instaを投稿する
    $FBpostresult = $FBpost->postfacebook();
    //echo $IGpostresult;
    //投稿結果をセッションに保存する
    $_SESSION['FBpostresult'] = $FBpostresult;

    //facebookの投稿画面に戻る
    header('Location: ../public/app_facebook.php');
}

if (isset($_POST['tweet'])) {
    $TWpost = new Twitter();

    //Twitterを投稿する
    $TWpostresult = $TWpost->twpost($_SESSION['TWaccess_token'], $_SESSION['TWaccess_token_secret']);
    //echo $TWpostresult;
    //投稿結果をセッションに保存する
    $_SESSION['TWpostresult'] = $TWpostresult;

    //Twitterの投稿画面に戻る
    header('Location: ../public/app.php');
}
