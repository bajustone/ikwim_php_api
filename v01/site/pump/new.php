<?php header("Content-Type:application/json; charset=utf-8"); ?>
<?php include "../../lib/mysqli.php"; ?>


<?php
$requestType = $_SERVER['REQUEST_METHOD'];
if ($requestType === "POST") {
    $body = file_get_contents("php://input");
    $data = json_decode($body);
    $stationId = $data->stationId;
    $stationActivityId = $data->stationActivityId;

    $pdoInstance = PDOHelper::getInstance();
    $pdoInstance->beginTransaction();

    if ($data == null) {
        echo ("Invalid data");
        return;
    }
    $createPumpQuery = "INSERT INTO Pump 
        (pumpName, pumpIndex, comments, pumpOnStationDate, stationActivityId) 
        VALUES (:pumpName, :pumpIndex, :comments, :pumpOnStationDate, :stationActivityId)";
    $insertPumpCuveQuery = "INSERT INTO PumpCuve 
        (pumpId, cuveId, stationId, pumpCuveDate) 
        VALUES (:pumpId, :cuveId, :stationId, :pumpCuveDate)";

    try {

        $createPumpPreparedQuery = $pdoInstance->prepare($createPumpQuery);
        $pumpCreated = $createPumpPreparedQuery->execute(array(
            "pumpName" => $data->pump->pumpName,
            "pumpIndex" => $data->pump->pumpIndex, 
            "pumpOnStationDate" => $data->pump->pumpOnStationDate, 
            "stationActivityId" => $data->pump->stationActivityId, 
            "comments" => $data->pump->comments
        ));

        if($pumpCreated == false) {
            throw  new ErrorException("Failed to create pump new " . $data->pump->pumpName);
            $pdoInstance->rollBack();
            return;
        }
        $insertedRow = PDOHelper::get_result("SELECT * FROM Pump WHERE stationActivityId=:stationActivityId AND pumpName=:pumpName", 
        array(
            "stationActivityId" => $data->pump->stationActivityId,
            "pumpName" => $data->pump->pumpName
        ));
        if(count($insertedRow) == 0) {
            throw  new ErrorException("Pump not added " . $data->pump->pumpName);
            $pdoInstance->rollBack();
        }
        $pumpId = $insertedRow[0]["pumpId"];

        $insertPumpCuvePreparedQuery = $pdoInstance->prepare($insertPumpCuveQuery);
        $pumpCreated = $insertPumpCuvePreparedQuery->execute(array(
            "pumpId" => $pumpId,
            "cuveId" => $data->cuveId, 
            "stationId" => $data->stationId, 
            "pumpCuveDate" => $data->pump->pumpOnStationDate
        ));
       

        if ($pumpCreated == false ) {
            
            throw  new ErrorException("Failed to add pump to cuve " . $data->cuveId);
        }
        $result = array(
            "success" => true
        );
        $pdoInstance->commit();
        echo (json_encode($result));
    } catch (Exception $th) {
        $result = array(
            "success" => false,
            "error" => $th->getMessage()
        );
        echo (json_encode($result));
    }


    return;
}
echo ("Hello world");




?>