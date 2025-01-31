<?php
    require_once '../../includes/connect_endpoint.php';
    session_start();

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);

        if (
            !isset($data["days"]) || $data['days'] == "" ||
            !isset($data["smtpaddress"]) || $data["smtpaddress"] == "" ||
            !isset($data["smtpport"]) || $data["smtpport"] == "" ||
            !isset($data["smtpusername"]) || $data["smtpusername"] == "" ||
            !isset($data["smtppassword"]) || $data["smtppassword"] == ""
        ) {
            $response = [
                "success" => false,
                "errorMessage" => "Please fill all fields"
            ];
            echo json_encode($response);
        } else {
            $enabled = $data["enabled"];
            $days = $data["days"];
            $smtpAddress = $data["smtpaddress"];
            $smtpPort = $data["smtpport"];
            $smtpUsername = $data["smtpusername"];
            $smtpPassword = $data["smtppassword"];

            $query = "SELECT COUNT(*) FROM notifications";
            $result = $db->querySingle($query);
    
            if ($result === false) {
                $response = [
                    "success" => false,
                    "errorMessage" => "Error saving notifications data"
                ];
                echo json_encode($response);
            } else {
                if ($result == 0) {
                    $query = "INSERT INTO notifications (enabled, days, smtp_address, smtp_port, smtp_username, smtp_password)
                              VALUES (:enabled, :days, :smtpAddress, :smtpPort, :smtpUsername, :smtpPassword)";
                } else {
                    $query = "UPDATE notifications
                              SET enabled = :enabled, days = :days, smtp_address = :smtpAddress, smtp_port = :smtpPort,
                                  smtp_username = :smtpUsername, smtp_password = :smtpPassword";
                }
    
                $stmt = $db->prepare($query);
                $stmt->bindValue(':enabled', $enabled, SQLITE3_INTEGER);
                $stmt->bindValue(':days', $days, SQLITE3_INTEGER);
                $stmt->bindValue(':smtpAddress', $smtpAddress, SQLITE3_TEXT);
                $stmt->bindValue(':smtpPort', $smtpPort, SQLITE3_INTEGER);
                $stmt->bindValue(':smtpUsername', $smtpUsername, SQLITE3_TEXT);
                $stmt->bindValue(':smtpPassword', $smtpPassword, SQLITE3_TEXT);
    
                if ($stmt->execute()) {
                    $response = [
                        "success" => true
                    ];
                    echo json_encode($response);
                } else {
                    $response = [
                        "success" => false,
                        "errorMessage" => "Error saving notification data"
                    ];
                    echo json_encode($response);
                }
            }
        }
    }
?>