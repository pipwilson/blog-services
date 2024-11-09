<?php
// Get the intitle and inauthor parameters from the URL
$title = isset($_GET['title']) ? urlencode($_GET['title']) : '';
$author = isset($_GET['author']) ? urlencode($_GET['author']) : '';

// Check if the title parameter is provided
if (empty($title)) {
    die('You must provide a book title');
}

// Make the title safe for a filename
$safeTitle = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '_', urldecode($title)));

// Define the local file name
$file = $safeTitle . '.json';

// Check if the local file exists
if (file_exists($file)) {
    // Read the JSON data from the local file
    $jsonData = file_get_contents($file);
} else {
    // Construct the URL
    $url = "https://www.googleapis.com/books/v1/volumes?q=intitle:$title";
    if (!empty($author)) {
        $url .= "+inauthor:$author";
    }

    // Fetch the JSON data
    $jsonData = file_get_contents($url);

    // Check if data was fetched successfully
    if ($jsonData === FALSE) {
        die('Error fetching JSON data.');
    }

    // Save the JSON data to a local file
    file_put_contents($file, $jsonData);
}

// Decode the JSON data to a PHP array
$data = json_decode($jsonData, true);

// Check if decoding was successful
if ($data === NULL) {
    die('Error decoding JSON data.');
}

// Loop through all items to find an industryIdentifier with type ISBN_13
$found = false;
$isbn = '';
foreach ($data['items'] as $item) {
    if (isset($item['volumeInfo']['industryIdentifiers'])) {
        foreach ($item['volumeInfo']['industryIdentifiers'] as $identifier) {
            if ($identifier['type'] === 'ISBN_13') {
                $isbn = $identifier['identifier'];
                $found = true;
                break 2; // Exit both loops
            }
        }
    }
}

if (!$found) {
    echo 'No ISBN_13 identifier found.';
} else {
    echo $isbn;
}
?>