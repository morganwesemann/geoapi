<?php

//The base URL of the site. Notice the lack of trailing slash!
const BASE_URL = "http://localhost:8888/geoapi";

//Host of database
const DB_HOST = "localhost";

//Database username 
const DB_USER = "root";

//Database password
const DB_PASS = "root";

//Database name
const DB_NAME = "geoapi";

//Are we in test mode?
const TESTING_MODE = false;

//register the connection to our database with flight 
//so we can use it in the routing functions
Flight::register('db', 'PDO', array('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS), function($db){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
});

?>