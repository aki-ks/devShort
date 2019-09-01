<?php

// All relevant changes can be made in the data file. Please read the docs: https://github.com/flokX/devShort/wiki

$config_path = __DIR__ . DIRECTORY_SEPARATOR . "config.json";
$config_content = json_decode(file_get_contents($config_path), true);
$stats_path = __DIR__ . DIRECTORY_SEPARATOR . "stats.json";
$stats_content = json_decode(file_get_contents($stats_path), true);

// Filter the names that the admin interface doesn't break
function filter_name($nameRaw) {
    $name = filter_var($nameRaw, FILTER_SANITIZE_STRING);
    $name = str_replace(" ", "-", $name);
    $name = preg_replace("/[^A-Za-z0-9-_]/", "", $name);
    return $name;
}

// API functions to delete and add the shortlinks via the admin panel
if (isset($_GET["delete"]) || isset($_GET["add"])) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($_GET["delete"])) {
        unset($config_content[$data["name"]]);
        unset($stats_content[$data["name"]]);
    } else if (isset($_GET["add"])) {
        $filtered = array("name" => filter_name($data["name"]),
                          "url" => filter_var($data["url"], FILTER_SANITIZE_URL));
        if (!filter_var($filtered["url"], FILTER_VALIDATE_URL)) {
            echo "{\"status\": \"unvalid-url\"}";
            exit;
        }
        $config_content[$filtered["name"]] = $filtered["url"];
        $stats_content[$filtered["name"]] = array();
    }
    file_put_contents($config_path, json_encode($config_content, JSON_PRETTY_PRINT));
    file_put_contents($stats_path, json_encode($stats_content, JSON_PRETTY_PRINT));
    echo "{\"status\": \"successful\"}";
    exit;
}

?>

<!doctype html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <link href="../assets/icon.png" rel="icon">
    <title>Admin panel</title>
    <link href="../assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/main.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

    <main class="flex-shrink-0">
        <div class="container">
            <div class="card mt-3 mb-3">
                <div class="card-body">
                    <h5 class="card-title">Add shortlink <small><a class="card-link" id="refresh" href="#refresh">Refresh charts</a></small></h5>
                    <form class="form-inline" id="add-form">
                        <label class="sr-only" for="name">Name</label>
                        <input class="form-control mb-2 mr-sm-2" id="name" type="text" placeholder="Link1" required>
                        <label class="sr-only" for="url">URL (destination)</label>
                        <input class="form-control mb-2 mr-sm-2" id="url" type="url" placeholder="https://example.com" required>
                        <button class="btn btn-primary mb-2" type="submit">Add</button>
                        <div id="status"></div>
                    </form>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <div class="spinner-border text-primary" id="spinner" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div id="charts"></div>
        </div>
    </main>

    <script src="../assets/vendor/frappe-charts/frappe-charts.min.iife.js"></script>
    <script src="main.js"></script>
</body>

</html>
