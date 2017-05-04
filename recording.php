<?php
//GET request for list recordings
Flight::route('GET /api/recording/list', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $stmt = $connection->prepare('SELECT * FROM recordings');
    $stmt->execute();
    $row_count = $stmt->rowCount();

    $results = array();
    $results['data'] = array();

    if ($row_count != 0) {
    	$count = 0;
    	$results['error'] = false;
    	$results['message'] = "Successful recording data Retrieval";
    	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    		$count += 1;
    		$arr = array();
    		foreach($row as $key => $value) {
    			$arr[$key] = $value;
    		}
    		array_push($results['data'],$arr);
    	}
    	Flight::json($results);
    } else {
    	Flight::json(array('error' => True, 'message' => 'No recording data to list.'));
    }
});

//GET request for search recording
Flight::route('GET /api/recording/search/(@term)', function($term){
    //get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $sql = "SELECT * FROM recordings WHERE
    transducerID LIKE ? OR
    type LIKE ? OR
    value LIKE ? OR
    date LIKE ?
    ";

    $values = array();

    $attrNames = array('transducerID','type','value','date');

    foreach($attrNames as $value) {
        array_push($values,'%'.$term.'%');
    }

    $stmt = $connection->prepare($sql);
    $stmt->execute($values);
    $row_count = $stmt->rowCount();

    $results = array();
    $results['data'] = array();

    if ($row_count != 0) {
        $count = 0;
        $results['error'] = false;
        $results['message'] = "Successful recording data Retrieval";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $count += 1;
            $arr = array();
            foreach($row as $key => $value) {
                $arr[$key] = $value;
            }
            array_push($results['data'],$arr);
        }
        Flight::json($results);
    } else {
        $results['error'] = false;
        $results['message'] = "No recording data to list.";

        Flight::json($results);
    }

    
});


//POST insert new recording
Flight::route('POST /api/recording', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    $attrNames = array('transducerID','type','value','date');

    $badDataAttrs = array();

    //check that all the required attributes are present
    $badDataInput = false;
    foreach($attrNames as $value) {
        if (!isset($_POST[$value]) || empty($_POST[$value])) {
            $badDataInput = true;
            array_push($badDataAttrs,$value);
        }
    }

    if ($badDataInput) {
        $jsonToReturn = array('error' => true);
        $jsonToReturn['missing'] = $badDataAttrs;
        Flight::json($jsonToReturn);
    } else {
        $sql = "INSERT INTO recordings (";
        $valSql = ") VALUES (";

        $len = count($attrNames);
        $count = 0;
        $values = array();
        foreach($attrNames as $value) {
            $sql .= $value;
            $valSql .= "?";
            if ($count < $len - 1) {
                $sql .= ",";
                $valSql .= ",";
            }
            array_push($values,$_POST[$value]);
            $count += 1;
        }

        $sql = $sql . $valSql . ")";
        //echo $sql;
        try {
            $stmt = $connection->prepare($sql);
            $stmt->execute($values);
        } catch (PDOException $exception) {
            Flight::json(array('error' => True, 'message' => $exception->getMessage()));
        }
        

        //check if successful
        if ($stmt) {
            Flight::json(array('error' => False));
        }

    }

    //output form data entered for testing
    /*foreach($attrNames as $value) {
        echo $value . ": " . $_POST[$value] . "\n";
    }*/
    
});

//PUT update recording
Flight::route('PUT /api/recording/@id:[0-9A-Za-z]+', function($id){


    //get the instance of the PDO connection
    $connection = Flight::db();

    //get the put vars
    parse_str(file_get_contents("php://input"),$put_vars);

    $date = $put_vars['date'];

    $attrNames = array();

    $optAttrNames = array('type','value');

    $badDataAttrs = array();

    //check that all the required attributes are present
    $badDataInput = false;

    //check the optional attributes
    foreach($optAttrNames as $value) {
        if (isset($put_vars[$value]) && !empty($put_vars[$value])) {
            array_push($attrNames,$value); 
        }
        
    }


    if (count($attrNames) == 0) {
        $jsonToReturn = array('error' => true);
        $jsonToReturn['message'] = "No attributes to update specified";
        Flight::json($jsonToReturn);
    } else {
        $sql = "UPDATE recordings SET ";
        $valSql = " WHERE id=? AND date=?";

        $len = count($attrNames);
        $count = 0;
        $values = array();
        foreach($attrNames as $value) {
            $sql .= $value . "=?";
            
            if ($count < $len - 1) {
                $sql .= ",";
               
            }
            array_push($values,$put_vars[$value]);
            $count += 1;
        }

        array_push($values,$id);
        array_push($values,$date);

        $sql = $sql . $valSql;
        //echo $sql;

        try {
            $stmt = $connection->prepare($sql);
            $stmt->execute($values);
        } catch (PDOException $exception) {
            Flight::json(array('error' => True, 'message' => $exception->getMessage()));
        }
        

        //check if successful
        if ($stmt) {
            $rowsAffected = $stmt->rowCount();
            if ($rowsAffected == 0) {
                Flight::json(array('error' => True, 'message' => "No rows affected. Check that ID exists in table"));
            } else {
                Flight::json(array('error' => False));
            }
            
        }
        
    }    
    
});

//DELETE transducer
Flight::route('DELETE /api/recording/@id:[0-9]+', function($id){
    //get the instance of the PDO connection
    $connection = Flight::db();

     parse_str(file_get_contents("php://input"),$delete_vars);

     //prepare and execute the statement
    $stmt = $connection->prepare('DELETE FROM recordings WHERE id=? AND date=?');
    $stmt->execute(array($id,$delete_vars['date']));

    //check if successful
    if ($stmt) {
        Flight::json(array('error' => False));
    } else {
        Flight::json(array('error' => True));
    }
});


?>