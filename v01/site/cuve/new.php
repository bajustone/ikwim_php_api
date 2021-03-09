<?php header("Content-Type:application/json; charset=utf-8"); ?>
<?php include "../../lib/mysqli.php"; ?>


<?php
$requestType = $_SERVER['REQUEST_METHOD'];
if ($requestType === "POST") {
    $body = file_get_contents("php://input");
    $data = json_decode($body);

    $pdoInstance = PDOHelper::getInstance();

    if ($data == null) {
        echo ("Invalid data");
        return;
    }
    $createCuveQuery = "INSERT INTO Cuve 
        (cuveName, cuveSize, cuveMin, comments, cuveOnStationDate, stationId, productId) 
        VALUES (:cuveName, :cuveSize, :cuveMin, :comments, :cuveOnStationDate, :stationId, :productId)";

    try {

        $createCuvePreparedQuery = $pdoInstance->prepare($createCuveQuery);
        $cuveCreated = $createCuvePreparedQuery->execute(array(
            "cuveName" => $data->cuveName,
            "cuveSize" => $data->cuveSize, 
            "cuveMin" => $data->cuveMin, 
            "comments" => $data->comments, 
            "cuveOnStationDate" => $data->cuveOnStationDate, 
            "stationId" => $data->stationId, 
            "productId" => $data->productId
        ));

        if ($cuveCreated == false) {
            throw  new ErrorException("Failed to create cuve new " . $data->cuveName);
            return;
        }
        $result = array(
            "success" => true
        );
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