<?php
// includes/header.php
// ======================================================
// HEADER DENGAN CSS INTERNAL (PASTI RAPIH DI MANA SAJA)
// ======================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Ujian Online</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ====================================================== */
        /* CSS GLOBAL - INTERNAL STYLE (PASTI TERLOAD) */
        /* ====================================================== */
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
        }
        
        /* ========== SIDEBAR ========== */
        .sidebar { 
            position: fixed; left: 0; top: 0; width: 280px; height: 100vh; 
            background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); 
            border-right: 1px solid rgba(255,255,255,0.1); 
            display: flex; flex-direction: column; z-index: 100; 
        }
        
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        
        .logo { display: flex; align-items: center; gap: 10px; font-size: 24px; font-weight: 700; color: white; }
        .logo i { color: #818cf8; font-size: 28px; }
        .logo .dot { color: #818cf8; }
        
        .sidebar-nav { flex: 1; padding: 20px 0; }
        
        .nav-item { 
            display: flex; align-items: center; gap: 12px; padding: 12px 20px; 
            color: rgba(255,255,255,0.7); text-decoration: none; 
            transition: all 0.3s ease; margin: 4px 12px; border-radius: 12px; 
        }
        .nav-item i { width: 24px; font-size: 18px; }
        .nav-item:hover { background: rgba(129,140,248,0.2); color: white; }
        
        .sidebar-footer { padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); }
        
        .user-info { 
            display: flex; align-items: center; gap: 12px; padding: 12px; 
            background: rgba(255,255,255,0.05); border-radius: 12px; margin-bottom: 12px; 
        }
        .user-info i { font-size: 32px; color: #818cf8; }
        .user-name { font-weight: 600; color: white; font-size: 14px; }
        .user-role { font-size: 12px; color: rgba(255,255,255,0.5); text-transform: capitalize; }
        
        .logout-btn { 
            display: flex; align-items: center; gap: 10px; padding: 10px 12px; 
            background: rgba(239,68,68,0.2); color: #f87171; text-decoration: none; 
            border-radius: 10px; transition: all 0.3s ease; 
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
        
        /* ========== CARDS ========== */
        .card-modern { 
            background: white; border-radius: 24px; padding: 24px; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
            transition: transform 0.2s ease; 
        }
        .card-modern:hover { transform: translateY(-2px); }
        
        /* ========== BUTTONS ========== */
        .btn-primary { 
            background: linear-gradient(135deg, #6366f1, #4f46e5); border: none; 
            padding: 10px 20px; border-radius: 12px; color: white; font-weight: 600; 
            cursor: pointer; text-decoration: none; display: inline-block; 
        }
        .btn-primary:hover { transform: scale(1.02); box-shadow: 0 10px 20px -5px rgba(99,102,241,0.4); }
        
        .btn-outline { 
            background: transparent; border: 2px solid #6366f1; color: #4f46e5; 
            padding: 8px 18px; border-radius: 12px; text-decoration: none; display: inline-block; 
        }
        .btn-outline:hover { background: #6366f1; color: white; }
        
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
        
        /* ========== DASHBOARD STATS ========== */
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
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar { width: 80px; }
            .sidebar .logo span, .sidebar .nav-item span, .sidebar .user-info div, .logout-btn span { display: none; }
            .sidebar .nav-item { justify-content: center; }
            .main-content.with-sidebar { margin-left: 80px; }
            .content-container { padding: 15px; }
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