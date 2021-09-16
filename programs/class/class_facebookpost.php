<?php
//設定ファイル
require_once(dirname(__FILE__) . '/../../config/config_facebook_insta.php');

//facebookページに投稿するためのクラス
class Facebook
{
    //property
    public $accessToken; //ユーザーアクセストークン

    // constructor
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    //facebookログイン認証
    public function getactoken($appid, $callback_url, $FBIGapp_secret, $codeparameter)
    {
        // リクエストするURL
        $url = "https://graph.facebook.com/v11.0/oauth/access_token?client_id={$appid}&redirect_uri={$callback_url}&client_secret={$FBIGapp_secret}&code={$codeparameter}";
        //echo $url;

        //cURLセッションを初期化する
        $curl = curl_init($url);

        // オプションをセット
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
        //curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE); //プロキシを有効にする

        // レスポンスを変数に入れる
        $response = curl_exec($curl);

        //jsonに変換
        $rejson = json_decode($response, true);

        // 処理を終了
        curl_close($curl);

        //取得結果を表示
        //var_dump($rejson);
        if ($rejson === NULL) {
            echo "アクセストークンを取得できませんでした";
        } else {
            //アクセストークンとページIDの取得
            $accessToken = $rejson['access_token'];
            return $accessToken;
        }
    }

    //facebookのpageidを利用してinstagramuseridを取得
    public function postfacebook()
    {

        if (!empty($_FILES)) {
            //サーバー上のtempファイル名を格納
            $fileName = $_FILES['picture']['name'];

            //ローカルの画像保存先のファイルパス作成
            $filePath = 'FBimages_after/' . $fileName;

            //ローカルのフォルダに画像を移動
            $result = move_uploaded_file($_FILES['picture']['tmp_name'], $filePath);

            //画像フォルダをhttpの形式に
            $fbfilePath = 'https://diana-heart.co.jp/dev/test_sns/tmp/FBimages_after/' . $fileName;

            //投稿文字
            $FBcap = $_POST['page-text'];
        }

        //facebookuserIDとユーザーアクセストークンを利用してfacebookページIDの取得
        // リクエストするURL
        $url = "https://graph.facebook.com/{$_SESSION['FBuserid']}/accounts?access_token={$this->accessToken}";

        //cURLセッションを初期化する
        $curl = curl_init($url);

        // オプションをセット
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
        //curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE); //プロキシを有効にする

        // レスポンスを変数に入れる
        $response = curl_exec($curl);

        //jsonに変換
        $rejson = json_decode($response, true);

        // 処理を終了
        curl_close($curl);

        //取得結果を表示
        //var_dump($rejson);
        if ($rejson === NULL) {
            echo "ページアクセストークンを取得できませんでした";
        } else {
            //ページアクセストークンとページIDの取得
            $pageac = $rejson['data'][0]['access_token'];
            $pageid = $rejson['data'][0]['id'];
            //echo $pageac;
        }

        //ページ投稿処理
        //facebookアクセストークンを利用してfacebookuserIDを取得
        //リクエストするURL
        $FBpublish = "https://graph.facebook.com/v11.0/{$pageid}/photos?url={$fbfilePath}&message={$FBcap}&access_token={$pageac}";
        //echo $FBpublish;

        //IGコンテナを作成するためのURLをPOST
        // curlを初期化
        $FBpublishch = curl_init();

        // 設定!
        curl_setopt($FBpublishch, CURLOPT_URL, $FBpublish); // 送り先
        curl_setopt($FBpublishch, CURLOPT_POST, true); // POSTです
        curl_setopt($FBpublishch, CURLOPT_RETURNTRANSFER, true); // 実行結果取得の設定

        // 実行！
        $FBpubres = curl_exec($FBpublishch);
        //echo $IGpubres;

        // リソースを閉じる
        curl_close($FBpublishch);

        //取得結果を表示
        //var_dump($FBpubres);
        if (strpos($FBpubres, 'error') == false) {
            //echo "投稿完了";
            return $FBpubres;
        } else {
            echo "投稿できませんでした";
        }
    }

    //アクセストークンを利用してプロフィールを取得
    public function getFbprofile()
    {
        //facebookアクセストークンを利用してfacebookuserIDを取得
        //リクエストするURL
        $FBgetprofileurl = "https://graph.facebook.com/me?access_token={$this->accessToken}";

        //cURLセッションを初期化する
        $FBgetprofilecurl = curl_init($FBgetprofileurl);

        // オプションをセット
        curl_setopt($FBgetprofilecurl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($FBgetprofilecurl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
        //curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE); //プロキシを有効にする

        // レスポンスを変数に入れる
        $FBgetprofileres = curl_exec($FBgetprofilecurl);

        //jsonに変換
        $FBgetprofileresrejson = json_decode($FBgetprofileres, true);

        // 処理を終了
        curl_close($FBgetprofilecurl);

        //取得結果を表示
        //var_dump($FBgetprofileresrejson);
        if ($FBgetprofileresrejson === NULL) {
            echo "IDを取得できませんでした";
        } else {
            $FBuserid = $FBgetprofileresrejson['id'];
            $_SESSION['FBuserid'] = $FBuserid; //セッション変数に登録
            //return $IGuserid;
        }

        //facebookuserIDとユーザーアクセストークンを利用してfacebookプロフィール情報の取得
        //リクエストするURL
        $fbgeturl = "https://graph.facebook.com/{$FBuserid}?fields=name,picture&access_token={$this->accessToken}";

        //cURLセッションを初期化する
        $fbgetcurl = curl_init($fbgeturl);

        // オプションをセット
        curl_setopt($fbgetcurl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($fbgetcurl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
        //curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE); //プロキシを有効にする

        // レスポンスを変数に入れる
        $fbgetres = curl_exec($fbgetcurl);

        //jsonに変換
        $fbgetresrejson = json_decode($fbgetres, true);

        // 処理を終了
        curl_close($fbgetcurl);

        //取得結果を表示
        //var_dump($fbgetresrejson);
        if ($fbgetresrejson === NULL) {
            echo "ページアクセストークンを取得できませんでした";
        } else {
            //名前の取得
            $fbname = $fbgetresrejson['name'];
            return $fbname;
        }
    }
}
