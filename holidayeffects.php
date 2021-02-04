<?php
ini_set('display_errors', TRUE);
error_reporting(E_ALL);
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require 'vendor/autoload.php';
use sandeepshetty\shopify_api;
require 'conn-shopify.php';
require 'help.php';

if (isset($_GET["action"])) {
    $action = $_GET["action"];
    $shop = $_GET["shop"];
    $version = 8; //6
    if ($action == "getHolidayEvents") {
        $events = getAllEvents($db,$shop);
        $expired = checkExpired($shop, $appId, $trialTime);
        $shop_data = db_fetch_row("select * from tbl_usersettings where store_name = '$shop' and app_id = $appId");
        if(isset($shop_data['installed_date'])) {
            $installed = true;
        } else {
            $installed = false;
        }
        $response = array(
            "events" => $events,
            "expired" => $expired,
            "install"  => $installed,
            "ver" => $version
        );
        echo json_encode($response);		
    } 
}
function checkEventDate($startDate,$endDate) {
    $now = date("Y-m-d H:i:s");
    $start = strtotime($now) - strtotime($startDate);
    $end = strtotime($endDate) - strtotime($now);
    if($start > 0 && $end > 0){
        return true;
    } else {
        return false;
    }
}
function getAllEvents($db, $shop) {
    $sql = "SELECT * FROM holiday_events_effects WHERE shop = '$shop' and publish = '1'";
    $query = $db->query($sql);
    $lists = array();
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            if(checkEventDate($row["start_date"],$row["end_date"])){
                $row["choose_icons"] = json_decode($row["choose_icons"]);
                if(!is_array($row["choose_icons"])) {
                    $row["choose_icons"] = array();
                }
                $row["choose_images"] = json_decode($row["choose_images"]);
                if(!is_array($row["choose_images"])) {
                    $row["choose_images"] = array();
                }
                $row["custom_images"] = json_decode($row["custom_images"]);
                if(!is_array($row["custom_images"])) {
                    $row["custom_images"] = array();
                }
                $lists[] = $row;
            }
        }
    }
    return $lists;
}
function checkExpired($shop, $appId, $trialTime) {
    $shop_data = db_fetch_row("select * from tbl_usersettings where store_name = '$shop' and app_id = $appId");
    if(isset($shop_data['installed_date'])) {
        $installedDate = $shop_data['installed_date'];
        $clientStatus = $shop_data['status'];

        $date1 = new DateTime($installedDate);
        $date2 = new DateTime("now");
        $interval = date_diff($date1, $date2);
        $diff = (int)$interval->format('%R%a');
        if($diff > $trialTime && $clientStatus != 'active'){
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}