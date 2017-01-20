<?php
$definedURL="http://".$_SERVER[SERVER_DOMAIN]."/api/task/";
DEFINE("URL",$definedURL);
class taskController{
	private $conn;
	public function __construct($conn) 
	{
		$this->conn = $conn;
		
	}

	public function set_headers(){
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');
		
		
	}	

	public function create_task(){
		
		$a=file_get_contents('php://input');
		$array=(array)json_decode($a);
		
		
		if(isset($array['name'])
				 && isset($array['descryption'])
				 && isset($array['start_date'])
				 && isset($array['end_date'])){
		
				$query="INSERT INTO `task` (`id`, `name`, `descryption`, `start_date`, `end_date`) VALUES (NULL, '".$array['name']."','".$array['descryption']."','".$array['start_date']."','".$array['end_date']."');";		
				$stmt = $this->conn->prepare($query);
				$result = $stmt->execute();
				$error = $stmt->error;
			
				if ($result) {				
						$id=$this->conn->insert_id;					
					$this->set_headers();var_dump(http_response_code(201)); $output = ob_get_clean();  return "{\"status\":201,\"result\":\"Task created successfully\",
				\"_self\":\"".URL.$id."\"}";}
				else {$this->set_headers(); var_dump(http_response_code(400)); return "{\"status\": 400,\"result\":\"".$error."\"}";}
		}
		else {$this->set_headers(); var_dump(http_response_code(400)); return "{\"status\": 400 }"; 
        }
	}
		
		
	public function get_task($param){
        if($param!="-1"&&is_numeric($param)){

                    $stmt = $this->conn->prepare("SELECT * FROM `task` WHERE id=".$param);
                    $result = $stmt->execute();
                    $task = $stmt->get_result();
                    $num_row = $task->num_rows;
                    $error = $stmt->error;
                    $stmt->close();
                    $rows = array();
                        if($num_row==NULL) {
                            $this->set_headers();
                            var_dump(http_response_code(404)); $output = ob_get_clean();
                            return "{\"status\": 404,\"result\":\"Not found\"}";
                            }

                            if($num_row==1){
                                while($r = mysqli_fetch_assoc($task)) {
                                    $rows[] = $r;
									$rows[0]["_links"]=array("_self" =>URL.$r["id"]);
                                }
                           $this->set_headers();
                           $rows=json_encode($rows);							
                           return $rows;
                            }
		}

		
	if($param==-1){

            $stmt = $this->conn->prepare("SELECT * FROM `task` WHERE 1");
            $result = $stmt->execute();
            $task = $stmt->get_result();
			$i=0;

            if($result) {
                while ($r = mysqli_fetch_assoc($task)) {
                    $rows[] = $r;
					$rows[$i]["_links"]=array("_self" =>URL.$r["id"]);
					$i++;
                }

                json_encode($rows);
                $this->set_headers();
                $rows = json_encode($rows);
                return $rows;
            }

				else {$this->set_headers(); var_dump(http_response_code(404)); return "{\"status\": 404,\"result\":\"Not found\"}";}


			}
        
	$this->set_headers();
	var_dump(http_response_code(400));
	return "{\"status\": 400, \"result\":\"Bad param\"}";        
	}
	
	
	public function delete_task($param){
		
			if($param!=-1&&is_numeric($param)){				
			$stmt = $this->conn->prepare("SELECT * FROM `task` WHERE id=?");
			$stmt->bind_param("s", $param);
			$result = $stmt->execute();
            $tasks = $stmt->get_result();
			$rows = $tasks->num_rows;
			$stmt->close();
			if($rows){				
				$stmt = $this->conn->prepare("DELETE FROM `task` WHERE id=?");
				$stmt->bind_param("s", $param);
				$result = $stmt->execute();
				$task = $stmt->get_result();
				$error = $stmt->error;				
				$stmt->close();
				if($result) {$this->set_headers(); return "{\"status\": 200,\"result\":\"Task deleted successfully\"}";}
					else {$this->set_headers(); var_dump(http_response_code(400)); $output = ob_get_clean(); return "{\"status\": 400,\"result\":\"Error\"}";}				
			}
				else {$this->set_headers();var_dump(http_response_code(400)); $output = ob_get_clean(); return "{\"status\": 400,\"result\":\"Task doesn't exist\"}";}		
		}
		else {$this->set_headers(); var_dump(http_response_code(400)); $output = ob_get_clean(); return "{\"status\": 400, \"result\":\"Set task id\"}";}
		
	}
	
	
	
	public function update_task($param){

        $a = file_get_contents('php://input');
        $array = (array)json_decode($a);
        $i = 0;
        $query = 'UPDATE `task` SET ';
        if ($param != "-1") {
            if (isset($array["name"])) {
                if ($i == 1) {
                    $query = $query . " , ";
                    $i = 0;
                }
                $query = $query . "`name`=\"" . $array['name'] . "\" ";
                $i++;
            }

            if (isset($array['descryption'])) {
                if ($i == 1) {
                    $query = $query . " , ";
                    $i = 0;
                }
                $query = $query . " `descryption`=\"" . $array['descryption'] . "\" ";
                $i++;
            }

            if (isset($array['start_date'])) {
                if ($i == 1) {
                    $query = $query . " , ";
                    $i = 0;
                }
                $query = $query . " `start_date`=\"" . $array['start_date'] . "\" ";
                $i++;
            }

            if (isset($array['end_date'])) {
                if ($i == 1) {
                    $query = $query . " , ";
                    $i = 0;
                }
                $query = $query . " `end_date`=\"" . $array['end_date'] . "\" ";
                $i++;
            }

            $query = $query . "where `id`= " . $param;


        }


        else {
            $this->set_headers();
            var_dump(http_response_code(400));
            $output = ob_get_clean();
            return 0;
        }

        if ($i == 0) {
            $this->set_headers();
            var_dump(http_response_code(400));
            $output = ob_get_clean();
            return 0;
        }
        if ($i == 1) {

            $stmt = $this->conn->prepare("SELECT * FROM `task` WHERE id=?");
            $stmt->bind_param("s", $param);
            $result = $stmt->execute();
            $tasks = $stmt->get_result();
            $rows = $tasks->num_rows;
            $stmt->close();
            if ($rows) {
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                $result = $stmt->execute();
                $error = $stmt->error;
                $stmt->close();
                if ($result) {
                    $this->set_headers();
                    return "{\"status\":200,
				\"result\":\"Project updated successfully\",
				\"_links\":{\"_self\":\"" . URL . $param . "\"}}";
                } else {
                    $this->set_headers();
                    return "{\"status\": 400,\"result\":\"" . $error . "\"}";
                }
            } else {
                $this->set_headers();
                var_dump(http_response_code(404));
                $output = ob_get_clean();
            }
        }

	}
		
	

			
} 
