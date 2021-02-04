<?php
ini_set('display_errors', TRUE);
error_reporting(E_ALL);
require 'vendor/autoload.php';
use sandeepshetty\shopify_api;
require 'conn-shopify.php';

function show_array($data) {
    if (is_array($data)) {
        echo "<pre>";
        print_r($data); 
        echo "</pre>"; 
    }
}

function db_query($query_string) {
    global $db;
    $result = mysqli_query($db, $query_string);
    if (!$result) {
         echo ('Query Error' . $query_string);
    }
    return $result;
}

function redirect($data) {
    header("Location: $data");
}
function deleteDataCache($dir){
    if (is_dir($dir)) {
        $structure = glob(rtrim($dir, "/").'/*');
        if (is_array($structure)) {
            foreach($structure as $file) {
                if (is_dir($file)) recursiveRemove($file);
                else if (is_file($file)) @unlink($file);
            }
        }
        rmdir($dir);
    }
}
function getCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
    $response = curl_exec($ch);
    if ($response === false) {
        $api_response = curl_error($ch);
    } else {
        $api_response = $response;
    }
    curl_close($ch);
    return $api_response; 
}

function valaditon_get($data) {
    if ($data) {
        return $data;
    } else {
        $data = "";
        return $data;
    }
}

function result_fetch_object($data) {
    $result = $data->fetch_object();
    return $result;
}

function fetchDbObject ($sql) {
    global $db;
    global $shop;
    $query = $db->query($sql);
    $object = array();
    if ($query && mysqli_num_rows($query) > 0) {
        while ($row = $query->fetch_assoc()) {
            $object = $row;
        }
    }
    return $object;
}

function db_insert($table, $data) {
    global $db;
    $fields = "(" . implode(", ", array_keys($data)) . ")";
    $values = "";

    foreach ($data as $field => $value) {
        if ($value === NULL) {
            $values .= "NULL, ";
        // } elseif (is_numeric($value)) {
        //     $values .= $value . ", ";
        } elseif ($value == 'true' || $value == 'false') {
            $values .= $value . ", ";
        } else {
            $values .= "'" . addslashes($value) . "', ";
        }
    }
    $values = substr($values, 0, -2);
    db_query("
            INSERT INTO $table $fields
            VALUES($values)
        ");
    return mysqli_insert_id($db);
}

function db_update($table, $data, $where) {
    global $db;
    $sql = "";
    foreach ($data as $field => $value) {
        if ($value === NULL) {
            $sql .= "$field=NULL, ";
        } elseif (is_numeric($value)) {
            $sql .= "$field=" . addslashes($value) . ", ";
        } elseif ($value == 'true' || $value == 'false') {
            $sql .= "$field=" . addslashes($value) . ", ";
        } else
            $sql .= "$field='" . addslashes($value) . "', ";
    }
    $sql = substr($sql, 0, -2);
    db_query("
        UPDATE `$table`
        SET $sql
        WHERE $where
    ");
    return mysqli_affected_rows($db);
}

function db_duplicate($table,$data,$content_duplicate){
    global $db;
    $fields = "(" . implode(", ", array_keys($data)) . ")";
    $values = "(";
    foreach ($data as $field => $value) {
        if ($value === NULL)
            $values .= "NULL, ";
        elseif ($value === TRUE || $value === FALSE)
            $values .= "$value, ";
        else
            $values .= "'" . addslashes($value) . "',";
    }  
    $values = rtrim($values,',');
    $values .= ")";
    $query = "INSERT INTO $table  $fields  VALUES $values ON DUPLICATE KEY UPDATE $content_duplicate";
    db_query($query);
    return  mysqli_insert_id($db);  
}

function db_delete($table, $where) {
    global $db;
    $query_string = "DELETE FROM " . $table . " WHERE $where";
    db_query($query_string);
    return mysqli_affected_rows($db);
}

function db_fetch_array($query_string) {
    global $db;
    $result = array();
    $mysqli_result = db_query($query_string);
    if (!is_bool($mysqli_result)) {
        while ($row = mysqli_fetch_assoc($mysqli_result)) {
            $result[] = $row;
        }
        mysqli_free_result($mysqli_result);
    }
    return $result;
}

function db_fetch_row($query_string) {
    global $db;
    $result = array();
    $mysqli_result = db_query($query_string);
    $result = mysqli_fetch_assoc($mysqli_result);
    mysqli_free_result($mysqli_result);
    return $result;
}

function checkExistArray($array1, $array2) {
    if (is_array($array1) && is_array($array2)) {
        $check = array();
        foreach ($array1 as $v1) {
            array_push($check, $v1);
        }
        foreach ($array2 as $v2) {
            if (in_array($v2, $check)) {
                return $result = 1;
                break;
            } else {
                $result = 0;
            }
        }
    } else {
        return 0;
    }
    return $result;
}

// đo tốc độ thực thi
function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function cvf_convert_object_to_array($data) {
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    } else {
        return $data;
    }
}

function creatSlug($string, $plusString) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#',
        '#(ì|í|ị|ỉ|ĩ)#',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#',
        '#(đ)#',
        '#(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)#',
        '#(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)#',
        '#(Ì|Í|Ị|Ỉ|Ĩ)#',
        '#(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)#',
        '#(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)#',
        '#(Ỳ|Ý|Ỵ|Ỷ|Ỹ)#',
        '#(Đ)#',
        "/[^a-zA-Z0-9\-\_]/",
    );
    $replace = array(
        'a',
        'e',
        'i',
        'o',
        'u',
        'y',
        'd',
        'A',
        'E',
        'I',
        'O',
        'U',
        'Y',
        'D',
        '-',
    );
    $string = preg_replace($search, $replace, $string);
    $string = preg_replace('/(-)+/', '-', $string);
    $string = strtolower($string);
    return $string . $plusString;
}

