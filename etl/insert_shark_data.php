<?php
include 'config.php'; // Include your database connection

// Function to fetch shark attack data from API
function fetchSharkAttackData() {
    $url = "https://public.opendatasoft.com/api/explore/v2.1/catalog/datasets/global-shark-attack/records?where=area%20%3D%20%27Florida%27&limit=20";
    
    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set options for the cURL request
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    // Execute cURL session and get the content
    $response = curl_exec($ch);
    
    // Check for errors
    if ($response === false) {
        $error = curl_error($ch);
        echo "Error fetching data: " . $error;
        curl_close($ch);
        return null;
    }
    
    // Close the cURL session
    curl_close($ch);
    
    // Decode the JSON response and return the data
    return json_decode($response, true);
}

// Fetch data from the API
$data = fetchSharkAttackData();

if ($data && isset($data['results'])) {
    // Establish the PDO connection using the config file
    try {
        $pdo = new PDO($dsn, $username, $password, $options);

        // Loop through each record in the 'results' array
        foreach ($data['results'] as $record) {
            $location = $record['location'] ?? null;
            $date = $record['date'] ?? null;
            $fatal = $record['fatal_y_n'] ?? null; // Correct mapping for 'Fatal'
            $activity = $record['activity'] ?? null;
            $injury = $record['injury'] ?? null;
            $species = $record['species'] ?? null;

            // Validate if required fields are not null
            if ($location && $date) {
                // Prepare SQL query with placeholders for data insertion
                $sql = "INSERT INTO shark_attack_data (location, date, Fatal, Activity, Injury, shark_type)
                        VALUES (:location, :date, :fatal, :activity, :injury, :species)";
                
                // Prepare and execute the statement with data binding
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':location' => $location,
                    ':date' => $date,
                    ':fatal' => $fatal,
                    ':activity' => $activity,
                    ':injury' => $injury,
                    ':species' => $species
                ]);

                echo "New shark attack record inserted successfully for location: $location<br>";
            } else {
                echo "Missing required fields for record: " . print_r($record, true) . "<br>";
            }
        }

    } catch (PDOException $e) {
        // Catch any errors during the connection or query execution
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "No data to insert or invalid data structure.";
}
