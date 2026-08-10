<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apnaa Ghar Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #11223b;
            --sidebar-hover: rgba(255,255,255,0.05);
            --sidebar-active: #C79A4A;
            --sidebar-text: #FFFFFF;
            --sidebar-muted: #B0B0B0;
            --content-bg: #F8F8F6;
            --card-bg: #FFFFFF;
            --card-border: #E8E3DA;
            --card-shadow: 0 4px 30px rgba(0,0,0,0.03);
            --primary: #C79A4A;
            --primary-hover: #B8893A;
            --text-heading: #1C1C1C;
            --text-body: #6A6A6A;
            
            /* Status Colors */
            --status-active: #2E7D32;
            --status-pending: #E0A63E;
            --status-rejected: #C0392B;
            --status-info: #3B6FA0;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        
        body {
            background-color: var(--content-bg);
            color: var(--text-body);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6 { color: var(--text-heading); font-weight: 600; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100; border-right: 1px solid var(--primary);
        }
        
        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-header i { font-size: 1.5rem; color: var(--sidebar-active); }
        .sidebar-header h2 { font-size: 1.2rem; font-weight: 700; color: #fff; letter-spacing: 0.5px; }
        
        .nav-menu { padding: 1.5rem 0; flex-grow: 1; display: flex; flex-direction: column; gap: 4px; }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--sidebar-muted);
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
        }
        
        .nav-link i { width: 24px; font-size: 1.1rem; margin-right: 12px; transition: color 0.2s ease; }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background-color: var(--sidebar-active);
            transition: width 0.2s ease;
            z-index: -1;
        }
        
        .nav-link:hover, .nav-link.active { color: var(--sidebar-text); }
        .nav-link:hover::before, .nav-link.active::before { width: 100%; }
        
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.3); }

        /* Main Content */
        .main-content {
            flex-grow: 1;
            margin-left: 280px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .topbar {
            background: var(--card-bg);
            padding: 1.25rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--card-border);
            position: sticky;
            top: 0;
            z-index: 90;
        }
        
        .topbar-title { font-size: 1.25rem; font-weight: 600; color: var(--text-heading); }
        
        .admin-profile { display: flex; align-items: center; gap: 1.5rem; }
        .admin-profile span { font-weight: 500; font-size: 0.9rem; color: var(--text-heading); }
        
        .btn-logout {
            padding: 0.6rem 1.2rem;
            background: rgba(192, 57, 43, 0.1);
            color: var(--status-rejected);
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-logout:hover { background: var(--status-rejected); color: #fff; }
        
        .content-area { padding: 2.5rem; flex-grow: 1; }
        
        /* Cards */
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--card-border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover { transform: translateY(-2px); box-shadow: 0 8px 40px rgba(0,0,0,0.06); }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.25s ease-out;
            background: var(--primary);
            color: #fff;
        }
        .btn:hover { background: var(--primary-hover); transform: scale(1.02); box-shadow: 0 4px 15px rgba(199, 154, 74, 0.2); }
        .btn-danger { background: var(--status-rejected); }
        .btn-danger:hover { background: #a83225; box-shadow: 0 4px 15px rgba(192, 57, 43, 0.2); }
        
        /* Tables */
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th, td { padding: 1.2rem 1rem; text-align: left; border-bottom: 1px solid var(--card-border); }
        th { font-weight: 600; color: var(--text-heading); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid var(--card-border); }
        td { font-size: 0.95rem; color: var(--text-body); }
        tr { transition: background 0.2s ease; }
        tr:hover { background: #fcfcfc; }
        
        /* Forms & Custom UI */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; color: var(--text-heading); }
        .form-control {
            width: 100%; padding: 0.8rem 1rem; border: 1px solid var(--card-border); border-radius: 6px; font-size: 0.95rem;
            background: #fff; color: var(--text-heading); transition: all 0.2s;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(199, 154, 74, 0.1); }
        
        select.form-control { appearance: none; background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%231C1C1C%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E"); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto; padding-right: 2.5rem; }
        
        /* Custom File Upload UI */
        .custom-file-upload { position: relative; width: 100%; border: 2px dashed var(--card-border); border-radius: 8px; background: #fafafa; transition: all 0.2s; }
        .custom-file-upload:hover { border-color: var(--primary); background: #f4f9f8; }
        .custom-file-label { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; cursor: pointer; color: var(--text-body); font-weight: 500; }
        .custom-file-label i { font-size: 2rem; color: var(--primary); margin-bottom: 0.5rem; opacity: 0.8; }
        .custom-file-upload input[type="file"] { position: absolute; left: 0; top: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .file-preview-container { padding: 1rem; border-top: 1px solid var(--card-border); background: #fff; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; display: flex; justify-content: center; }
        .file-preview-image { max-width: 100%; max-height: 200px; border-radius: 4px; object-fit: contain; }

        /* Status Badges */
        .status-badge { padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; letter-spacing: 0.3px; }
        .status-badge.active { background: rgba(46, 125, 50, 0.1); color: var(--status-active); }
        .status-badge.pending { background: rgba(224, 166, 62, 0.1); color: var(--status-pending); }
        .status-badge.rejected { background: rgba(192, 57, 43, 0.1); color: var(--status-rejected); }
        .status-badge.info { background: rgba(59, 111, 160, 0.1); color: var(--status-info); }
        
        /* Animations */
        .fade-up { animation: fadeUp 0.6s ease-out forwards; opacity: 0; transform: translateY(15px); }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        
        /* Utility */
        .d-flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .align-center { align-items: center; }
        .gap-2 { gap: 0.5rem; }
        .gap-4 { gap: 1rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mb-4 { margin-bottom: 1rem; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header" style="justify-content: flex-start; padding: 1.5rem;">
        <div style="background: #fff; padding: 6px; border-radius: 8px; margin-right: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--primary);">
            <img src="../logo.png" alt="Apnaa Ghar" style="max-height: 35px; object-fit: contain;">
        </div>
        <h2 style="font-size: 1.15rem; margin: 0; color: #fff; letter-spacing: 0.5px;">ApnaaGhar</h2>
    </div>
    <div class="nav-menu">
        <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie"></i> Dashboard
        </a>
        <a href="manage_properties.php" class="nav-link <?php echo ($current_page == 'manage_properties.php' || $current_page == 'property_edit.php' || $current_page == 'property_add.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-city"></i> Properties
        </a>
        <a href="manage_enquiries.php" class="nav-link <?php echo ($current_page == 'manage_enquiries.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-envelope-open-text"></i> Enquiries
        </a>
        <a href="gallery.php" class="nav-link <?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-images"></i> Gallery
        </a>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="topbar-title">
            <?php 
                if($current_page == 'dashboard.php') echo 'Dashboard Overview';
                elseif($current_page == 'manage_properties.php' || $current_page == 'property_edit.php' || $current_page == 'property_add.php') echo 'Property Management';
                elseif($current_page == 'manage_enquiries.php') echo 'Enquiries Management';
                elseif($current_page == 'gallery.php') echo 'Gallery Management';
                else echo 'Admin Panel';
            ?>
        </div>
        <div class="admin-profile">
            <span><i class="fa-solid fa-circle-user" style="color:var(--primary); font-size:1.2rem; vertical-align:middle; margin-right:4px;"></i> <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" class="btn-logout" style="padding: 0.5rem 1rem; background: rgba(192,57,43,0.1); color: #C0392B; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.85rem; transition: all 0.2s;"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>
    
    <div class="content-area">



