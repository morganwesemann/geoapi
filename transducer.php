<?php

//GET request for list transducer
Flight::route('GET /api/transducer/list', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $stmt = $connection->prepare('SELECT * FROM transducer');
    $stmt->execute();
    $row_count = $stmt->rowCount();

    $results = array();
    $results['data'] = array();

    if ($row_count != 0) {
    	$count = 0;
    	$results['error'] = false;
    	$results['message'] = "Successful transducers Retrieval";
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
    	Flight::json(array('error' => True, 'message' => 'No transducers to list.'));
    }

    
}); 

//GET request for search transducer
Flight::route('GET /api/transducer/search/(@term)', function($term){
    //get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $sql = "SELECT * FROM transducer WHERE
    id LIKE ? OR
    type LIKE ? OR
    name LIKE ? OR
    wellID LIKE ?
    ";

    $values = array();

    $attrNames = array('id','type','name','wellID');

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
        $results['message'] = "Successful transducers Retrieval";
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
        $results['message'] = "No transducers to list.";

        Flight::json($results);
    }

    
});

//GET request for transducer by wellID 
Flight::route('GET /api/transducer/well/@id:[0-9A-Za-z]+', function($id){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $stmt = $connection->prepare('SELECT * FROM transducer where wellID=?');
    $stmt->execute(array($id));
    $row_count = $stmt->rowCount();

    if ($row_count == 1) {
    	$row = $stmt->fetch(PDO::FETCH_ASSOC);
    	Flight::json($row);
    } else {
    	Flight::json(array('error' => True, 'message' => 'No transducers found for wellID: ' .$id));
    }

    
});

//POST insert new transducer
Flight::route('POST /api/transducer', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    $attrNames = array('id','type','name','wellID');

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
        $sql = "INSERT INTO transducer (";
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

//PUT update well
Flight::route('PUT /api/transducer/@id:[0-9A-Za-z]+', function($id){


    //get the instance of the PDO connection
    $connection = Flight::db();

    //get the put vars
    parse_str(file_get_contents("php://input"),$put_vars);

    $attrNames = array();

    $optAttrNames = array('type','name','wellID');

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
        $sql = "UPDATE transducer SET ";
        $valSql = " WHERE id=?";

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
Flight::route('DELETE /api/transducer/@id:[0-9A-ZA-z]+', function($id){
    //get the instance of the PDO connection
    $connection = Flight::db();

     //prepare and execute the statement
    $stmt = $connection->prepare('DELETE FROM transducer WHERE id=?');
    $stmt->execute(array($id));

    //check if successful
    if ($stmt) {
        Flight::json(array('error' => False));
    } else {
        Flight::json(array('error' => True));
    }
});

?>