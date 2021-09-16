<?php
session_start();

//設定ファイル
require_once(dirname(__FILE__) . '/../config/config_facebook_insta.php');
require_once(dirname(__FILE__) . '/../programs/class/class_instapost.php');

if (isset($_SESSION['facebook_access_token'])) {

	//$accessToken = $_SESSION['facebook_access_token'];
	//Instaprofileをインスタンス化
	$IGgetprofile = new Instapost($_SESSION['facebook_access_token']);

	//instaのプロフィールを取得する
	$IGprofile = $IGgetprofile->getInstaprofile();
} else {
	header('Location: login_instagram.php');
	exit();
}
?>
<!DOCTYPE HTML>
<html lang="ja">

<head>
	<meta charset="utf-8">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<title>Facebook App</title>
	<!--#_=_を排除する-->
	<script type="text/javascript">
		if (window.location.hash && window.location.hash == '#_=_') {
			if (window.history && history.pushState) {
				window.history.pushState("", document.title, window.location.pathname);
			} else {
				// Prevent scrolling by storing the page's current scroll offset
				var scroll = {
					top: document.body.scrollTop,
					left: document.body.scrollLeft
				};
				window.location.hash = '';
				// Restore the scroll offset, should be flicker free
				document.body.scrollTop = scroll.top;
				document.body.scrollLeft = scroll.left;
			}
		}
	</script>
</head>

<body>
	<div class="container mt-4 mb-4">
		<h4>instagramアカウント</h4>
		<div class="row">
			<div class="col-sm-8">
				<span>ユーザー名：<?= $IGprofile ?></span>
			</div>
		</div>
	</div>
	<div class="container mb-4">
		<h4>instagram投稿</h4>
		<form action="../programs/post_sns.php" method="POST" enctype="multipart/form-data">
			<textarea rows="3" cols="100" name="IG-text"></textarea><br />
			<input type="file" name="IG-picture"><br />
			<input type="submit" value="送信" />
		</form>
	</div>
	<?php
	if (isset($_SESSION['IGpostresult'])) {
		echo "投稿完了" . $_SESSION['IGpostresult'];
	} else {
		echo "まだ投稿はありません";
	}
	?>
	<p><a href='logout_insta.php'>ログアウト</a></p>
</body>

</html>