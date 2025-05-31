<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Search - Tax Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .response {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            white-space: pre-wrap;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Vehicle Search - Tax Portal</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="user_id">User ID</label>
            <input type="text" id="user_id" name="user_id" value="WebPortal" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" value="Sapphire@123" required>
        </div>
        <div class="form-group">
            <label for="vehicle_category">Vehicle Category</label>
            <select id="vehicle_category" name="vehicle_category" required>
                <(my apologies, the response was cut off here. I will continue where it left off and complete the HTML/PHP solution, ensuring it addresses the requirements and the WAF issue context.)

```php
                <option value="4W" selected>4W (Four Wheeler)</option>
                <option value="2W">2W (Two Wheeler)</option>
                <option value="CV">CV (Commercial Vehicle)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="registration_no">Registration Number</label>
            <input type="text" id="registration_no" name="registration_no" value="BA-002" required>
        </div>
        <div class="form-group">
            <label for="cnic_no">CNIC Number (optional)</label>
            <input type="text" id="cnic_no" name="cnic_no" placeholder="Enter CNIC or leave blank">
        </div>
        <div class="form-group">
            <label for="chassis_no">Chassis Number (optional)</label>
            <input type="text" id="chassis_no" name="chassis_no" placeholder="Enter Chassis No or leave blank">
        </div>
        <div class="form-group">
            <label for="engine_no">Engine Number (optional)</label>
            <input type="text" id="engine_no" name="engine_no" placeholder="Enter Engine No or leave blank">
        </div>
        <button type="submit" name="submit">Search Vehicle</button>
    </form>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if (isset($_POST['submit'])) {
        // API endpoint
        $url = 'https://taxportal.excise.gos.pk/TaxPortalAppServices/RoutVehicleSearching';

        // Request headers
        $headers = array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
            'Content-Type: application/json; charset=utf-8',
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate',
            'Authorization: Basic V2ViUG9ydGFsOlNhcHBoaXJlQDEyMw==',
            'Connection: keep-alive'
        );

        // Request payload from form inputs
        $data = array(
            '_USER_ID_' => $_POST['user_id'],
            '_PASSWORD_' => $_POST['password'],
            '_VEHICLE_CATEGORY_' => $_POST['vehicle_category'],
            '_REGISTRATION_NO_' => $_POST['registration_no'],
            '_CNIC_NO_' => !empty($_POST['cnic_no']) ? $_POST['cnic_no'] : null,
            '_CHASSIS_NO_' => !empty($_POST['chassis_no']) ? $_POST['chassis_no'] : null,
            '_ENGINE_NO_' => !empty($_POST['engine_no']) ? $_POST['engine_no'] : null
        );

        // Convert data to JSON
        $jsonData = json_encode($data);

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Use with caution
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        // Execute the request
        $response = curl_exec($ch);

        // Get additional info
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $requestHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);

        // Check for cURL errors
        if (curl_errno($ch)) {
            echo '<div class="response error">cURL Error: ' . curl_error($ch) . '</div>';
        } else {
            echo '<div class="response">';
            echo 'HTTP Status Code: ' . $httpCode . '<br>';
            echo 'Content-Type: ' . $contentType . '<br>';
            echo 'Request Headers: <pre>' . htmlspecialchars($requestHeaders) . '</pre>';

            // Separate headers and body
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            echo 'Response Headers: <pre>' . htmlspecialchars($header) . '</pre>';

            // Handle response based on Content-Type
            if (strpos($contentType, 'application/json') !== false) {
                $decodedResponse = json_decode($body, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo 'Decoded JSON Response: <pre>';
                    print_r($decodedResponse);
                    echo '</pre>';
                } else {
                    echo 'JSON Decode Error: ' . json_last_error_msg() . '<br>';
                }
            } else {
                echo 'Non-JSON Response Received: <pre>' . htmlspecialchars($body) . '</pre>';
            }
            echo '</div>';
        }

        // Close cURL
        curl_close($ch);
    }
    ?>
</body>
</html>
