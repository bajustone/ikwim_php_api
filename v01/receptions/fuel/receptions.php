<?php header("Content-Type:application/json; charset=utf-8"); 
include "../../lib/mysqli.php";

function listReceptions( $date, $activityId){
    $query = "SELECT ReceptionPerTour.receptionPerTourId AS dispatchId, ReceptionPerTour.cuveId, ReceptionPerTour.receptionPerTourDate, ReceptionPerTour.receptionPerTourQuantity, ReceptionPerTour.receptionProduiBlancId, Cuve.cuveName, Product.productId, Product.productName, ReceptionProduitBlanc.receptionProduitBlancId, ReceptionProduitBlanc.receptionProduitBlancQte, ReceptionProduitBlanc.receptionProduitBlancShipmentNo, ReceptionProduitBlanc.receptionProduitBlancPlateNo, ReceptionProduitBlanc.receptionProduitBlancDate, ReceptionProduitBlanc.depotDeliveryNoteId, ReceptionType.receptionTypeId, ReceptionType.receptionTypeName,
    DepotPB.depotPBId, DepotPB.depotPBName, Station.stationId, Station.stationName, Activity.activityId, Activity.activityName  
    FROM ReceptionPerTour 
    LEFT JOIN Cuve ON ReceptionPerTour.cuveId = Cuve.cuveId 
    LEFT JOIN Product ON Cuve.productId = Product.productId 
    LEFT JOIN ReceptionProduitBlanc ON  ReceptionPerTour.receptionProduiBlancId = ReceptionProduitBlanc.receptionProduitBlancId
    LEFT JOIN StationActivity ON ReceptionProduitBlanc.stationId = StationActivity.stationActivityId
    LEFT JOIN Station ON StationActivity.stationId = Station.stationId
    LEFT JOIN Activity ON StationActivity.activityId = Activity.activityId
    LEFT JOIN ReceptionType ON ReceptionProduitBlanc.ReceptionType = ReceptionType.receptionTypeId
    LEFT JOIN DepotPB ON ReceptionProduitBlanc.depotId = DepotPB.depotPBId
        WHERE ReceptionProduitBlanc.receptionProduitBlancDate= :dateValue AND ReceptionProduitBlanc.stationId= :activityId";
    $queryResult = PDOHelper::get_result($query, array("dateValue"=>$date, "activityId"=>$activityId));
    $res = [];
    foreach ($queryResult as $row) {
        $dispatch = array(
            "cuveId"=> $row["cuveId"],
            "cuveName"=> $row["cuveName"],
            "dispatchId"=> $row["dispatchId"],
            "date"=> $row["receptionPerTourDate"],
            "receptionId"=> $row["receptionProduitBlancId"],
            "productId"=> $row["productId"],
            "productName" => $row["productName"],
            "quantity" => $row["receptionPerTourQuantity"],

        );
        if($res[$row["receptionProduitBlancId"]] != null){
            $res[$row["receptionProduitBlancId"]]["dispatch"][] = $dispatch;
            continue;
        }
        $res[$row["receptionProduitBlancId"]] = array(
        "receptionId"=> $row["receptionProduiBlancId"],
        "productId"=> $row["productId"],
        "productName" => $row["productName"],
        "receptionProduitBlancQte" => $row["receptionProduitBlancQte"],
        "receptionProduitBlancShipmentNo" => $row["receptionProduitBlancShipmentNo"],
        "receptionProduitBlancPlateNo" => $row["receptionProduitBlancPlateNo"],
        "receptionProduitBlancDate" => $row["receptionProduitBlancDate"],
        "depotDeliveryNoteId" => $row["depotDeliveryNoteId"],
        "receptionTypeId" => $row["receptionTypeId"],
        "receptionTypeName" => $row["receptionTypeName"],
        "depotPBId" => $row["depotPBId"],
        "depotPBName" => $row["depotPBName"],
        "stationId" => $row["stationId"],
        "stationName" => $row["stationName"],
        "activityId" => $row["activityId"],
        "activityName" => $row["activityName"],
        "dispatch" => [$dispatch]
        );
    }

    return $res;
}
function updateReception($body){
    echo($body);
}

function readReceptions(){
    $date = $_GET['date'];
    $activityId = $_GET['activityId'];
    $response = listReceptions( $date, $activityId);
    $json = json_encode($response);
    echo($json);
}

$requestType = $_SERVER['REQUEST_METHOD'];
if( $requestType === "PUT"){
    $body = file_get_contents("php://input");
    $rowId = $_GET["id"];
    return updateReception($body);
}
elseif($requestType === "GET"){
    readReceptions();
}
?>