<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'global.php';

class Post extends GlobalMethods
{

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function AddUsers($data){
        try{
            $sql = "INSERT INTO users (first_name, last_name, username, email, password, is_superadmin) 
        VALUES (?,?,?,?,?,?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data->first_name,
            $data->last_name,
            $data->username,
            $data->email,
            $data->password,
            0
        ]);
        return $this->sendPayload(null, "success" , "User Successfully Added!", 200);
        } catch (PDOException $e){
            return $this->sendPayload(null, "failed", $e->getMessage(), 400);
        }
    }

    public function AddProducts($data){
        try{
            $sql = "INSERT INTO product (product_name, price, description, quantity, views, images, supplier_id) 
                    VALUES (?,?,?,?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data->product_name,
                $data->price,
                $data->description,
                $data->quantity,
                $data->views,
                $data->images,
                $data->supplier_id
            ]);
            return $this->sendPayload(null, "success", "Product Successfully Added!", 200);
        } catch (PDOException $e){
            error_log("AddProducts Error: " . $e->getMessage());
            return $this->sendPayload(null, "failed", $e->getMessage(), 400);
        }
    }

    public function AddSuppliers($data){
        try{
            $sql = "INSERT INTO supplier (supplier_name, contact_person, email, phone, address) 
        VALUES (?,?,?,?,?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data->supplier_name,
            $data->contact_person,
            $data->email,
            $data->phone,
            $data->address,
        ]);
        return $this->sendPayload(null, "success" , "Supplier Successfully Added!", 200);
        } catch (PDOException $e){
            return $this->sendPayload(null, "failed", $e->getMessage(), 400);
        }
    }

    public function userLogin($data)
    {
        try {
            // CHECK IF USER EXISTS
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$data->username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !$data->password) {
                return $this->sendPayload(null, "failed", "Invalid username or password", 401);
            }   

            // GENERATE JWT TOKEN
            /* $jwt = new Jwt();
            $payload = [
                "id" => $user['id'],
                "firstName" => $user['first_name'],
                "lastName" => $user['last_name'],
                "email" => $user['email'],
                "role" => $user['role'],
                'exp' => time() + (60 * 60 * 24)
            ]; */

           /*  $token = $jwt->encode($payload);
            return $this->sendPayload([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name']
                ]
            ], "success", "Login successful", 200); */
            return $this->sendPayload(null, "success", "Login successful", 200);
        } catch (PDOException $e) {
            return $this->sendPayload(null, "failed", $e->getMessage(), 400);
        }
    }

    

    /* public function AddProductImages($data){
        try{
            if(isset($_FILES['image'])) {
                $file = $_FILES['image'];
                $fileName = time() . '_' . $file['name'];
                
                // Create absolute path to uploads directory
                $uploadDir = __DIR__ . '/../../uploads/';  // Go up two directories from current file
                
                // Create directory if it doesn't exist
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $uploadPath = $uploadDir . $fileName;
    
                if(move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $sql = "INSERT INTO product_images (product_id, image) VALUES (?,?)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute([
                        $data->product_id,
                        $fileName  // Save only filename in database
                    ]);
                    return $this->sendPayload(null, "success", "Product Image Successfully Added!", 200);
                } else {
                    $uploadError = error_get_last();
                    return $this->sendPayload(null, "failed", "Failed to upload image: " . ($uploadError['message'] ?? 'Unknown error'), 400);
                }
            } else {
                return $this->sendPayload(null, "failed", "No image file received", 400);
            }
        } catch (PDOException $e){
            return $this->sendPayload(null, "failed", $e->getMessage(), 400);
        }
    }
 */

    public function AddPokemonCard($data) {
        try {
            // Debug the incoming request
            error_log('POST data: ' . print_r($_POST, true));
            error_log('FILES data: ' . print_r($_FILES, true));

            if (empty($_FILES['image'])) {
                throw new Exception('No image file in request');
            }

            // Handle file upload
            try {
                error_log("Attempting to upload main image");
                // Check if image exists in $_FILES
                if (!isset($_FILES['image'])) {
                    error_log("No image file found in request");
                    error_log("FILES array content: " . print_r($_FILES, true));
                    throw new Exception('No image file received');
                }

                $image_path = $this->handleFileUpload($_FILES['image']);
                error_log("Main image uploaded successfully: " . $image_path);
            } catch (Exception $e) {
                error_log("Main image upload failed: " . $e->getMessage());
                return $this->sendPayload(null, "failed", "Image upload failed: " . $e->getMessage(), 400);
            }

            // Debug log for received data
            error_log("Processing data for database insertion:");
            error_log("Name: " . (isset($data->name) ? $data->name : 'not set'));
            error_log("Rarity: " . (isset($data->rarity) ? $data->rarity : 'not set'));
            error_log("Type: " . (isset($data->type) ? $data->type : 'not set'));
            error_log("Date Added: " . (isset($data->date_added) ? $data->date_added : 'not set'));

            // Convert form data to object properties if needed
            $name = $_POST['name'] ?? null;
            $rarity = $_POST['rarity'] ?? null;
            $type = $_POST['type'] ?? null;
            $date_added = $_POST['date_added'] ?? null;

            if (!$name || !$rarity || !$type || !$date_added) {
                throw new Exception('Missing required fields');
            }

            // Insert record into database
            $sql = "INSERT INTO pokemon_card (name, rarity, type, date_added, image) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $name,
                $rarity,
                $type,
                $date_added,
                basename($image_path) // Store only the filename
            ]);

            return $this->sendPayload(null, "success", "Pokemon Card Successfully Added!", 200);
        } catch (Exception $e) {
            error_log("AddPokemonCard Error: " . $e->getMessage());
            return $this->sendPayload(null, "failed", $e->getMessage(), 400);
        }
    }

    private function handleFileUpload($file) {
        error_log("HandleFileUpload received file: " . print_r($file, true));

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception('No file uploaded or invalid file');
        }

        $uploadDir = __DIR__ . '/../../static/uploads/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("Failed to create directory: " . $uploadDir);
                throw new Exception('Failed to create upload directory');
            }
            error_log("Created upload directory: " . $uploadDir);
        }

        // Generate unique filename
        $filename = time() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        error_log("Attempting to move file to: " . $targetPath);
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            error_log("Failed to move uploaded file. Upload error: " . error_get_last()['message']);
            throw new Exception('Failed to move uploaded file');
        }

        error_log("File successfully moved to: " . $targetPath);
        return $filename; // Return just the filename since we'll store that in the database
    }

}



