<?php
include 'functions.php';
$useragent = $_GET['user_agent'];
$user = $_GET['u'];
$pass = $_GET['p'];

$cnf = array(
    'email' => $user,
    'pass' =>  $pass
);


//Login
$cnf['login'] = 'Login';
$random = md5(rand(00000000,99999999)).'.txt';
$login = cURL_iOS('https://m.facebook.com/login.php', false, $cnf,$useragent);
//print $login;
$dom = new DOMDocument();

if(preg_match('/name="fb_dtsg" value="(.*?)"/', $random, $response)){
    $fb_dtsg = $response[1];
    $responseToken = cURL_iOS('https://www.facebook.com/v1.0/dialog/oauth/confirm?', $random, 'fb_dtsg='.$fb_dtsg.'&app_id=165907476854626&redirect_uri=fbconnect://success&display=popup&access_token=&sdk=&from_post=1&private=&tos=&login=&read=&write=&extended=&social_confirm=&confirm=&seen_scopes=&auth_type=&auth_token=&auth_nonce=&default_audience=&ref=Default&return_format=access_token&domain=&sso_device=ios&__CONFIRM__=1',$useragent);
    $data = json_decode($responseToken,true);
    if(preg_match('/access_token=(.*?)&/', $responseToken, $token2))
    {
        $token['access_token'] = $token2[1];
        exit(json_encode($token));
    }
    else
    {
        // Render HTML to get error
        $error = $dom->loadHTML($responseToken);
        $dom->validateOnParse = true;
        $nodes = array();
        $nodes = $dom->getElementsByTagName("input");
        foreach ($nodes as $element)
        {
            $attr = $element->getAttribute("name");
            if (strpos($attr, "error") >= 0)
            {
                $error_msg = $element->getAttribute('value');
            }
        }
        $token['error_msg'] = "Lỗi Facebook:  ".json_decode(utf8_decode($error_msg),true)['error_message'];
        # $token['error_msg'] = 'Tài khoản bị check point';
        exit(json_encode($token));
    }
}
else{
    $token['error_msg'] = 'Sai tài khoản,mật khẩu hoặc nick bị checkpoint';
    exit(json_encode($token));
}
unlink($random);
?>
