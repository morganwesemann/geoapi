<?php
/******* OWNER *******/

//GET request for list owner
Flight::route('GET /api/owner/list', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $stmt = $connection->prepare('SELECT * FROM owner');
    $stmt->execute();
    $row_count = $stmt->rowCount();

    $results = array();
    $results['data'] = array();

    if ($row_count != 0) {
    	$count = 0;
    	$results['error'] = false;
    	$results['message'] = "Successful Owners Retrieval";
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
    	Flight::json(array('error' => True, 'message' => 'No owners to list.'));
    }

    
});

//GET request for search owner
Flight::route('GET /api/owner/search/(@term)', function($term){
    //get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $sql = "SELECT * FROM owner where
    id LIKE ? OR
    type LIKE ? OR
    name LIKE ?
    ";

    $values = array();

    $attrNames = array('id','type','name');

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
        $results['message'] = "Successful owners Retrieval";
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
        $results['message'] = "No owners to list.";

        Flight::json($results);
    }

    
});

//GET request for owner by ID
Flight::route('GET /api/owner/@id:[0-9]+', function($id){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //prepare the statement and execute
    $stmt = $connection->prepare('SELECT * FROM owner where id=?');
    $stmt->execute(array($id));
    $row_count = $stmt->rowCount();

    if ($row_count == 1) {
    	$row = $stmt->fetch(PDO::FETCH_ASSOC);
    	Flight::json($row);
    } else {
    	Flight::json(array('error' => True, 'message' => 'No owners found for id ' .$id));
    }

    
});

//POST insert new owner
Flight::route('POST /api/owner', function(){
	//get the instance of the PDO connection
    $connection = Flight::db();

    //check that each attribute required is set and not empty
	if (isset($_POST['name']) && !empty($_POST['name'])) {

		if (isset($_POST['type']) && !empty($_POST['type'])) {

			//prepare and execute the statement
			$stmt = $connection->prepare('INSERT INTO owner (name,type) VALUES (?,?)');
        	$stmt->execute(array($_POST['name'],$_POST['type']));

        	//check if successful
        	if ($stmt) {
        		Flight::json(array('error' => False));
       		} else {
        		Flight::json(array('error' => True));
        	}
		}
		
	}
    
});

//PUT update owner
Flight::route('PUT /api/owner/@id:[0-9]+', function($id){
    //get the instance of the PDO connection
    $connection = Flight::db();

    //load PUT vars
    parse_str(file_get_contents("php://input"),$put_vars);

    //check that each attribute required is set and not empty

    if (isset($put_vars['name']) && !empty($put_vars['name'])) {

        if (isset($put_vars['type']) && !empty($put_vars['type'])) {

            //prepare and execute the statement
            $stmt = $connection->prepare('UPDATE owner SET name=?, type=? WHERE id=?');
            $stmt->execute(array($put_vars['name'],$put_vars['type'],$id));

            //check if successful
            if ($stmt) {
                Flight::json(array('error' => False));
            } else {
                Flight::json(array('error' => True));
            }
        } else {
            Flight::json(array('error' => True, 'message' => "No type set"));
        }
        
    } else {
        Flight::json(array('error' => True, 'message' => "No name set"));
    }
    
});

//DELETE owner
Flight::route('DELETE /api/owner/@id:[0-9]+', function($id){
    //get the instance of the PDO connection
    $connection = Flight::db();

     //prepare and execute the statement
    $stmt = $connection->prepare('DELETE FROM owner WHERE id=?');
    $stmt->execute(array($id));

    //check if successful
    if ($stmt) {
        Flight::json(array('error' => False));
    } else {
        Flight::json(array('error' => True));
    }
});

?>