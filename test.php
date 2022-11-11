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
    $site=$_GET['sid'];
    //if $site is empty, show error message: "No site selected" and stop function

    if (empty($site)){
        print_r("No site selected");
        return;
    }else{
        $sql = "SELECT * FROM Rooms WHERE r_s_id = $site";
        $result = run_query_on_mysql($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                print_r("id: " . $row["r_id"]. " - Name: " . $row["r_label"]. "<br>");
            }
        } else {
            print_r("No results found for this site");
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
    $searchTerm = $_GET['search'];
    $server = $_SERVER['SERVER_NAME'];
    //if $searchTerm is empty, show all sites
    if (empty($searchTerm)){
        $sql = "SELECT * FROM Sites";
    }else{
        //filter on search term for s_name and s_location
        $sql = "SELECT * FROM Sites WHERE s_name LIKE '%$searchTerm%' OR s_location LIKE '%$searchTerm%'";
    }
    $result = run_query_on_mysql($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row["s_image_url"] == ""){
                $imagelink = "http://carocareuat.kinsta.cloud/wp-content/uploads/2022/11/images-1.png";
            }else{
                $imagelink = $row["s_image_url"];
            }
            $siteUrl = "http://".$server."/sitedetails/?sid=" . $row["s_id"];
            $card_list_item = $card_list_item."<li class='card-list-item'> <a href='".$siteUrl."'><div class='card'><div class='card-image'><img alt= '' class='logos' src='";
			$card_list_item = $card_list_item.$imagelink;
			$card_list_item = $card_list_item."' data-image></div><div class='card-content'><h3 class='card-heading'>";
			$card_list_item = $card_list_item.$row["s_name"];
			$card_list_item = $card_list_item."</h3><article>";
			$card_list_item = $card_list_item."<i class='fa-solid fa-location-dot'></i> ".$row["s_location"];
			$card_list_item = $card_list_item."</article></div></div></a></li>";
        }
    } else {
        echo "No results found";
    }
    return $card_list_item;
}
add_shortcode('site_details_page', 'get_site_details');
function get_site_details(){
    include "views/site_details.html";
}
add_shortcode('get_site_detail_title', 'get_base_site_info');
function get_base_site_info(){
    $siteID = $_GET['sid'];
    if(empty($siteID)){
        echo "No site selected";
        return;
    }else{
        $sql = "SELECT * FROM Sites WHERE s_id = $siteID";
        $result = run_query_on_mysql($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $siteName = $row["s_name"];
                $siteLocation = $row["s_location"];
            }
        } else {
            echo "No results found";
        }
        return $siteName." - ".$siteLocation. " (10.".$siteID.".x.x)";
    } 
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