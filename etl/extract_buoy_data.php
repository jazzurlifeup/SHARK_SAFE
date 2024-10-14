<?php
include 'config.php'; // This is your database connection file

// Buoy data API URL (replace this with the actual API endpoint you're using)
$buoy_api_url = "https://surftruths.com/api/buoys.json";

// Fetch the data from the API
$buoy_data = file_get_contents($buoy_api_url);
$buoy_data = json_decode($buoy_data, true); // Decode JSON into an associative array

// Loop through each data point and insert it into the database
foreach ($buoy_data as $data) {
    // Prepare an SQL insert query
    $query = "INSERT INTO buoy_data (wave_height, water_temp, wind_speed, time, latitude, longitude) 
              VALUES ('".$data['wave_height']."', '".$data['water_temp']."', '".$data['wind_speed']."', 
              '".$data['time']."', '".$data['latitude']."', '".$data['longitude']."')";
    
    // Execute the query
    if ($conn->query($query) === TRUE) {
        echo "New buoy data inserted successfully\n";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

$conn->close(); // Close the database connection
?>
