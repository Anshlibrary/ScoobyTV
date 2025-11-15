<?php
// Initialize cURL session
$ch = curl_init();

// Set the URL to connect to
$url = "http://stv1.xyz:53570/";
curl_setopt($ch, CURLOPT_URL, $url);

// Return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Include the header in the output
curl_setopt($ch, CURLOPT_HEADER, true);

// Enable verbose output to include detailed information
curl_setopt($ch, CURLOPT_VERBOSE, true);

// Open a file handle for the verbose output
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

// Execute the cURL session
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    // Display the response
    echo "Response:\n$response\n";

    // Get detailed info about the connection
    $info = curl_getinfo($ch);
    echo "\nConnection details:\n";
    print_r($info);
}

// Close the cURL session
curl_close($ch);

// Output the verbose information
rewind($verbose);
$verboseLog = stream_get_contents($verbose);
echo "\nVerbose information:\n" . $verboseLog;

fclose($verbose);
?>

