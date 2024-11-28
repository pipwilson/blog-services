<?php
// Get the title and author parameters from the URL
$title = isset($_GET['title']) ? urldecode($_GET['title']) : '';
$author = isset($_GET['author']) ? urldecode($_GET['author']) : '';
$url = isset($_GET['url']) ? urldecode($_GET['url']) : '';
$isbn = isset($_GET['isbn']) ? urldecode($_GET['isbn']) : '';
$when = isset($_GET['when']) ? urldecode($_GET['when']) : '';

// Check if the title parameter is provided
if (empty($title) || empty($author) || empty($url)) {
    die('You must provide a title, author and url');
}

function makeBookObject($title, $author, $isbn, $url, $when) {
    $book = array();
    $book['title'] = $title;
    $book['author'] = $author;
    $book['isbn'] = $isbn;
    $book['url'] = $url;
    $book['when'] = $when;

    return $book;
}

function addBookToFile($file, $book) {
    if (file_exists($file)) {
        // Read the JSON data from the local file
        $currentData = file_get_contents($file);
        $currentData = json_decode($currentData, true);
        // if title already exists return current data
        foreach ($currentData as $key => $value) {
            if ($value['title'] == $book['title']) {
                return $currentData;
            }
        }
    } else {
        $currentData = array();
    }

    $currentData = array_merge($currentData, array($book));

    file_put_contents($file, json_encode($currentData));

    return $currentData;
}

$book = makeBookObject($title, $author, $isbn, $url, $when);

$file = 'books.json';

// Save the JSON data to a local file
$books = json_encode(addBookToFile($file, $book));

// don't actually need to return anything here - can take this out later
echo $books;

?>
