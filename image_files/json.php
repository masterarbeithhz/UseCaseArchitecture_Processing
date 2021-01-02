<html>
 <head>
  <title>PHP-Test</title>
 </head>
 <body>
<?php
include('envVariables.php');

//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
    throw new Exception('Request method must be POST!');
}
 
//Make sure that the content type of the POST request has been set to application/json
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if(strcasecmp($contentType, 'application/json') != 0){
    throw new Exception('Content type must be: application/json');
}

// Read the input stream
$json = file_get_contents('php://input');
$obj = json_decode($json);
$hardware_serial = $obj->hardware_serial;
$wifi = $obj->payload_fields->wifi;
$ble = $obj->payload_fields->ble;


  $conn = mysqli_connect(DATABASENAME,DATABASEUSER,DATABASEPSWD,DATABASEDB);

  if ($conn->connect_error) {
    die("ERROR: Unable to connect: " . $conn->connect_error);
  }



$sqlQuery_devices = "SELECT dev_id from devices WHERE dev_Id='$hardware_serial'";
$result_devices = mysqli_query($conn,$sqlQuery_devices);
$data_sensors = array();
foreach ($result_devices as $row) {
        $data_sensors[] = $row;
}

// checks if sensor id is listed in your devices table if not massage is ignored
if ($data_sensors[0][dev_id]) {
  $stmt = $conn->prepare("INSERT INTO readings (dev_id, count_wifi, count_ble) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $hardware_serial, $wifi, $ble);
  $stmt->execute();
  echo 'sensor value saved successfully';
} else {
    echo 'the used sensor dev_id is not known';
}

  $stmt->close();
  $conn->close();
?>
 </body>
</html>