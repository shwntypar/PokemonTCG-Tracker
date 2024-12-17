<?php

require_once 'global.php';

class Get extends GlobalMethods
{
    private $pdo;
    public function __construct(\PDO $pdo)
    {   
        parent::__construct();
        $this->pdo = $pdo;
    }

    /* Functions that executes Queries */

    private function get_records($table = null, $conditions = null, $columns = '*', $customSqlStr = null, $params = [])
    {
        if ($customSqlStr != null) {
            $sqlStr = $customSqlStr;
        } else {
            $sqlStr = "SELECT $columns FROM $table";
            if ($conditions != null) {
                $sqlStr .= " WHERE " . $conditions;
            }
        }
        $result = $this->executeQuery($sqlStr, $params);

        if ($result['code'] == 200) {
            return $this->sendPayload($result['data'], 'success', "Successfully retrieved data.", $result['code']);
        }
        return $this->sendPayload(null, 'failed', "Failed to retrieve data.", $result['code']);
    }

    private function executeQuery($sql, $params = [])
    {
        $data = [];
        $errmsg = "";
        $code = 0;

        try {
            $statement = $this->pdo->prepare($sql);
            if ($statement->execute($params)) {
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($result as $record) {
                    // Handle BLOB data
                    /* if (isset($record['images'])) {
                        $record['images'] = base64_encode($record['images']);
                    } */
                   /* if(isset($record['images'])){
                    $record['images'] = stream_get_contents($record['images']);
                   } */
                    
                    array_push($data, $record);
                }
                $code = 200;
                return array("code" => $code, "data" => $data);
            } else {
                $errmsg = "No data found.";
                $code = 404;
            }
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 403;
        }
        return array("code" => $code, "errmsg" => $errmsg);
    }
 
    public function getProducts($id = null){    
        $condition = null;
        if ($id != null) {
            $condition = "id=$id";
        }
        return $this->get_records('product', $condition);
    }

    public function getUsers($id = null){
        $condition = null;
        if ($id != null) {
            $condition = "id=$id";
        }
        return $this->get_records('users', $condition);
    }

    public function getSuppliers($id = null)
    {
        $condition = null;
        if ($id != null) {
            $condition = "id=$id";
        }
        return $this->get_records('supplier', $condition);
    }

    public function getProductImages($id = null){
        $condition = null;
        if ($id != null) {
            $condition = "product_id=$id";
        }
        return $this->get_records('product_images', $condition);
    }

    public function getPokemonCards($id = null){
        $condition = null;
        if ($id != null) {
            $condition = "id=$id";
        }
        return $this->get_records('pokemon_card', $condition);
    }
}