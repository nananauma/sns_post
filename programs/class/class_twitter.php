<?php
//設定ファイル
require_once(dirname(__FILE__) . '/../../config/config.php');

//Twitterポストするためのクラス
class Twitter
{
    public function getrequesttoken()
    {
        // 認証画面のURLを[.../authenticate]にした時にリダイレクトループを防ぐ処理
        if (isset($_GET['oauth_token']) || isset($_GET["oauth_verifier"])) {
            //echo "認証画面から帰ってきました。";
            exit;
        }
        /*** [手順1] リクエストトークンの取得 ***/

        // [アクセストークンシークレット] (まだ存在しないので「なし」)
        $access_token_secret = "";

        // エンドポイントURL
        $request_url = "https://api.twitter.com/oauth/request_token";

        // リクエストメソッド
        $request_method = "POST";

        // キーを作成する (URLエンコードする)
        $signature_key = rawurlencode(api_secret) . "&" . rawurlencode($access_token_secret);

        // パラメータ([oauth_signature]を除く)を連想配列で指定
        $params = array(
            "oauth_callback" => callback_url,
            "oauth_consumer_key" => api_key,
            "oauth_signature_method" => "HMAC-SHA1",
            "oauth_timestamp" => time(),
            "oauth_nonce" => microtime(),
            "oauth_version" => "1.0",
        );

        // 各パラメータをURLエンコードする
        foreach ($params as $key => $value) {
            // コールバックURLはエンコードしない
            if ($key == "oauth_callback") {
                continue;
            }

            // URLエンコード処理
            $params[$key] = rawurlencode($value);
        }

        // 連想配列をアルファベット順に並び替える
        ksort($params);

        // パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
        $request_params = http_build_query($params, "", "&");

        // 変換した文字列をURLエンコードする
        $request_params = rawurlencode($request_params);

        // リクエストメソッドをURLエンコードする
        $encoded_request_method = rawurlencode($request_method);

        // リクエストURLをURLエンコードする
        $encoded_request_url = rawurlencode($request_url);

        // リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
        $signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params;

        // キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
        $hash = hash_hmac("sha1", $signature_data, $signature_key, TRUE);

        // base64エンコードして、署名[$signature]が完成する
        $signature = base64_encode($hash);

        // パラメータの連想配列、[$params]に、作成した署名を加える
        $params["oauth_signature"] = $signature;

        // パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
        $header_params = http_build_query($params, "", ",");

        // リクエスト用のコンテキストを作成する
        $context = array(
            "http" => array(
                "method" => $request_method, // リクエストメソッド (POST)
                "header" => array(              // カスタムヘッダー
                    "Authorization: OAuth " . $header_params,
                ),
            ),
        );

        // cURLを使ってリクエスト
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);    // リクエストURL
        curl_setopt($curl, CURLOPT_HEADER, true);    // ヘッダーを取得する
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context["http"]["method"]);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $context["http"]["header"]);    // リクエストヘッダーの内容
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);    // タイムアウトの秒数
        $res1 = curl_exec($curl);
        $res2 = curl_getinfo($curl);
        curl_close($curl);

        // 取得したデータ
        $response = substr($res1, $res2["header_size"]);    // 取得したデータ(JSONなど)

        // リクエストトークンを取得できなかった場合
        if (!$response) {
            echo "<p>リクエストトークンを取得できませんでした…。api_keyとcallback_url、そしてTwitterのアプリケーションに設定しているCallback URLを確認して下さい。</p>";
            exit;
        }

        // $responseの内容(文字列)を$query(配列)に直す
        $query = [];
        parse_str($response, $query);

        return $query;
    }

    public function getaccesstoken()
    {
        //session_start();
        $request_token_secret = $_SESSION["oauth_token_secret"];
        // リクエストURL
        $request_url = "https://api.twitter.com/oauth/access_token";

        // リクエストメソッド
        $request_method = "POST";

        // キーを作成する
        $signature_key = rawurlencode(api_secret) . "&" . rawurlencode($request_token_secret);

        // パラメータ([oauth_signature]を除く)を連想配列で指定
        $params = array(
            "oauth_consumer_key" => api_key,
            "oauth_token" => $_GET["oauth_token"],
            "oauth_signature_method" => "HMAC-SHA1",
            "oauth_timestamp" => time(),
            "oauth_verifier" => $_GET["oauth_verifier"],
            "oauth_nonce" => microtime(),
            "oauth_version" => "1.0",
        );

        // 配列の各パラメータの値をURLエンコード
        foreach ($params as $key => $value) {
            $params[$key] = rawurlencode($value);
        }

        // 連想配列をアルファベット順に並び替え
        ksort($params);

        // パラメータの連想配列を[キー=値&キー=値...]の文字列に変換
        $request_params = http_build_query($params, "", "&");

        // 変換した文字列をURLエンコードする
        $request_params = rawurlencode($request_params);

        // リクエストメソッドをURLエンコードする
        $encoded_request_method = rawurlencode($request_method);

        // リクエストURLをURLエンコードする
        $encoded_request_url = rawurlencode($request_url);

        // リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
        $signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params;

        // キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
        $hash = hash_hmac("sha1", $signature_data, $signature_key, TRUE);

        // base64エンコードして、署名[$signature]が完成する
        $signature = base64_encode($hash);
        // パラメータの連想配列、[$params]に、作成した署名を加える
        $params["oauth_signature"] = $signature;

        // パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
        $header_params = http_build_query($params, "", ",");

        // リクエスト用のコンテキストを作成する
        $context = array(
            "http" => array(
                "method" => $request_method,    //リクエストメソッド
                "header" => array(    //カスタムヘッダー
                    "Authorization: OAuth " . $header_params,
                ),
            ),
        );

        // cURLを使ってリクエスト
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context["http"]["method"]);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $context["http"]["header"]);    // ヘッダー
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);    // タイムアウトの秒数
        $res1 = curl_exec($curl);
        $res2 = curl_getinfo($curl);
        curl_close($curl);

        // 取得したデータ
        $response = substr($res1, $res2["header_size"]);    // 取得したデータ(JSONなど)
        //$responseの内容(文字列)を$query(配列)に直す
        // aaa=AAA&bbb=BBB → [ "aaa"=>"AAA", "bbb"=>"BBB" ]
        $query = [];
        parse_str($response, $query);

        //値を返す
        return $query;
    }

    public function getuser($TWaccess_token, $TWaccess_token_secret)
    {
        //echo $access_token . $access_token_secret;
        $request_url = 'https://api.twitter.com/1.1/account/verify_credentials.json';        // エンドポイント
        $request_method = 'GET';
        // パラメータA (オプション)
        $params_a = array();

        // キーを作成する (URLエンコードする)
        $signature_key = rawurlencode(api_secret) . '&' . rawurlencode($TWaccess_token_secret);

        // パラメータB (署名の材料用)
        $params_b = array(
            'oauth_token' => $TWaccess_token,
            'oauth_consumer_key' => api_key,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => microtime(),
            'oauth_version' => '1.0',
        );

        // パラメータAとパラメータBを合成してパラメータCを作る
        $params_c = array_merge($params_a, $params_b);

        // 連想配列をアルファベット順に並び替える
        ksort($params_c);
        // パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
        $request_params = http_build_query($params_c, '', '&');

        // 一部の文字列をフォロー
        $request_params = str_replace(array('+', '%7E'), array('%20', '~'), $request_params);

        // 変換した文字列をURLエンコードする
        $request_params = rawurlencode($request_params);

        // リクエストメソッドをURLエンコードする
        // ここでは、URL末尾の[?]以下は付けないこと
        $encoded_request_method = rawurlencode($request_method);

        // リクエストURLをURLエンコードする
        $encoded_request_url = rawurlencode($request_url);

        // リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
        $signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params;

        // キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
        $hash = hash_hmac('sha1', $signature_data, $signature_key, TRUE);

        // base64エンコードして、署名[$signature]が完成する
        $signature = base64_encode($hash);

        // パラメータの連想配列、[$params]に、作成した署名を加える
        $params_c['oauth_signature'] = $signature;

        // パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
        $header_params = http_build_query($params_c, '', ',');

        // リクエスト用のコンテキスト
        $context = array(
            'http' => array(
                'method' => $request_method, // リクエストメソッド
                'header' => array(              // ヘッダー
                    'Authorization: OAuth ' . $header_params,
                ),
            ),
        );

        // パラメータがある場合、URLの末尾に追加
        if ($params_a) {
            $request_url .= '?' . http_build_query($params_a);
        }

        // cURLを使ってリクエスト
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context['http']['method']);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $context['http']['header']);    // ヘッダー
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);    // タイムアウトの秒数
        $res1 = curl_exec($curl);
        $res2 = curl_getinfo($curl);
        curl_close($curl);

        // 取得したデータ
        $json = substr($res1, $res2['header_size']);        // 取得したデータ(JSONなど)

        // JSONをオブジェクトに変換
        $obj = json_decode($json, true);
        return $obj;
    }

    public function twpost($TWaccess_token, $TWaccess_token_secret)
    {
        if (!empty($_FILES)) {

            //サーバー上のtempファイル名を格納
            $TWfileName =  $_FILES['picture']['tmp_name'];
        }

        //まずはmedeliaアップロードして、画像付き投稿に必要なIDを取得する
        $request_url = 'https://upload.twitter.com/1.1/media/upload.json';        // エンドポイント
        $request_method = 'POST';

        // パラメータA (リクエストのオプション)
        $params_a = array(
            'media_data' => base64_encode(file_get_contents($TWfileName)),
        );

        // キーを作成する (URLエンコードする)
        $signature_key = rawurlencode(api_secret) . '&' . rawurlencode($TWaccess_token_secret);

        // パラメータB (署名の材料用)
        $params_b = array(
            'oauth_token' => $TWaccess_token,
            'oauth_consumer_key' => api_key,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => microtime(),
            'oauth_version' => '1.0',
        );

        // リクエストURLにより、メディアを指定するパラメータが違う
        switch ($request_url) {
            case ('https://api.twitter.com/1.1/account/update_profile_background_image.json'):
            case ('https://api.twitter.com/1.1/account/update_profile_image.json'):
                $media_param = 'image';
                break;

            case ('https://api.twitter.com/1.1/account/update_profile_banner.json'):
                $media_param = 'banner';
                break;

            case ('https://upload.twitter.com/1.1/media/upload.json'):
                $media_param = (isset($params_a['media']) && !empty($params_a['media'])) ? 'media' : 'media_data';
                break;
        }

        // イメージデータの取得
        $media_data = (isset($params_a[$media_param])) ? $params_a[$media_param] : '';

        // 署名の材料から、動画データを除外する
        if (isset($params_a[$media_param])) unset($params_a[$media_param]);

        // バウンダリーの定義
        $boundary = 's-y-n-c-e-r---------------' . md5(mt_rand());

        // POSTフィールドの作成 (まずはメディアのパラメータ)
        $request_body = '';
        $request_body .= '--' . $boundary . "\r\n";
        $request_body .= 'Content-Disposition: form-data; name="' . $media_param . '"; ';
        $request_body .= "\r\n";
        $request_body .= "\r\n" . $media_data . "\r\n";

        // POSTフィールドの作成 (その他のオプションパラメータ)
        foreach ($params_a as $key => $value) {
            $request_body .= '--' . $boundary . "\r\n";
            $request_body .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n";
            $request_body .= $value . "\r\n";
        }

        // リクエストボディの作成
        $request_body .= '--' . $boundary . '--' . "\r\n\r\n";

        // リクエストヘッダーの作成
        $request_header = "Content-Type: multipart/form-data; boundary=" . $boundary;

        // パラメータAとパラメータBを合成してパラメータCを作る → ×
        //	$params_c = array_merge( $params_a , $params_b ) ;
        $params_c = $params_b;        // 署名の材料にオプションパラメータを加えないこと

        // 連想配列をアルファベット順に並び替える
        ksort($params_c);

        // パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
        $request_params = http_build_query($params_c, '', '&');

        // 一部の文字列をフォロー
        $request_params = str_replace(array('+', '%7E'), array('%20', '~'), $request_params);

        // 変換した文字列をURLエンコードする
        $request_params = rawurlencode($request_params);

        // リクエストメソッドをURLエンコードする
        // ここでは、URL末尾の[?]以下は付けないこと
        $encoded_request_method = rawurlencode($request_method);

        // リクエストURLをURLエンコードする
        $encoded_request_url = rawurlencode($request_url);

        // リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
        $signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params;

        // キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
        $hash = hash_hmac('sha1', $signature_data, $signature_key, TRUE);

        // base64エンコードして、署名[$signature]が完成する
        $signature = base64_encode($hash);

        // パラメータの連想配列、[$params]に、作成した署名を加える
        $params_c['oauth_signature'] = $signature;

        // パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
        $header_params = http_build_query($params_c, '', ',');

        // リクエスト用のコンテキスト
        $context = array(
            'http' => array(
                'method' => $request_method,    // リクエストメソッド
                'header' => array(    // ヘッダー
                    'Authorization: OAuth ' . $header_params,
                    'Content-Type: multipart/form-data; boundary= ' . $boundary,
                ),
                'content' => $request_body,
            ),
        );

        // cURLを使ってリクエスト
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);    // リクエストURL
        curl_setopt($curl, CURLOPT_HEADER, true);    // ヘッダーを取得
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context['http']['method']);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $context['http']['header']);    // ヘッダー
        if (isset($context['http']['content']) && !empty($context['http']['content'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $context['http']['content']);    // リクエストボディ
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);    // タイムアウトの秒数
        $res1 = curl_exec($curl);
        $res2 = curl_getinfo($curl);
        curl_close($curl);

        // 取得したデータ
        $json = substr($res1, $res2['header_size']);    // 取得したデータ(JSONなど)
        $header = substr($res1, 0, $res2['header_size']);    // レスポンスヘッダー (検証に利用したい場合にどうぞ)

        $arr = json_decode($json, true);    // 配列に変換
        //$media_id_string = $arr['media_id_string']; //投稿に必要なmedia_id_stringを取得
        $media_id_string = $arr['media_id_string']; //投稿に必要なmedia_id_stringを取得

        ##//ここからは取得したmedia_id_stringを使って投稿
        $request_url = 'https://api.twitter.com/1.1/statuses/update.json';        // エンドポイント
        $request_method = 'POST';

        // パラメータA (リクエストのオプション)
        $params_a = array(
            'status' => $_POST['tweet'],
            'media_ids' => $media_id_string,    // 添付する画像のメディアID
        );

        // キーを作成する (URLエンコードする)
        $signature_key = rawurlencode(api_secret) . '&' . rawurlencode($TWaccess_token_secret);

        // パラメータB (署名の材料用)
        $params_b = array(
            'oauth_token' => $TWaccess_token,
            'oauth_consumer_key' => api_key,
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_nonce' => microtime(),
            'oauth_version' => '1.0',
        );

        // パラメータAとパラメータBを合成してパラメータCを作る
        $params_c = array_merge($params_a, $params_b);

        // 連想配列をアルファベット順に並び替える
        ksort($params_c);

        // パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
        $request_params = http_build_query($params_c, '', '&');

        // 一部の文字列をフォロー
        $request_params = str_replace(array('+', '%7E'), array('%20', '~'), $request_params);

        // 変換した文字列をURLエンコードする
        $request_params = rawurlencode($request_params);

        // リクエストメソッドをURLエンコードする
        // ここでは、URL末尾の[?]以下は付けないこと
        $encoded_request_method = rawurlencode($request_method);

        // リクエストURLをURLエンコードする
        $encoded_request_url = rawurlencode($request_url);

        // リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
        $signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params;

        // キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
        $hash = hash_hmac('sha1', $signature_data, $signature_key, TRUE);

        // base64エンコードして、署名[$signature]が完成する
        $signature = base64_encode($hash);

        // パラメータの連想配列、[$params]に、作成した署名を加える
        $params_c['oauth_signature'] = $signature;

        // パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
        $header_params = http_build_query($params_c, '', ',');

        // リクエスト用のコンテキスト
        $context = array(
            'http' => array(
                'method' => $request_method, // リクエストメソッド
                'header' => array(              // ヘッダー
                    'Authorization: OAuth ' . $header_params,
                ),
            ),
        );

        // オプションがある場合、コンテキストにPOSTフィールドを作成する
        if ($params_a) {
            $context['http']['content'] = http_build_query($params_a);
        }

        // cURLを使ってリクエスト
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);    // リクエストURL
        curl_setopt($curl, CURLOPT_HEADER, true);    // ヘッダーを取得
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context['http']['method']);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $context['http']['header']);    // ヘッダー
        if (isset($context['http']['content']) && !empty($context['http']['content'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $context['http']['content']);    // リクエストボディ
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);    // タイムアウトの秒数
        $res1 = curl_exec($curl);
        $res2 = curl_getinfo($curl);
        curl_close($curl);

        // 取得したデータ
        $json = substr($res1, $res2['header_size']);    // 取得したデータ(JSONなど)

        // JSONを変換
        // $obj = json_decode( $json ) ;	// オブジェクトに変換
        $arr = json_decode($json, true);   // 配列に変換
        //var_dump($arr);
        return $arr['created_at'];
    }
}
