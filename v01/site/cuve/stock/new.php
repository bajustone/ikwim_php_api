<?php header("Content-Type:application/json; charset=utf-8"); ?>
<?php include "../../../lib/mysqli.php"; ?>


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
    $newStockQuery = "INSERT INTO StockPerTour 
        (cuveId, stockPerTourDate, stockPerTourQuantity, stockDebut) 
        VALUES (:cuveId, :stockPerTourDate, :stockPerTourQuantity, :stockDebut)";

    try {

        $newStockPreparedQuery = $pdoInstance->prepare($newStockQuery);
        $stockCreated = $newStockPreparedQuery->execute(array(
            "cuveId" => $data->cuveId,
            "stockPerTourDate" => $data->stockPerTourDate, 
            "stockPerTourQuantity" => $data->stockPerTourQuantity, 
            "stockDebut" => $data->stockDebut
        ));

        if ($stockCreated == false) {
            throw  new ErrorException("Failed to add stock to cuve new " . $data->cuveId);
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