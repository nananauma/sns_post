<?php
session_start();

//設定ファイル
require_once(dirname(__FILE__) . '/../programs/class/class_facebookpost.php');
require_once(dirname(__FILE__) . '/../config/config_facebook_insta.php');

if (isset($_GET["code"])) {
	//アクセストークンを取得してセッションに保存する処理を追加
	//$_SESSION['facebook_access_token'] = (string) $accessToken;
	//echo htmlspecialchars($_GET["code"]);

	//ログインに必要なコードパラメーターを取得して変数へ
	$codeparameter = htmlspecialchars($_GET["code"]);

	//インスタンス化
	$FBgetreq = new Facebook(null);

	//アクセストークンの取得に必要なリクエストトークンを取得する
	$FBactoken = $FBgetreq->getactoken($app_id, $FBcallback_url, $FBIGapp_secret, $codeparameter);

	$_SESSION['facebook_access_token'] = $FBactoken;

	header('Location: ../public/app_facebook.php');
	exit();
} elseif (isset($_GET["error"])) {
	// エラーメッセージを出力して終了
	//echo "連携を拒否しました。";

	header('Location: ../public/login_facebook.php');
	exit;
}

// //タイムゾーンの設定
// date_default_timezone_set('asia/tokyo');
 
// $helper = $fb->getRedirectLoginHelper();
 
// try {
// 	if (isset($_SESSION['facebook_access_token'])) {
// 		$accessToken = $_SESSION['facebook_access_token'];
// 	} else {
// 		//アクセストークンを取得する
// 		$accessToken = $helper->getAccessToken();
// 	}
// } catch(Facebook\Exceptions\FacebookResponseException $e) {
// 	// When Graph returns an error
// 	echo 'Graph returned an error: ' . $e->getMessage();
// 	exit;
// } catch(Facebook\Exceptions\FacebookSDKException $e) {
// 	// When validation fails or other local issues
// 	echo 'Facebook SDK returned an error: ' . $e->getMessage();
// 	exit;
// }
 
// if (isset($accessToken)) {
// 	//アクセストークンをセッションに保存
// 	$_SESSION['facebook_access_token'] = (string) $accessToken;
	
// 	header('Location: app_facebook.php');
// 	exit();
// }else{
// 	echo "<a href='login_facebook.php'>はじめのページへ</a>";
// }