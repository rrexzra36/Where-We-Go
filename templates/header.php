<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Journal</title>

    <!-- CSS Links (Bootstrap, Icons, Leaflet, Fonts, Custom) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <?php include 'sidebar.php'; // Call sidebar offcanvas 
    ?>

    <!-- Main wrapper for all page content -->
    <div class="page-content-wrapper">
        <!-- Top navbar with toggle button -->
        <nav class="navbar navbar-light bg-white shadow-sm sticky-top">
            <div class="container-fluid">
                <button class="btn btn-outline-secondary border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>
        </nav>

        <!-- Dynamic content from each page will be placed here -->
        <main class="container-fluid p-4 p-md-5">