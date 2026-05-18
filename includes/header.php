<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Aplikasi Ujian Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ====================================================== */
        /* CSS GLOBAL - INTERNAL STYLE */
        /* ====================================================== */
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        
        /* ========== SIDEBAR ========== */
        .sidebar { 
            position: fixed; 
            left: 0; 
            top: 0; 
            width: 280px; 
            height: 100vh; 
            background: rgba(15, 23, 42, 0.95); 
            backdrop-filter: blur(10px); 
            border-right: 1px solid rgba(255,255,255,0.1); 
            display: flex; 
            flex-direction: column; 
            z-index: 100; 
            transition: left 0.3s ease;
        }
        
        /* Wrapper untuk scrollable content */
        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            -webkit-overflow-scrolling: touch;
        }
        
        .sidebar-header { 
            padding: 24px 20px; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        
        .logo { display: flex; align-items: center; gap: 10px; font-size: 24px; font-weight: 700; color: white; }
        .logo i { color: #818cf8; font-size: 28px; }
        .logo .dot { color: #818cf8; }
        
        .sidebar-nav { 
            flex: 1; 
            padding: 20px 0;
        }
        
        .nav-item { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px 20px; 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            transition: all 0.3s ease; 
            margin: 4px 12px; 
            border-radius: 12px; 
        }
        .nav-item i { width: 24px; font-size: 18px; }
        .nav-item:hover { background: rgba(129,140,248,0.2); color: white; }
        .nav-item.active { background: linear-gradient(135deg, #818cf8, #4f46e5); color: white; }
        
        .sidebar-footer { 
            padding: 20px; 
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            background: rgba(15, 23, 42, 0.98);
        }
        
        .user-info { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            padding: 12px; 
            background: rgba(255,255,255,0.05); 
            border-radius: 12px; 
            margin-bottom: 12px; 
        }
        .user-info i { font-size: 32px; color: #818cf8; }
        .user-name { font-weight: 600; color: white; font-size: 14px; }
        .user-role { font-size: 12px; color: rgba(255,255,255,0.5); text-transform: capitalize; }
        
        .logout-btn { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            padding: 10px 12px; 
            background: rgba(239,68,68,0.2); 
            color: #f87171; 
            text-decoration: none; 
            border-radius: 10px; 
            transition: all 0.3s ease; 
            justify-content: center;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.4); color: white; }
        
        /* ========== MAIN CONTENT ========== */
        .main-content { margin-left: 0; min-height: 100vh; transition: margin-left 0.3s ease; }
        .main-content.with-sidebar { margin-left: 280px; }
        
        .content-container { 
            padding: 30px; 
            background: linear-gradient(135deg, #f5f7fa 0%, #eef2f7 100%); 
            min-height: 100vh; 
        }
        
        /* ========== BUTTONS ========== */
        .btn-primary { 
            background: linear-gradient(135deg, #6366f1, #4f46e5); border: none; 
            padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; 
            cursor: pointer; text-decoration: none; display: inline-block; 
            transition: all 0.3s ease;
        }
        .btn-primary:hover { transform: scale(1.02); box-shadow: 0 10px 20px -5px rgba(99,102,241,0.4); }
        
        .btn-outline { 
            background: transparent; border: 2px solid #6366f1; color: #4f46e5; 
            padding: 8px 18px; border-radius: 12px; text-decoration: none; display: inline-block; 
            transition: all 0.3s ease;
        }
        .btn-outline:hover { background: #6366f1; color: white; }
        
        /* ========== CARDS ========== */
        .card-modern { 
            background: white; border-radius: 24px; padding: 24px; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
            transition: transform 0.2s ease; 
        }
        .card-modern:hover { transform: translateY(-2px); }
        
        /* ========== FORM ========== */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #1e293b; }
        
        .form-control { 
            width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; 
            border-radius: 14px; font-size: 14px; transition: all 0.3s ease; 
        }
        .form-control:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
        
        /* ========== TABLE ========== */
        .table-modern { width: 100%; background: white; border-radius: 20px; overflow: hidden; border-collapse: collapse; }
        .table-modern thead tr { background: linear-gradient(135deg, #1e293b, #0f172a); color: white; }
        .table-modern th, .table-modern td { padding: 12px 20px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table-modern tr:hover td { background: #f8fafc; }
        
        /* ========== DASHBOARD ========== */
        .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 24px; }
        
        .stat-card { 
            background: white; border-radius: 24px; padding: 24px; 
            display: flex; align-items: center; justify-content: space-between; 
        }
        .stat-icon { 
            width: 60px; height: 60px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe); 
            border-radius: 20px; display: flex; align-items: center; justify-content: center; 
        }
        .stat-icon i { font-size: 28px; color: #4f46e5; }
        .stat-info h3 { font-size: 28px; font-weight: 800; color: #0f172a; }
        .stat-info p { color: #64748b; font-size: 14px; }
        
        /* ========== PAGE HEADER ========== */
        .page-header { margin-bottom: 24px; }
        .page-title { font-size: 28px; font-weight: 700; color: #0f172a; }
        .page-subtitle { color: #64748b; margin-top: 4px; }
        
        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-top: 32px; }
        
        /* ========== ALERT ========== */
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; }
        .alert.error { background: #fee2e2; color: #dc2626; }
        .alert.success { background: #dcfce7; color: #16a34a; }
        .alert.info { background: #e0e7ff; color: #4338ca; }
        
        /* ========== AUTH PAGES ========== */
        .auth-container { 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            padding: 20px;
        }
        .auth-card { 
            background: white; border-radius: 32px; padding: 40px; 
            width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); 
        }
        .auth-header { text-align: center; margin-bottom: 32px; }
        .auth-header i { font-size: 48px; color: #4f46e5; margin-bottom: 16px; }
        .auth-header h2 { font-size: 24px; color: #0f172a; }
        .auth-header p { color: #64748b; margin-top: 8px; }
        .auth-footer { text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
        .auth-footer a { color: #4f46e5; text-decoration: none; }
        
        /* ========== UJIAN ========== */
        .ujian-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 20px;
            padding: 20px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            flex-wrap: wrap;
            gap: 10px;
        }
        .timer-box {
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 40px;
            font-family: monospace;
            font-size: 24px;
            font-weight: 700;
        }
        .warning-box {
            background: rgba(245,158,11,0.2);
            padding: 8px 16px;
            border-radius: 40px;
            color: #fbbf24;
            font-size: 14px;
        }
        .soal-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .soal-number {
            background: #4f46e5;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 12px;
        }
        .radio-group {
            margin: 12px 0 0 20px;
        }
        .radio-group label {
            margin-left: 8px;
            margin-right: 20px;
            cursor: pointer;
        }
        
        /* ========== BADGE ========== */
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-danger { background: #fee2e2; color: #dc2626; }
        .badge-warning { background: #fed7aa; color: #ea580c; }
        .badge-success { background: #dcfce7; color: #16a34a; }
        
        /* ========== RESPONSIVE DESIGN ========== */
        
        /* Tablet (max-width: 1024px) */
        @media (max-width: 1024px) {
            .sidebar { width: 240px; }
            .main-content.with-sidebar { margin-left: 240px; }
            .content-container { padding: 20px; }
            .dashboard-grid, .menu-grid { gap: 16px; }
            .stat-card { padding: 16px; }
            .stat-info h3 { font-size: 24px; }
        }
        
        /* Mobile Landscape (max-width: 768px) */
        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
                width: 280px;
            }
            .sidebar.show { left: 0; }
            
            .main-content.with-sidebar { margin-left: 0; }
            
            .menu-toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1001;
                background: #4f46e5;
                color: white;
                border: none;
                padding: 10px 15px;
                border-radius: 8px;
                cursor: pointer;
                font-size: 18px;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show { display: block; }
            
            .content-container { padding: 60px 15px 15px 15px; }
            
            .dashboard-grid, .menu-grid { grid-template-columns: 1fr; }
            
            .card-modern { overflow-x: auto; }
            .table-modern { min-width: 600px; }
            
            .form-control, .btn-primary, .btn-outline { font-size: 16px; }
            
            .auth-card { padding: 24px; margin: 15px; }
            .auth-header h2 { font-size: 20px; }
            
            .ujian-header { flex-direction: column; text-align: center; }
            .timer-box { font-size: 20px; }
            
            /* Sidebar footer mobile */
            .sidebar-footer {
                position: sticky;
                bottom: 0;
                background: rgba(15, 23, 42, 0.98);
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
            }
            
            .logout-btn {
                margin-top: 5px;
            }
        }
        
        /* Mobile Portrait (max-width: 480px) */
        @media (max-width: 480px) {
            .content-container { padding: 50px 12px 12px 12px; }
            
            .page-title { font-size: 22px; }
            .page-subtitle { font-size: 12px; }
            
            .stat-card { padding: 12px; }
            .stat-info h3 { font-size: 22px; }
            .stat-info p { font-size: 12px; }
            .stat-icon { width: 45px; height: 45px; }
            .stat-icon i { font-size: 20px; }
            
            .card-modern { padding: 16px; }
            .card-modern h3 { font-size: 16px; }
            
            .btn-primary, .btn-outline { padding: 8px 16px; font-size: 14px; }
        }
    </style>
</head>
<body>
<div class="app-wrapper">
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php include __DIR__ . '/navbar.php'; ?>
    <?php endif; ?>
    <main class="main-content <?= isset($_SESSION['user_id']) ? 'with-sidebar' : 'full-width' ?>">
        <div class="content-container">