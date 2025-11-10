<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Login</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <!-- <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon"> -->

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad+Flux:wght@100..1000&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-chubby/css/uicons-regular-chubby.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-thin-rounded/css/uicons-thin-rounded.css'>

    <!-- Jquey & Javascript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   
    <?php 
        if (isset($common_files) && $common_files == 'main') { ?>    
            <!-- Stylesheet  -->
            
            <!-- Icons -->
            <link rel="stylesheet" href="../styles/Admin/boxicons.css" />
             <!-- Jquey & Javascript -->
    <?php } ?>

    <?php 
        if (isset($common_files) && $common_files == 'sub_files') { ?>    
            <!-- Stylesheet -->
            <link rel="stylesheet" href="../../styles/Admin/core.css"/>
            <link rel="stylesheet" href="../../styles/Admin/theme-default.css" />
            <link rel="stylesheet" href="../../styles/Admin/demo.css" />
            <link rel="stylesheet" href="../../styles/Admin/perfect-scrollbar.css" />
            <link rel="stylesheet" href="../../styles/Admin/pages/custom.css" />
            <!-- Icons -->
            <link rel="stylesheet" href="../../styles/Admin/boxicons.css" />
            <!-- Jquey & Javascript --> 
            <script src="../../js/admin/header.js"></script>
            <script src="../../js/admin/helpers.js"></script>
            <script src="../../js/admin/config.js"></script>
            <script src="../../js/main.js"></script>
    <?php } ?>

    <?php 
        if (isset($page) && $page == 'login') { ?>    
            <!-- Stylesheet -->
            <link rel="stylesheet" href="../../css/login.css" />
            <!-- js  -->
             <script src="../../js/login.js"></script>
    <?php } ?>
</head>