<?php header("Content-Type:application/json; charset=utf-8"); ?>
<?php include "../../lib/mysqli.php";?>


<?php 
$requestType = $_SERVER['REQUEST_METHOD'];
if( $requestType === "POST"){
    $body = file_get_contents("php://input");
    $data = json_decode($body);

    $pdoInstance = PDOHelper::getInstance();
    $pdoInstance->beginTransaction();
    // print_r($stationData);
    if($data == null ){
        echo("Invalid data");
        return;
    }
    // echo($body);

    $createSiteQuery = "INSERT INTO Station (stationName, stationAddress) VALUES ( :siteName, :siteAddress )";
    $createStationActivityQuery = "INSERT INTO StationActivity ( stationId, activityId ) VALUES ( ?, ? )";

    $stationData = array("siteName"=> $data->name, "siteAddress"=>$data->address);
    $createSitePreparedQuery = $pdoInstance->prepare($createSiteQuery);
    $stationCreated = $createSitePreparedQuery->execute( $stationData);

    $createStationActivityPreparedQuery = $pdoInstance->prepare($createStationActivityQuery);
    try {


    if($stationCreated == false) {
        throw  new ErrorException("Failed to create station with name " . $data->name);
        $pdoInstance->rollBack();
        return;
    }
    $insertedRow = PDOHelper::get_result("SELECT * FROM Station WHERE stationName=:stationName", array("stationName"=>$data->name));
    if(count($insertedRow) == 0) {
        $pdoInstance->rollBack();
        return;
    }
    $stationId = $insertedRow[0]["stationId"];
    $activityInserted = true;
        for ($i=0; $i < count($data->activities); $i++) { 
            $activityId = $data->activities[$i];
            $activityData = [$stationId, $activityId];

            $executeSuccess = $createStationActivityPreparedQuery->execute($activityData);
            if( $executeSuccess!=true){
                print_r($createStationActivityPreparedQuery->queryString);
                throw new ErrorException("Failed to insert in StationActivity: " . $activityId);
            }
        }
        $pdoInstance->commit();


        $result = array(
            "success"=> true,
            "stationData"=> $insertedRow
        );
        echo(json_encode($result));
    } catch ( Exception $th) {
        $pdoInstance->rollBack();
        $result = array(
            "success"=> false,
            "error:" => $th->getMessage()
        );
        echo(json_encode($result));
    }
   
    
    return;
}
echo("Hello world");




?>