// SHOPIFY
function deleteWebhook($shopify,$id){
    $result = $shopify("DELETE", "/admin/webhooks/".$id.".json");
    return $result;
}
function createWebhook($shopify,$link){
    $webhook = array(
        "webhook" => array(
            "topic" => "products/create",
            "address" => $link,
            "format" => "json"
        )
    ); 
    $result = $shopify("POST", "/admin/webhooks.json",$webhook);
    return $result;
}
function editWebhook($shopify,$link,$id){
    $webhook = array(
        "webhook" => array(
            "id"    => $id,
            "topic" => "products/create",
            "address" => $link,
            "format" => "json"
        )
    ); 
    $result = $shopify("PUT", "/admin/webhooks.json",$webhook);
    return $result;
}
function getListWebhook($shopify){
    $result = $shopify("GET", "/admin/webhooks.json");
    return $result;
}
function cleanString($text){
    $map = array(
        array("|", ""), 
        array("-", ""), 
        array(")", ""), 
        array("(", ""), 
        array(" ", ""), 
        array("&", ""), 
        array("'", ""), 
        array('"', ""), 
        array("/", ""), 
        array("[", ""),
        array("]", ""),
        array("\\", ""),
    );
    if (is_array($map)) {
        foreach ($map as $pair)
            $text = str_replace($pair[0], $pair[1], $text);
    }
    return $text;
}
function getnextday($num, $date){
	return date("Y-m-d H:i:s", strtotime("+" . $num . " day", strtotime($date)));
}
function getbackday($num, $date){
	return date("Y-m-d H:i:s", strtotime("-" . $num . " day", strtotime($date)));
}

function shopifyInit($db, $shop, $appId) {
    $select_settings = $db->query("SELECT * FROM tbl_appsettings WHERE id = $appId");
    $app_settings = $select_settings->fetch_object();
    $shop_data = $db->query("select * from tbl_usersettings where store_name = '" . $shop . "' and app_id = $appId");
    $shop_data = $shop_data->fetch_object();
    if(!isset($shop_data->access_token)){
        die("Please check the store: ".$shop." seems to be incorrect access_token.");
    }
    $shopify = shopify_api\client(
        $shop, $shop_data->access_token, $app_settings->api_key, $app_settings->shared_secret
    );
    return $shopify;
}

function getDataById($shopify,$id,$topic,$filter = null){
    $data = $shopify("GET","/admin/$topic/$id.json?".$filter);
    return $data;
}

function getMainThemeId($themes) {
    $id = "";
    if(is_array($themes) && count($themes) > 0) {
        foreach ($themes as $theme){
            if($theme["role"] == 'main') $id = $theme["id"];
        }
    }
    return $id;
}
function getMainThemeName($themes) {
    $name = "";
    if(is_array($themes) && count($themes) > 0) {
        foreach ($themes as $theme){
            if($theme["role"] == 'main') $name = $theme["name"];
        }
    }
    return $name;
}

function returnBooleanData($data){
    if($data == 'true') return 1;
    else return 0;
}

function FixSpecialChars($text){
    $map = array(array("\ufffd", ""),
            array("�", ""),
            array(" ", "_"),
            array("&", "_"),
            array("!", "_"),
            array("%", "_"),
            array("#", "_"),
            array("@", "_"),
            array("%", "_"),
            array("^", "_"),
            array("*", "_"),
            array("(", "_"),
            array(")", "_"),
            array("+", "_"),
            array("=", "_"),
            array("~", "_"),
            array("?", "_"),
            array("<", "_"),
            array(">", "_"),
            array("�", "")
            );
    if (is_array($map))
    {
        foreach ($map as $pair)
            $text = str_replace($pair[0], $pair[1], $text);
    }
    $text = stripUnicode($text);
    return $text;
}
function stripUnicode($str) {
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    return $str;
}

function saveScriptTagId($shop, $shopify, $appId, $table) {
    if(checkShopScriptTag($shop, $appId) == false){
        $data_insert = [
            'app_id'=> $appId,
            'shop'  => $shop,
        ];
        $id = db_insert('tbl_scripttag', $data_insert);
    }
    $scriptTags = $shopify("GET", "/admin/api/".apiVersion."/script_tags.json");
    $scriptTag = $scriptTags[0];
    $scriptTagId = $scriptTag["id"];
    $settings = fetchDbObject("SELECT * FROM $table WHERE shop = '$shop' AND app_id = $appId");
    $settings["script_tagid"] = $scriptTagId;
    $query = db_update($table, $settings, "shop = '$shop' and app_id = $appId");
    return $scriptTagId;
}

function updateScriptTag($shop, $shopify, $table, $clientJsUrl) {
    $date = new DateTime();
    $settings = fetchDbObject("SELECT script_tagid FROM $table WHERE shop = '$shop'");
    $newVersion = $date->format('ymdHis');
    $scriptTagId = $settings["script_tagid"];
    $updatedScriptTag = $shopify("PUT", "/admin/api/".apiVersion."/script_tags/$scriptTagId.json", [
        "script_tag"=> [
            "id" => $scriptTagId,
            "src" => $clientJsUrl . "?v=" .$newVersion
        ]
    ]);
    return $updatedScriptTag;
}

function checkShopScriptTag($shop, $appId) {
    $query = db_fetch_row("SELECT * FROM tbl_scripttag WHERE shop = '$shop' AND app_id = $appId");
    if (!empty($query)) {
        return true;
    } else {
        return false;
    }
}