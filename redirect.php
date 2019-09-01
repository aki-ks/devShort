<?php

// All relevant changes can be made in the data file. Please read the docs: https://github.com/flokX/devShort/wiki

$short = htmlspecialchars($_GET["short"]);

$return_404 = array("favicon.ico", "assets/vendor/bootstrap/bootstrap.min.css.map", "assets/vendor/frappe-charts/frappe-charts.min.iife.js.map");
if (in_array($short, $return_404)) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// Counts the access to the given $name
function count_access($base_path, $name) {
    $filename = $base_path . DIRECTORY_SEPARATOR . "stats.json";
    $stats = json_decode(file_get_contents($filename), true);
    $stats[$name][mktime(0, 0, 0)] += 1;
    file_put_contents($filename, json_encode($stats, JSON_PRETTY_PRINT));
}

$base_path = __DIR__;
$config_content = json_decode(file_get_contents($base_path . DIRECTORY_SEPARATOR . "config.json"), true);

if (array_key_exists($short, $config_content)) {
    header("Location: " . $config_content[$short], $http_response_code=303);
    count_access($base_path, $short);
    exit;
} else if ($short === "") {
    header("Location: index.php", $http_response_code=301);
    exit;
} else {
    header("HTTP/1.1 404 Not Found");
    count_access($base_path, "404-request");
}

?>

<!doctype html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <link href="assets/icon.png" rel="icon">
    <title>404 | Shortlink not found</title>
    <link href="assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="assets/main.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <div class="container">
            <h1 class="mt-5">404 | Shortlink Not Found.</h1>
            <p class="lead">The requested shortlink <i><?php echo $short; ?></i> was not found on this server. It was either deleted, expired, misspelled or eaten by a monster.</p>
        </div>
    </main>
</body>

</html>
