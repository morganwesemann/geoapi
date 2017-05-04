<?php

//Flight API
require 'flight/Flight.php';

//Database setup
include 'db.php';

//Owner API calls
include 'owner.php';

//Well API calls
include 'well.php';

//Transducer API calls
include 'transducer.php';

//Transducer recording API calls
include 'recording.php';

//Rainfall API calls
include 'rainfall.php';


//API Main page
Flight::route('/', function(){
    echo '{ hello: world!}';
});

//404 page
Flight::map('notFound', function(){
    echo 'Error 404. The requested page could not be found.';
});

Flight::start();

?>
