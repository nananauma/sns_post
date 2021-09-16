<?php
//設定ファイル
require_once(dirname(__FILE__) . '/../../config/config_facebook_insta.php');

//インスタポストするためのクラス
class Instapost
{
    //property
    public $accessToken; //ユーザーアクセストークン

    // constructor
    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }
    //instagramログイン認証
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
    public function postinsta()
    {

        if (!empty($_FILES)) {

            //サーバー上のtempファイル名を格納
            $IGfileName = $_FILES['IG-picture']['name'];

            //ローカルの画像保存先のファイルパス作成
            $IGfilePath = 'IGimages_after/' . $IGfileName;
            //$IGfilePath = 'IGimages_after/photo.jpeg';

            //ローカルのフォルダに画像を移動
            $IGresult = move_uploaded_file($_FILES['IG-picture']['tmp_name'], $IGfilePath);

            //画像フォルダをhttpの形式に
            $IGupfilePath = 'https://diana-heart.co.jp/dev/test_sns/tmp/IGimages_after/' . $IGfileName;

            //投稿キャプション
            $IGcap = $_POST['IG-text'];
        }

        //facebook指定のURLにポストして投稿に必要なコンテナを作成
        //$IGcontainer = "https://graph.facebook.com/v11.0/{$_SESSION['IGuserid']}/media?image_url={$IGupfilePath}&caption={$IGcap}&access_token={$this->accessToken}";
        $IGcontainer = "https://graph.facebook.com/v11.0/{$_SESSION['IGuserid']}/media?image_url={$IGupfilePath}&caption={$IGcap}&access_token={$this->accessToken}";

        //IGコンテナを作成するためのURLをPOST
        // curlを初期化
        $ch = curl_init();

        // 設定!
        curl_setopt($ch, CURLOPT_URL, $IGcontainer); // 送り先
        curl_setopt($ch, CURLOPT_POST, true); // POSTです
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 実行結果取得の設定

        // 実行！とcreation_idの取得
        $creationid_res = curl_exec($ch);

        //jsonに変換
        $creatidrejson = json_decode($creationid_res, true);

        // リソースを閉じる
        curl_close($ch);

        //取得結果を表示
        //var_dump($creatidrejson);
        if ($creatidrejson === NULL) {
            echo "クリエイションIDを取得できませんでした";
        } else {
            //ページアクセストークンとページIDの取得
            $creation_id = $creatidrejson['id'];
            //return $creation_id;
        }
        //instagramに投稿する
        //作成したIGコンテナを公開するためのURL(インスタに投稿)
        //$IGpublish = "https://graph.facebook.com/v11.0/{$_SESSION['IGuserid']}/media_publish?creation_id={$creation_id}&access_token={$this->accessToken}";
        $IGpublish = "https://graph.facebook.com/v11.0/{$_SESSION['IGuserid']}/media_publish?creation_id={$creation_id}&access_token={$this->accessToken}";

        //IGコンテナを作成するためのURLをPOST
        // curlを初期化
        $IGpublishch = curl_init();

        // 設定!
        curl_setopt($IGpublishch, CURLOPT_URL, $IGpublish); // 送り先
        curl_setopt($IGpublishch, CURLOPT_POST, true); // POSTです
        curl_setopt($IGpublishch, CURLOPT_RETURNTRANSFER, true); // 実行結果取得の設定

        // 実行！
        $IGpubres = curl_exec($IGpublishch);
        //echo $IGpubres;

        // リソースを閉じる
        curl_close($IGpublishch);

        if (strpos($IGpubres, 'error') == false) {
            //echo "投稿完了";
            return $IGpubres;
        } else {
            echo "投稿できませんでした";
        }
    }

    //アクセストークンを利用してプロフィールを取得
    public function getInstaprofile()
    {
        //facebookアクセストークンを利用してfacebookuserIDを取得
        //リクエストするURL
        $FBgetprofileurl = "https://graph.facebook.com/me?access_token={$this->accessToken}";
        //echo $FBgetprofileurl;

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
        }

        //facebookuserIDとユーザーアクセストークンを利用してfacebookページIDの取得
        // リクエストするURL
        $url = "https://graph.facebook.com/{$FBuserid}/accounts?access_token={$this->accessToken}";

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
            //$pageac = $rejson['data'][0]['access_token'];
            $pageid = $rejson['data'][0]['id'];
            //echo $pageac;
        }
        //ページIDとfacebookアクセストークンを利用してInstagramのIGuserIDを取得
        //リクエストするURL
        $IGurl = "https://graph.facebook.com/v11.0/{$pageid}?fields=instagram_business_account&access_token={$this->accessToken}";

        //cURLセッションを初期化する
        $IGcurl = curl_init($IGurl);

        // オプションをセット
        curl_setopt($IGcurl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($IGcurl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
        //curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE); //プロキシを有効にする

        // レスポンスを変数に入れる
        $IGresponse = curl_exec($IGcurl);

        //jsonに変換
        $IGrejson = json_decode($IGresponse, true);

        // 処理を終了
        curl_close($IGcurl);

        //取得結果を表示
        //var_dump($IGrejson);
        if ($IGrejson === NULL) {
            echo "IGuserIDを取得できませんでした";
        } else {
            //IGuserIDの取得
            $IGuserid = $IGrejson['instagram_business_account']['id'];
            $_SESSION['IGuserid'] = $IGuserid; //セッション変数に登録
        }

        //リクエストするURL
        $IGgetprofileurl = "https://graph.facebook.com/v3.2/{$IGuserid}?fields=username%2Cprofile_picture_url&access_token={$this->accessToken}";
        //echo $IGgetprofileurl;

        //cURLセッションを初期化する
        $IGgetprofilecurl = curl_init($IGgetprofileurl);

        // オプションをセット
        curl_setopt($IGgetprofilecurl, CURLOPT_CUSTOMREQUEST, 'GET'); // メソッド指定
        curl_setopt($IGgetprofilecurl, CURLOPT_RETURNTRANSFER, true); // レスポンスを文字列で受け取る
        //curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, TRUE); //プロキシを有効にする

        // レスポンスを変数に入れる
        $IGgetprofileres = curl_exec($IGgetprofilecurl);

        //jsonに変換
        $IGgetprofilerejson = json_decode($IGgetprofileres, true);

        // 処理を終了
        curl_close($IGgetprofilecurl);

        //取得結果を表示
        //var_dump($IGgetprofilerejson);
        if ($IGgetprofilerejson === NULL) {
            echo "ユーザーネームを取得できませんでした";
        } else {
            //IGusernameの取得
            $IGusername = $IGgetprofilerejson['username'];
            return $IGusername;
        }
    }
}
