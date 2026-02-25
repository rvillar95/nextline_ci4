<?php
// Obtener datos de la empresa directamente
$empresaModel = new \App\Models\Empresa();
$empresaData = $empresaModel->getDatosParaPDF();
$empresaContacto = [
    'direccion' => $empresaData['direccion'] ?? 'Santiago, Chile',
    'telefono' => $empresaData['telefono'] ?? '+56 9 1234 5678',
    'email' => $empresaData['email'] ?? 'info@mansanchez.cl',
    'sitio_web' => $empresaData['sitio_web'] ?? 'www.mansanchez.cl'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?? 'VitaSync - Gestión nutricional' ?></title>
    <link rel="icon" href="<?= base_url('lib/logo/icono-transparente.png') ?>" type="image/png" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="<?= $description ?? 'VitaSync - Plataforma de gestión para nutricionistas: agenda, pacientes, historiales y más' ?>" name="description" />
    <meta content="<?= $keywords ?? 'vitasync, nutrición, nutricionista, agenda, consultas, pacientes, chile' ?>" name="keywords" />
    <meta content="VitaSync" name="author" />
    
        <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?= current_url() ?>" />
    <meta property="og:title" content="<?= $title ?? 'VitaSync - Gestión nutricional' ?>" />
    <meta property="og:description" content="<?= $description ?? 'Plataforma de gestión para nutricionistas' ?>" />
    <meta property="og:image" content="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:site_name" content="VitaSync" />
    <meta property="og:locale" content="es_CL" />
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:url" content="<?= current_url() ?>" />
    <meta name="twitter:title" content="<?= $title ?? 'VitaSync - Gestión nutricional' ?>" />
    <meta name="twitter:description" content="<?= $description ?? 'Plataforma de gestión para nutricionistas' ?>" />
    <meta name="twitter:image" content="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" />
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    
    <!-- CSS Files - Critical CSS first -->
    <link href="<?= base_url('lib/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/style.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/navigation.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/vitasync-colors.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/buttons-ripple-fix.css') ?>" rel="stylesheet" type="text/css" />
    
    <!-- Non-critical CSS - Deferred loading -->
    <link href="<?= base_url('lib/css/animate.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <link href="<?= base_url('lib/css/owl.carousel.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <link href="<?= base_url('lib/css/owl.theme.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <link href="<?= base_url('lib/css/owl.transitions.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <link href="<?= base_url('lib/css/magnific-popup.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <link href="<?= base_url('lib/css/jquery.countdown.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    
    <!-- Font Awesome Icons - Deferred -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css" /></noscript>
    
    <!-- Icofont Icons - Deferred -->
    <link href="<?= base_url('lib/css/icofont.min.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    
    <!-- Color schemes - Deferred -->
    <link href="<?= base_url('lib/css/colors/scheme-01.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    <link href="<?= base_url('lib/css/coloring.css') ?>" rel="stylesheet" type="text/css" media="print" onload="this.media='all'" />
    
    <!-- RS5.0 Stylesheet - Deferred -->
    <link rel="stylesheet" href="<?= base_url('lib/css/settings.css') ?>" type="text/css" media="print" onload="this.media='all'" />
    <link rel="stylesheet" href="<?= base_url('lib/css/layers.css') ?>" type="text/css" media="print" onload="this.media='all'" />
    
    <!-- Custom CSS for page spacing and navbar -->
    <style>
        .page-content {
            margin-top: 0;
            padding-top: 120px;
        }
        
        @media (max-width: 768px) {
            .page-content {
                padding-top: 100px;
            }
        }
        
        /* ===== SCROLL SUAVE ===== */
        html {
            scroll-behavior: smooth;
        }
        
        body {
            scroll-behavior: smooth;
        }
        
        /* ===== WRAPPER Y BODY ===== */
        #wrapper {
            background: transparent !important;
        }
        
        body {
            background-color: #ffffff;
        }
        
        #content {
            position: relative;
            z-index: 1;
        }
        
        /* Asegurar que las secciones estén debajo del menú móvil */
        section {
            position: relative;
            z-index: 1;
        }
        
        .hero-section-modern,
        .no-bottom,
        .no-top {
            position: relative;
            z-index: 1;
        }
        
        /* ===== HEADER Y NAVBAR MODERNO ===== */
        
        /* Topbar Moderno - VitaSync */
        .topbar-modern {
            background: var(--vitasync-primary);
            padding: 3px 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 9998;
        }
        
        .topbar-content-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .topbar-left-modern {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .contact-info-modern {
            display: flex;
            gap: 15px;
        }
        
        .contact-item-modern {
            display: flex;
            align-items: center;
            gap: 4px;
            color: rgba(255,255,255,0.95);
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .contact-item-modern i {
            color: #fafeff;
            font-size: 0.7rem;
        }
        
        .topbar-right-modern {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .social-links-modern {
            display: flex;
            gap: 8px;
        }
        
        .social-link-modern {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            background: rgba(255,255,255,0.2);
            color: #fafeff;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.4);
            font-size: 0.7rem;
        }
        
        .social-link-modern:hover {
            background: #fafeff;
            color: var(--vitasync-primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(77, 203, 165, 0.4);
        }
        
        .btn-topbar-modern {
            background: #fafeff;
            color: var(--vitasync-primary);
            padding: 4px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn-topbar-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(77, 203, 165, 0.4);
            color: var(--vitasync-primary) !important;
            background: var(--vitasync-muted) !important;
            text-decoration: none;
        }
        
        /* Header Moderno - VitaSync */
        .header-modern {
            background: #fff !important;
            backdrop-filter: blur(10px);
            padding: 5px 0;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(77, 203, 165, 0.15);
            border-bottom: 1px solid var(--vitasync-muted);
            transition: box-shadow 0.3s ease;
        }
        
        .header-content-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: transparent;
        }
        
        /* Logo Moderno */
        .logo-modern {
            flex-shrink: 0;
        }
        
        .logo-link-modern {
            display: block;
            text-decoration: none;
        }
        
        .logo-img-modern {
            height: 80px;
            width: auto;
            transition: all 0.3s ease;
        }
        
        .logo-link-modern:hover .logo-img-modern {
            transform: scale(1.05);
        }
        
        /* Navigation Moderna */
        .nav-modern {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        
        .nav-menu-modern {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 5px;
        }
        
        .nav-item-modern {
            position: relative;
        }
        
        .nav-link-modern {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 15px;
            color: #2d3748;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link-modern i {
            font-size: 1rem;
            margin-bottom: 3px;
            transition: all 0.3s ease;
        }
        
        .nav-link-modern span {
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .nav-link-modern:hover {
            background: var(--vitasync-muted);
            color: var(--vitasync-primary);
            transform: translateY(-2px);
        }
        
        .nav-link-modern.active {
            background: var(--vitasync-primary);
            color: white !important;
            box-shadow: 0 4px 15px rgba(77, 203, 165, 0.35);
        }
        
        .nav-link-modern.active i {
            color: white;
        }
        
        /* Header Actions */
        .header-actions-modern {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .phone-modern {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: var(--vitasync-muted);
            border-radius: 25px;
            border: 1px solid rgba(77, 203, 165, 0.3);
        }
        
        .phone-icon-modern {
            width: 35px;
            height: 35px;
            background: var(--vitasync-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .phone-info-modern {
            display: flex;
            flex-direction: column;
        }
        
        .phone-label-modern {
            font-size: 0.75rem;
            color: #2d3748;
            font-weight: 500;
        }
        
        .phone-number-modern {
            font-size: 0.9rem;
            color: var(--vitasync-primary);
            font-weight: 700;
        }
        
        /* Mobile Menu Button - VitaSync */
        .mobile-menu-btn-modern {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 35px;
            height: 35px;
            background: var(--vitasync-muted);
            border: 2px solid var(--vitasync-primary);
            border-radius: 8px;
            cursor: pointer;
            padding: 6px;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-btn-modern:hover {
            background: var(--vitasync-primary);
            transform: scale(1.05);
        }
        
        .mobile-menu-btn-modern span {
            width: 100%;
            height: 3px;
            background: var(--vitasync-primary);
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        /* Animación para menú móvil */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 1200px) {
            .nav-menu-modern {
                gap: 2px;
            }
            
            .nav-link-modern {
                padding: 10px 15px;
            }
            
            .nav-link-modern span {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 993px) {
            /* Ocultar topbar en tablets y móviles */
            .topbar-modern {
                display: none;
            }
            
            /* Sobrescribir el CSS del template para header-mobile */
            header.header-mobile,
            header.header-modern.header-mobile,
            .header-modern {
                padding: 15px 0 !important;
                min-height: 120px !important;
                height: 120px !important;
                display: flex !important;
                align-items: center !important;
                overflow: visible !important;
            }
            
            .header-content-modern {
                flex-wrap: nowrap;
                width: 100%;
            }
            
            .logo-img-modern {
                height: 70px !important;
                width: auto;
            }
            
            .phone-modern {
                display: none;
            }
            
            .mobile-menu-btn-modern {
                display: flex;
                flex-shrink: 0;
            }
            
            /* Menú móvil desde 993px hacia abajo */
            .nav-modern {
                display: block !important;
                position: fixed !important;
                top: 120px !important;
                left: 0 !important;
                right: 0 !important;
                background: #fff !important;
                backdrop-filter: blur(10px);
                padding: 20px !important;
                box-shadow: 0 8px 30px rgba(77, 203, 165, 0.2) !important;
                border-top: 2px solid var(--vitasync-primary) !important;
                z-index: 2147483647 !important;
                max-height: calc(100vh - 120px) !important;
                min-height: 450px !important;
                overflow-y: auto !important;
                width: 100% !important;
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: transform 0.3s ease, opacity 0.3s ease, visibility 0.3s;
                pointer-events: none;
            }
            
            .nav-modern.active {
                transform: translateY(0) !important;
                opacity: 1 !important;
                visibility: visible !important;
                pointer-events: auto !important;
                min-height: 450px !important;
                display: block !important;
                z-index: 2147483647 !important;
            }
            
            .nav-menu-modern {
                display: flex !important;
                flex-direction: column;
                gap: 12px;
                width: 100%;
            }
            
            .nav-link-modern {
                padding: 16px 20px;
                border-radius: 10px;
                background: var(--vitasync-muted);
                border: 1px solid rgba(77, 203, 165, 0.3);
                color: #2d3748 !important;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 12px;
            }
            
            .nav-link-modern i {
                font-size: 1.2rem;
                color: var(--vitasync-primary);
                margin-bottom: 0;
            }
            
            .nav-link-modern span {
                color: #2d3748 !important;
                font-weight: 600;
            }
            
            .nav-link-modern:hover {
                background: var(--vitasync-primary);
                transform: translateX(5px);
                border-color: var(--vitasync-primary);
                color: white !important;
            }
            
            .nav-link-modern:hover i,
            .nav-link-modern:hover span { color: white !important; }
            
            .nav-link-modern.active {
                background: var(--vitasync-primary);
                color: white !important;
                box-shadow: 0 4px 15px rgba(77, 203, 165, 0.4);
                border-color: transparent;
            }
            
            .nav-link-modern.active i,
            .nav-link-modern.active span {
                color: white !important;
            }
        }
        
        @media (max-width: 768px) {
            /* Ajustes adicionales para móviles pequeños */
            header.header-mobile,
            header.header-modern.header-mobile,
            .header-modern {
                min-height: 100px !important;
                height: 100px !important;
            }
            
            .logo-img-modern {
                height: 60px !important;
                width: auto;
            }
            
            .nav-modern {
                top: 100px !important;
                max-height: calc(100vh - 100px) !important;
                min-height: 400px !important;
                z-index: 2147483647 !important;
            }
        }
        
        /* Botones VitaSync */
        .btn-custom {
            background-color: var(--vitasync-primary) !important;
            color: #ffffff !important;
            border: 2px solid var(--vitasync-primary) !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            box-shadow: 0 4px 15px rgba(77, 203, 165, 0.35) !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-custom:hover {
            background-color: #3ab892 !important;
            color: #ffffff !important;
            border-color: #3ab892 !important;
            box-shadow: 0 6px 20px rgba(77, 203, 165, 0.45) !important;
            transform: translateY(-2px) !important;
        }
        
        .btn-custom.btn-black.light {
            background-color: #ffffff !important;
            color: var(--vitasync-primary) !important;
            border: 2px solid #ffffff !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3) !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-custom.btn-black.light:hover {
            background-color: var(--vitasync-muted) !important;
            color: var(--vitasync-primary) !important;
            border-color: var(--vitasync-muted) !important;
            transform: translateY(-2px) !important;
        }
        
        .text-light { color: #ffffff !important; }
        .text-light h1, .text-light h2, .text-light h3, .text-light h4, .text-light h5, .text-light h6 { color: #ffffff !important; }
        .text-light p { color: rgba(255,255,255,0.9) !important; }
        .container { position: relative !important; z-index: 10 !important; }
        .p-title { color: var(--vitasync-primary) !important; font-weight: 600 !important; }
        h2 { color: #2d3748 !important; }
        .small-border { background-color: var(--vitasync-primary) !important; }
        .estrellas-calificacion { margin: 10px 0; text-align: center; }
        .estrellas-calificacion i { font-size: 5px; margin: 0 2px; color: var(--vitasync-primary) !important; }
        .estrellas-calificacion .fa-star-o { color: #ccc !important; }
        .de_testi h3 { margin-bottom: 10px !important; }
        .de_testi .estrellas-calificacion { margin: 10px 0 15px 0 !important; }
        #section-highlight { background: linear-gradient(135deg, var(--vitasync-primary), var(--vitasync-muted)) !important; }
        #section-highlight .p-title, #section-highlight h2 { color: #fff !important; }
        #section-highlight p { color: rgba(255,255,255,0.9) !important; }
        .de_count { background: rgba(77, 203, 165, 0.2) !important; border-radius: 10px !important; padding: 20px !important; margin-bottom: 20px !important; }
        .de_count h3 { color: var(--vitasync-primary) !important; font-size: 2.5rem !important; font-weight: bold !important; }
        .de_count p { color: #2d3748 !important; font-size: 1rem !important; }
        
        /* ===== FOOTER MODERNO - VitaSync ===== */
        
        .footer-modern {
            background: linear-gradient(to bottom, var(--vitasync-primary) 0%, #3ab892 100%);
            color: white;
            padding: 80px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .footer-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-footer" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(250,254,255,0.15)"/><circle cx="75" cy="75" r="1" fill="rgba(250,254,255,0.15)"/><circle cx="50" cy="10" r="0.5" fill="rgba(250,254,255,0.08)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-footer)"/></svg>');
            opacity: 0.5;
        }
        
        .footer-widget-modern {
            margin-bottom: 40px;
            position: relative;
            z-index: 10;
        }
        
        .footer-logo-modern {
            margin-bottom: 25px;
        }
        
        .footer-logo-modern img {
            max-height: 60px;
            width: auto;
            margin-bottom: 15px;
        }
        
        .company-name-modern {
            color: #fafeff;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .company-description-modern {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }
        
        .contact-info-modern {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .contact-item-modern {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 0;
        }
        
        .contact-icon-modern {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .contact-details-modern { display: flex; flex-direction: column; gap: 2px; }
        
        .contact-label-modern {
            color: rgba(255,255,255,0.85);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .contact-value-modern {
            color: white;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .contact-value-modern:hover { color: #fafeff; opacity: 0.95; }
        
        .footer-title-modern {
            color: #fafeff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-title-modern i {
            font-size: 1.1rem;
        }
        
        .footer-links-modern {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-links-modern li {
            margin-bottom: 12px;
        }
        
        .footer-links-modern a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .footer-links-modern a:hover {
            color: #fafeff;
            transform: translateX(5px);
        }
        
        .footer-links-modern a i {
            color: rgba(255,255,255,0.9);
            font-size: 0.8rem;
            width: 12px;
        }
        
        .newsletter-description-modern {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        
        .newsletter-form-modern {
            margin-bottom: 20px;
        }
        
        .newsletter-input-group-modern {
            display: flex;
            background: white;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .newsletter-input-modern {
            flex: 1;
            padding: 15px 20px;
            border: none;
            outline: none;
            font-size: 0.9rem;
            background: transparent;
        }
        
        .newsletter-input-modern::placeholder {
            color: #999;
        }
        
        .newsletter-btn-modern {
            background: #fafeff;
            color: var(--vitasync-primary);
            border: none;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .newsletter-btn-modern:hover {
            background: var(--vitasync-muted);
            color: var(--vitasync-primary);
            transform: scale(1.05);
        }
        
        .newsletter-privacy-modern {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .newsletter-privacy-modern i { color: #fafeff; }
        
        /* ===== CONTACTO FOOTER CSS ===== */
        
        .contact-description-modern {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }
        
        .contact-methods-modern {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .contact-method-modern {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
        }
        
        .contact-method-modern:hover {
            background: rgba(255,255,255,0.12);
            transform: translateX(5px);
            border-color: rgba(255,255,255,0.4);
        }
        
        .contact-method-icon-modern {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .contact-method-content-modern { flex: 1; }
        
        .contact-method-content-modern h5 {
            color: #fafeff;
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .contact-method-content-modern p, .contact-method-content-modern a {
            color: white;
            font-size: 0.85rem;
            margin: 0;
            line-height: 1.4;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .contact-method-content-modern a:hover { color: #fafeff; opacity: 0.9; }
        
        .contact-actions-modern { margin-top: 20px; }
        
        .btn-contact-footer-modern {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            background: #fafeff;
            color: var(--vitasync-primary);
            text-decoration: none;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .btn-contact-footer-modern:hover {
            background: var(--vitasync-muted);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(77, 203, 165, 0.35);
            color: var(--vitasync-primary) !important;
            text-decoration: none;
        }
        
        .subfooter-modern {
            background: rgba(0, 0, 0, 0.15);
            padding: 25px 0;
            border-top: 1px solid rgba(255,255,255,0.2);
            margin-top: 40px;
        }
        
        .copyright-modern {
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .social-icons-modern {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }
        
        .social-icon-modern {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fafeff;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-icon-modern:hover {
            background: #fafeff;
            color: var(--vitasync-primary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .footer-modern {
                padding: 60px 0 0;
            }
            
            .social-icons-modern {
                justify-content: center;
                margin-top: 20px;
            }
            
            .copyright-modern {
                text-align: center;
            }
        }
        
        @media (max-width: 768px) {
            .footer-modern {
                padding: 40px 0 0;
            }
            
            .company-name-modern {
                font-size: 1.5rem;
            }
            
            .footer-title-modern {
                font-size: 1.1rem;
            }
            
            .contact-item-modern {
                gap: 12px;
            }
            
            .contact-icon-modern {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
            
            .contact-method-modern {
                padding: 12px;
                gap: 12px;
            }
            
            .contact-method-icon-modern {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .company-name-modern {
                font-size: 1.3rem;
            }
            
            .contact-item-modern {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .contact-icon-modern {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }
            
            .social-icons-modern {
                gap: 10px;
            }
            
            .social-icon-modern {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <!-- Top Bar Moderno -->
        <div id="topbar-modern" class="topbar-modern">
            <div class="container">
                <div class="topbar-content-modern">
                    <div class="topbar-left-modern">
                        
                            <div class="contact-item-modern">
                                <i class="fas fa-phone"></i>
                                <span><?= esc($empresaContacto['telefono']) ?></span>
                            </div>
                            <div class="contact-item-modern">
                                <i class="fas fa-envelope"></i>
                                <span><?= esc($empresaContacto['email']) ?></span>
                            </div>
                    </div>
                    <div class="topbar-right-modern">
                        <div class="social-links-modern">
                            <a href="https://www.facebook.com" target="_blank" rel="noopener" class="social-link-modern" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com" target="_blank" rel="noopener" class="social-link-modern" title="Instagram"><i class="fab fa-instagram"></i></a>
                        </div>
                        <div class="topbar-actions-modern">
                            <a href="<?= base_url('reservar') ?>" class="btn-topbar-modern">
                                <i class="fas fa-calendar-check"></i> Reservar hora
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Moderno -->
        <header id="header-modern" class="header-modern">
            <div class="container">
                <div class="header-content-modern">
                    <!-- Logo -->
                    <div class="logo-modern">
                        <a href="<?= base_url() ?>" class="logo-link-modern">
                            <img src="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" alt="VitaSync" class="logo-img-modern" style="max-height: 75px; width: auto;" loading="eager">
                        </a>
                    </div>
                    
                    <!-- Navigation --> 
                    <nav class="nav-modern">
                        <ul class="nav-menu-modern">
                            <li class="nav-item-modern">
                                <a href="<?= base_url() ?>" class="nav-link-modern <?= (uri_string() == '' || uri_string() == 'home') ? 'active' : '' ?>">
                                    <i class="fas fa-home"></i>
                                    <span>Inicio</span>
                                </a>
                            </li>
                            <li class="nav-item-modern">
                                <a href="<?= base_url('servicios') ?>" class="nav-link-modern <?= (uri_string() == 'servicios') ? 'active' : '' ?>">
                                    <i class="fas fa-tools"></i>
                                    <span>Servicios</span>
                                </a>
                            </li>
                            <li class="nav-item-modern">
                                <a href="<?= base_url('proyectos') ?>" class="nav-link-modern <?= (uri_string() == 'proyectos') ? 'active' : '' ?>">
                                    <i class="fas fa-building"></i>
                                    <span>Proyectos</span>
                                </a>
                            </li>
                            <li class="nav-item-modern">
                                <a href="<?= base_url('galeria') ?>" class="nav-link-modern <?= (strpos(uri_string(), 'galeria') === 0) ? 'active' : '' ?>">
                                    <i class="fas fa-images"></i>
                                    <span>Galería</span>
                                </a>
                            </li>
                            <li class="nav-item-modern">
                                <a href="<?= base_url('nosotros') ?>" class="nav-link-modern <?= (uri_string() == 'nosotros') ? 'active' : '' ?>">
                                    <i class="fas fa-users"></i>
                                    <span>Nosotros</span>
                                </a>
                            </li>
                            <li class="nav-item-modern">
                                <a href="<?= base_url('contacto') ?>" class="nav-link-modern <?= (uri_string() == 'contacto') ? 'active' : '' ?>">
                                    <i class="fas fa-envelope"></i>
                                    <span>Contacto</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    
                    <!-- Header Actions -->
                    <div class="header-actions-modern">
                        <div class="phone-modern">
                            <div class="phone-icon-modern">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="phone-info-modern">
                                <span class="phone-label-modern">¿Necesitas ayuda?</span>
                                <span class="phone-number-modern"><?= esc($empresaContacto['telefono']) ?></span>
                            </div>
                        </div>
                        <button class="mobile-menu-btn-modern" id="mobile-menu-btn">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main role="main" aria-label="Contenido principal">
            <div class="no-bottom no-top" id="content">
                <?= $this->renderSection('content') ?>
            </div>
        </main>

        <!-- Footer Moderno -->
        <footer class="footer-modern">
            <div class="footer-pattern"></div>
            <div class="container">
                <div class="row">
                    <!-- Información de la Empresa -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-widget-modern">
                            <div class="footer-logo-modern">
                                <a href="<?= base_url() ?>">
                                    <img alt="VitaSync" src="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" style="max-height: 44px; width: auto;" loading="lazy" />
                                </a>
                                <h3 class="company-name-modern">VitaSync</h3>
                            </div>
                            <p class="company-description-modern">
                                Plataforma de gestión para nutricionistas: agenda de citas, historiales clínicos, planes alimentarios y más.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Funcionalidades -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget-modern">
                            <h4 class="footer-title-modern">
                                <i class="fas fa-th-large"></i> Sistema
                            </h4>
                            <ul class="footer-links-modern">
                                <li><a href="<?= base_url() ?>#funcionalidades"><i class="fas fa-calendar-alt"></i> Agenda</a></li>
                                <li><a href="<?= base_url() ?>#funcionalidades"><i class="fas fa-user-friends"></i> Pacientes</a></li>
                                <li><a href="<?= base_url() ?>#funcionalidades"><i class="fas fa-file-medical"></i> Historial clínico</a></li>
                                <li><a href="<?= base_url() ?>#funcionalidades"><i class="fas fa-utensils"></i> Plan alimentario</a></li>
                                <li><a href="<?= base_url() ?>#funcionalidades"><i class="fas fa-credit-card"></i> Pagos</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Enlaces -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-widget-modern">
                            <h4 class="footer-title-modern">
                                <i class="fas fa-link"></i> Enlaces
                            </h4>
                            <ul class="footer-links-modern">
                                <li><a href="<?= base_url() ?>"><i class="fas fa-home"></i> Inicio</a></li>
                                <li><a href="<?= base_url('reservar') ?>"><i class="fas fa-calendar-check"></i> Reservar hora</a></li>
                                <li><a href="<?= base_url('contacto') ?>"><i class="fas fa-envelope"></i> Contacto</a></li>
                                <li><a href="<?= base_url('login') ?>"><i class="fas fa-sign-in-alt"></i> Iniciar sesión</a></li>
                                <li><a href="<?= base_url('politica-privacidad') ?>"><i class="fas fa-shield-alt"></i> Privacidad</a></li>
                                <li><a href="<?= base_url('terminos-condiciones') ?>"><i class="fas fa-file-contract"></i> Términos</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Contacto -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-widget-modern">
                            <h4 class="footer-title-modern">
                                <i class="fas fa-phone"></i> Contacto
                            </h4>
                            <div class="contact-methods-modern">
                                <div class="contact-method-modern">
                                    <div class="contact-method-icon-modern">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="contact-method-content-modern">
                                        <h5>Ubicación</h5>
                                        <p><?= esc($empresaContacto['direccion']) ?></p>
                                    </div>
                                </div>
                                <div class="contact-method-modern">
                                    <div class="contact-method-icon-modern">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="contact-method-content-modern">
                                        <h5>Teléfono</h5>
                                        <a href="tel:<?= preg_replace('/[^0-9]/', '', $empresaContacto['telefono']) ?>"><?= esc($empresaContacto['telefono']) ?></a>
                                    </div>
                                </div>
                                <div class="contact-method-modern">
                                    <div class="contact-method-icon-modern">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="contact-method-content-modern">
                                        <h5>Email</h5>
                                        <a href="mailto:<?= esc($empresaContacto['email']) ?>"><?= esc($empresaContacto['email']) ?></a>
                                    </div>
                                </div>
                                <div class="contact-method-modern">
                                    <div class="contact-method-icon-modern">
                                        <i class="fab fa-whatsapp"></i>
                                    </div>
                                    <div class="contact-method-content-modern">
                                        <h5>WhatsApp</h5>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $empresaContacto['telefono']) ?>" target="_blank">Chatear ahora</a>
                                    </div>
                                </div>
                            </div>
                            <div class="contact-actions-modern">
                                <a href="<?= base_url('contacto') ?>" class="btn-contact-footer-modern">
                                    <i class="fas fa-envelope"></i> Enviar Mensaje
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Subfooter -->
            <div class="subfooter-modern">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6">
                            <p class="copyright-modern">
                                &copy; <?= date('Y') ?> <strong>VitaSync</strong> | 
                                <a href="<?= base_url('politica-privacidad') ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">Privacidad</a> | 
                                <a href="<?= base_url('terminos-condiciones') ?>" style="color: rgba(255,255,255,0.7); text-decoration: none;">Términos</a>
                            </p>
                        </div>
                        <div class="col-lg-6 col-md-6 text-md-end">
                        <div class="social-icons-modern">
                            <a href="https://web.facebook.com/man.msanchez" target="_blank" class="social-icon-modern" title="Facebook">
                                <i class="fab fa-facebook-f" style="color:white;"></i>
                            </a>
                            <a href="https://www.instagram.com/mansanchez45/" target="_blank" class="social-icon-modern" title="Instagram">
                                <i class="fab fa-instagram" style="color:white;"></i>
                            </a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Preloader -->
        <div id="preloader">
            <div class="spinner">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
    </div>

    <!-- Javascript Files -->
    <script src="<?= base_url('lib/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/wow.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.isotope.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/easing.js') ?>"></script>
    <script src="<?= base_url('lib/js/owl.carousel.js') ?>"></script>
    <script src="<?= base_url('lib/js/validation.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/enquire.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.stellar.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.plugin.js') ?>"></script>
    <script src="<?= base_url('lib/js/typed.js') ?>"></script>
    <script src="<?= base_url('lib/js/jarallax.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.countTo.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.countdown.js') ?>"></script>
    <script src="<?= base_url('lib/js/mdb.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/designesia.js') ?>"></script>
    
    <!-- JavaScript para Header Moderno -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const navModern = document.querySelector('.nav-modern');
        const header = document.querySelector('.header-modern');
        
        // Función para posicionar el menú correctamente
        function positionMenu() {
            if (navModern && header) {
                const headerHeight = header.offsetHeight;
                const topbarHeight = document.querySelector('.topbar-modern')?.offsetHeight || 0;
                navModern.style.top = (headerHeight) + 'px';
                console.log('Menu posicionado en:', headerHeight + 'px');
            }
        }
        
        if (mobileMenuBtn && navModern) {
            console.log('Menu mobile inicializado correctamente');
            
            // Posicionar menú al cargar
            positionMenu();
            
            // Toggle del menú móvil
            mobileMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Click en hamburguesa');
                navModern.classList.toggle('active');
                console.log('Clase active:', navModern.classList.contains('active'));
                
                // Posicionar menú debajo del header
                positionMenu();
                
                // Animate hamburger menu
                const spans = this.querySelectorAll('span');
                if (navModern.classList.contains('active')) {
                    spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });
            
            // Close mobile menu when clicking on a link
            const navLinks = navModern.querySelectorAll('.nav-link-modern');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navModern.classList.remove('active');
                    const spans = mobileMenuBtn.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                });
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!navModern.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                    navModern.classList.remove('active');
                    const spans = mobileMenuBtn.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            });
            
            // Reposicionar menú al hacer resize
            window.addEventListener('resize', function() {
                positionMenu();
            });
        }
        
        // Smooth scrolling for anchor links
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Header scroll effect - Solo cambia la sombra, sin cambios de padding
        if (header) {
            let ticking = false;
            
            function updateHeader() {
                console.log(window.scrollY);
                if (window.scrollY > 50) {
                    header.style.boxShadow = '0 4px 25px rgba(29, 40, 68, 0.4)';
                    header.style.paddingTop = '5px';
                    header.style.transition = '2s';
                } else {
                    header.style.boxShadow = '0 4px 20px rgba(29, 40, 68, 0.3)';
                    header.style.paddingTop = '5px';
                    header.style.transition = '2s';
                }
                ticking = false;
            }
            
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    requestAnimationFrame(updateHeader);
                    ticking = true;
                }
            });
        }
    });
    </script>
</body>
</html>
