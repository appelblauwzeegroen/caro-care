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

add_shortcode( 'units_waregem', 'get_units_site_waregem' );
function get_units_site_waregem(){
    $sql = "SELECT * FROM Rooms WHERE r_s_id = '20'";
    $result = run_query_on_mysql($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["r_id"]. " - Name: " . $row["r_label"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    return $result;
}

add_shortcode('sites_from_db', 'get_sites_from_db');
function get_sites_from_db(){
    $sql = "SELECT * FROM Sites";
    $result = run_query_on_mysql($sql);
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