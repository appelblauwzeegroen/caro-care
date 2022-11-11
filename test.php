<?php
/**
* Plugin Name: Caro Care UAT
* Description: Plugin show all units from sites managed by Caro Care
* Author: Christophe Demeulemeester
* Company: Appelblauwzeegroen bv
* Version: 0.1
* Last updated: 1st of August 2022
*/

/* Your code goes below here. */

add_shortcode( 'units_site', 'get_units_site');
function get_units_site(){
    $site=$_GET['site'];
    //if $site is empty, show error message: "No site selected" and stop function

    if (empty($site)){
        echo "No site selected";
        return;
    }else{
        $sql = "SELECT * FROM Rooms WHERE r_s_id = $site";
        $result = run_query_on_mysql($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "id: " . $row["r_id"]. " - Name: " . $row["r_label"]. "<br>";
            }
        } else {
            echo "No results found for this site";
        }
        return $result;
    }
}

add_shortcode('sites', 'open_view_sites');
function open_view_sites(){
    include 'views/sites_overview.html';
}
add_shortcode('all_sites', 'get_sites_from_db');
function get_sites_from_db(){
    echo 'hello website';
    $sql = "SELECT * FROM Sites";
    $result = run_query_on_mysql($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $server = $_SERVER['SERVER_NAME'];
            $siteUrl = "http://$server/site/?s=" . $row["s_id"];
            $card_list_item = $card_list_item."<li class='card-list-item'> <a href='".$siteUrl."'><div class='card'><div class='card-image'><img alt= '' class='logos' src='";
			$card_list_item = $card_list_item.$row["s_image_url"];
			$card_list_item = $card_list_item."' data-image></div><div class='card-content'><h3 class='card-heading'>";
			$card_list_item = $card_list_item.$row["s_name"];
			$card_list_item = $card_list_item."</h3><article>";
			$card_list_item = $card_list_item."<i class='fa-solid fa-location-dot'></i> ".$row["s_location"];
			$card_list_item = $card_list_item."</article></div></div></a></li>";
        }
    } else {
        echo "No results found";
    }
    return $result;
}

function run_query_on_mysql($sql){
    $servername = "caro-uat-db-4xj75-mysql.external.kinsta.app:31110";
    $username = "qCshgpeUbTJs6tQe";
    $password = "4xfPjc0pemri6AQi";
    $dbname = "CARO_UAT_DB";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $result = $conn->query($sql);
    return $result;
    $conn->close();
}

/* Your code goes above here. */