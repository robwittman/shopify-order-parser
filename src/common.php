<?php

if (!function_exists("callShopify")) {
    function callShopify($auth, $url, $method = 'GET', $params = array())
    {
        $base = generateUrl($auth);

        $c = curl_init();
        if ($method == "GET") {
            $url = $url . "?" . http_build_query($params);
        } elseif ($method == "POST") {
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($params));
        } else {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($params));
        }
        curl_setopt($c, CURLOPT_URL, $base.$url);
        error_log($base.$url);
        curl_setopt($c, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($c);
        $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        if(!in_array($code, [200,201])) {
            error_log($res);
            throw new \Exception("Shopify API response error. [$code] [$res]");
        }
        return json_decode($res);
    }
}

if (!function_exists("generateUrl")) {
    function generateUrl($auth)
    {
        $key = $auth->api_key;
        $pass = $auth->password;
        $domain = $auth->myshopify_domain;
        return sprintf("https://%s:%s@%s", $key, $pass, $domain);
    }
}

if (!function_exists("checkLogin")) {
    function checkLogin($request, $response, $next)
    {
        if (isset($_SESSION['logged_in']) && $_SESSION['expiration'] > time()) {
            return $next($request, $response);
        }
        session_destroy();
        return $response->withRedirect('/login');
    }
}

if (!function_exists("writeLog")) {
    function writeLog($message)
    {
        return true;
        // error_log($message);
        // if (getenv("ENV") == "dev") {
        //     error_log($message);
        // }
    }
}
