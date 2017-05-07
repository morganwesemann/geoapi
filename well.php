<?php
//GET request for list well
Flight::route('GET /api/well/list', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $stmt = $connection->prepare('SELECT * FROM well');
    $stmt->execute();
    $row_count = $stmt->rowCount();

    $results = array();
    $results['data'] = array();

    if ($row_count != 0) {
    	$count = 0;
    	$results['error'] = false;
    	$results['message'] = "Successful wells Retrieval";
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
    	Flight::json(array('error' => True, 'message' => 'No wells to list.'));
    }

    
});

//GET request for search well
Flight::route('GET /api/well/search/(@term)', function($term){
    //get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $sql = "SELECT * FROM well where
    id LIKE ? OR
    acquiferCode LIKE ? OR
    typeCode LIKE ? OR
    latitude LIKE ? OR
    longitude LIKE ? OR
    county LIKE ? OR
    state LIKE ? OR
    depth LIKE ? OR
    usageType LIKE ? OR
    pump LIKE ? OR
    bottomElevation LIKE ? OR
    waterElevation LIKE ? OR
    surfaceElevation LIKE ? OR
    ownerID LIKE ? OR
    casingID LIKE ? OR
    topDepth LIKE ? OR
    diameter LIKE ? OR
    topDepth LIKE ? OR
    bottomDepth LIKE ? OR
    additionalText LIKE ?
    
    ";

    $values = array();

    $attrNames = array('id','acquiferCode','typeCode','latitude','longitude','county','state','depth','usageType','pump','bottomElevation','waterElevation','surfaceElevation', 'ownerID','casingID','topDepth','diameter','topDepth','bottomDepth','additionalText');

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
        $results['message'] = "Successful wells Retrieval";
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
        $results['message'] = "No wells to list.";

        Flight::json($results);
    }

    
});

//GET request for well by ID 
Flight::route('GET /api/well/@id:[0-9A-Za-z-]+/@aCode:[A-Za-z-]+/@type:[0-9A-Za-z-]/@ownerID:[0-9A-Za-z-]+', function($id, $aCode, $type, $ownerID){

    $sql = "SELECT * FROM well where";
    $values = array();
    $idNull = false;
    if ($id === "-") {
        $idNull = true;

    } else {
        $sql .= " id=?";
        array_push($values,$id);
    }

    $aCodeNull = false;
    if ($aCode === "-") {
        $aCodeNull = true;
    } else {
        $sql .= "AND acquiferCode=?";
        array_push($values,$aCode);
    }

    $typeNull = false;
    if ($type === "-") {
        $typeNull = true;
    } else {
        $sql .= "AND typeCode=?";
        array_push($values,$type);
    }

    $ownerNull = false;
    if ($ownerID === "-") {
        $ownerNull = true;

    } else {
        $sql .= "AND ownerID=?";
        array_push($values,$ownerID);
    }

    if ($idNull && $aCodeNull && $typeNull && $ownerNull) {
        Flight::json(array('error' => True, 'message' => 'No attributes specified' .$id));
    } else {
        //get the instance of the PDO connection
        $connection = Flight::db();

        //prepare the statement and execute
        $stmt = $connection->prepare($sql);
        $stmt->execute($values);
        $row_count = $stmt->rowCount();

        if ($row_count == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            Flight::json($row);
        } else {
            Flight::json(array('error' => True, 'message' => 'No well found for id ' .$id));
        }
    }

    
});

//POST insert new well
Flight::route('POST /api/well', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    $attrNames = array('id','acquiferCode','typeCode','latitude','longitude','county','state','depth','usageType','pump','bottomElevation','waterElevation','surfaceElevation');
    $optAttrNames = array('ownerID','casingID','topDepth','diameter','topDepth','bottomDepth','additionalText');

    $badDataAttrs = array();

    //check that all the required attributes are present
    $badDataInput = false;
    foreach($attrNames as $value) {
        if (!isset($_POST[$value]) || empty($_POST[$value])) {
            $badDataInput = true;
            array_push($badDataAttrs,$value);
        }
    }

    //if a pump is set, we need a pump description
    if (!$badDataInput && $_POST['pump'] == "yes") {
        if (!isset($_POST['pumpDescr']) || empty($_POST['pumpDescr'])) {
            $badDataInput = true;
            array_push($badDataAttrs,'pumpDescr');
        } else {
            array_push($attrNames,'pumpDescr'); 
        }
        
    }

    //check the optional attributes
    foreach($optAttrNames as $value) {
        if (isset($_POST[$value]) && !empty($_POST[$value])) {
            array_push($attrNames,$value); 
        }
    }


    if ($badDataInput) {
        $jsonToReturn = array('error' => true);
        $jsonToReturn['missing'] = $badDataAttrs;
        Flight::json($jsonToReturn);
    } else {
        $sql = "INSERT INTO well (";
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
Flight::route('PUT /api/well/@id:[0-9A-Za-z]+', function($id){


    //get the instance of the PDO connection
    $connection = Flight::db();

    //get the put vars
    parse_str(file_get_contents("php://input"),$put_vars);

    $attrNames = array();

    $optAttrNames = array('acquiferCode','typeCode','ownerID','latitude','longitude','county','state','depth','usageType','pump','bottomElevation','waterElevation','surfaceElevation','casingID','topDepth','diameter','topDepth','bottomDepth','additionalText');

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
        $sql = "UPDATE well SET ";
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

//DELETE well
Flight::route('DELETE /api/well/@id:[0-9A-ZA-z]+', function($id){
    //get the instance of the PDO connection
    $connection = Flight::db();

     //prepare and execute the statement
    $stmt = $connection->prepare('DELETE FROM well WHERE id=?');
    $stmt->execute(array($id));

    //check if successful
    if ($stmt) {
        Flight::json(array('error' => False));
    } else {
        Flight::json(array('error' => True));
    }
});

?>