<?php
// src/views/layouts/header.php

// 1) Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Incluir helpers
require_once __DIR__ . '/../../helpers/auth.php';

// 3) (Opcional) Definir BASE_URL si aún no existe
//   Ya lo cubrimos dentro de auth.php, así que no es estrictamente necesario repetirlo aquí.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>CliniGest – Sistema de Gestión Clínica</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <h1><a href="<?= BASE_URL ?>/">CliniGest</a></h1>
            </div>
            
            <div class="mobile-menu-toggle" id="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            
            <nav class="nav-menu">
                <ul>
                    <li class="active"><a href="<?= BASE_URL ?>" class="nav-link">Inicio</a></li>
                    <li><a href="<?= BASE_URL ?>/#servicios" class="nav-link">Servicios</a></li>
                    <li><a href="<?= BASE_URL ?>/#departamentos" class="nav-link">Departamentos</a></li>
                </ul>
            </nav>
            
            <div class="header-actions">
                <?php if (!is_logged_in()): ?>
                    <!-- Invitado -->
                    <div class="appointment-btn">
                        <a href="<?= BASE_URL ?>/login" class="btn btn-primary">Iniciar Sesión</a>
                    </div>
                <?php elseif (is_admin()): ?>
                    <!-- Admin -->
                    <a href="<?= BASE_URL ?>/reports" class="btn btn-primary">Generar Reportes</a>
                    <div class="user-menu">
                        <div class="user-toggle">
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="user-dropdown">
                            <ul>
                                <li><a href="<?= BASE_URL ?>/profile"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                <li><a href="<?= BASE_URL ?>/doctors"><i class="fas fa-user-md"></i> Administrar Médicos</a></li>
                                <li class="divider"></li>
                                <li><a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                <?php elseif (is_doctor()): ?>
                    <!-- Doctor -->
                    <a href="<?= BASE_URL ?>/appointments/mine" class="btn btn-primary">Mis Citas</a>
                    <div class="user-menu">
                        <div class="user-toggle">
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="user-dropdown">
                            <ul>
                                <li><a href="<?= BASE_URL ?>/profile"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                <li><a href="<?= BASE_URL ?>/appointments/mine"><i class="fas fa-calendar-check"></i> Mis Citas</a></li>
                                <li class="divider"></li>
                                <li><a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Usuario normal -->
                    <div class="appointment-btn">
                        <a href="<?= BASE_URL ?>/appointments/create">Agendar Cita</a>
                    </div>
                    <div class="user-menu">
                        <div class="user-toggle">
                            <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="user-dropdown">
                            <ul>
                                <li><a href="<?= BASE_URL ?>/profile"><i class="fas fa-user"></i> Mi Perfil</a></li>
                                <li><a href="<?= BASE_URL ?>/appointments/mine"><i class="fas fa-calendar-check"></i> Mis Citas</a></li>
                                <li><a href="<?= BASE_URL ?>/ratings"><i class="fas fa-star"></i> Valoraciones</a></li>
                                <li class="divider"></li>
                                <li><a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>



<style>

                /* Estilos para el header */
        :root {
            --primary-color: #1977cc;
            --primary-color-light: #3291e6;
            --primary-color-dark: #166ab5;
            --secondary-color: #2c4964;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --border-color: #e9ecef;
            --white-color: #ffffff;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--secondary-color);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .header {
            background: var(--white-color);
            transition: var(--transition);
            z-index: 997;
            padding: 15px 0;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            width: 100%;
        }

        .header.scrolled {
            padding: 10px 0;
            box-shadow: var(--shadow-md);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 28px;
            margin: 0;
            padding: 0;
            line-height: 1;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .logo h1 a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .logo h1 a:hover {
            color: var(--primary-color);
        }

        .nav-menu {
            margin: 0;
            padding: 0;
        }

        .nav-menu ul {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
            align-items: center;
        }

        .nav-menu li {
            position: relative;
            white-space: nowrap;
            padding: 8px 15px;
        }

        .nav-menu a {
            display: block;
            position: relative;
            color: var(--secondary-color);
            transition: var(--transition);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }

        .nav-menu a:hover, 
        .nav-menu .active > a, 
        .nav-menu li:hover > a {
            color: var(--primary-color);
        }

        .nav-menu .active > a {
            position: relative;
        }

        .nav-menu .active > a::after {
            content: "";
            position: absolute;
            display: block;
            width: 100%;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--primary-color);
            transform: scaleX(1);
            transition: transform 0.3s ease;
        }

        .nav-menu a:not(.active)::after {
            content: "";
            position: absolute;
            display: block;
            width: 100%;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .nav-menu a:hover::after {
            transform: scaleX(1);
        }

        .nav-menu .dropdown {
            position: relative;
        }

        .nav-menu .dropdown > a {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-menu .dropdown > a i {
            font-size: 10px;
            transition: var(--transition);
        }

        .nav-menu .dropdown:hover > a i {
            transform: rotate(180deg);
        }

        .nav-menu .dropdown-menu {
            display: block;
            position: absolute;
            left: 0;
            top: 100%;
            min-width: 200px;
            z-index: 99;
            padding: 10px 0;
            background: var(--white-color);
            box-shadow: var(--shadow-lg);
            border-radius: 4px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
        }

        .nav-menu .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .nav-menu .dropdown-menu li {
            min-width: 180px;
            padding: 0;
        }

        .nav-menu .dropdown-menu a {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            text-transform: none;
            color: var(--secondary-color);
        }

        .nav-menu .dropdown-menu a:hover, 
        .nav-menu .dropdown-menu .active > a, 
        .nav-menu .dropdown-menu li:hover > a {
            color: var(--primary-color);
            background: var(--light-color);
        }

        .nav-menu .dropdown-menu a::after {
            display: none;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .appointment-btn a {
            background: var(--primary-color);
            color: var(--white-color);
            border-radius: 50px;
            padding: 8px 25px;
            white-space: nowrap;
            transition: var(--transition);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            box-shadow: var(--shadow-sm);
        }

        .appointment-btn a:hover {
            background: var(--primary-color-dark);
            color: var(--white-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .user-menu {
            position: relative;
        }

        .user-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: var(--transition);
        }

        .user-toggle:hover {
            background-color: var(--light-color);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--secondary-color);
        }

        .user-toggle i {
            font-size: 10px;
            color: var(--gray-color);
            transition: var(--transition);
        }

        .user-menu:hover .user-toggle i {
            transform: rotate(180deg);
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 200px;
            background: var(--white-color);
            border-radius: 4px;
            box-shadow: var(--shadow-lg);
            padding: 10px 0;
            z-index: 99;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: var(--transition);
        }

        .user-menu:hover .user-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(5px);
        }

        .user-dropdown ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .user-dropdown li {
            padding: 0;
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--secondary-color);
            font-size: 14px;
            text-decoration: none;
            transition: var(--transition);
        }

        .user-dropdown a:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }

        .user-dropdown i {
            font-size: 16px;
            color: var(--primary-color);
        }

        .divider {
            height: 1px;
            background-color: var(--border-color);
            margin: 5px 0;
        }

        .mobile-menu-toggle {
            display: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--secondary-color);
            transition: var(--transition);
        }

        .mobile-menu-toggle:hover {
            color: var(--primary-color);
        }

        /* Responsivo */
        @media (max-width: 1199px) {
            .nav-menu li {
                padding: 8px 10px;
            }
            
            .appointment-btn a {
                padding: 8px 20px;
            }
        }

        @media (max-width: 991px) {
            .header-container {
                position: relative;
            }
            
            .mobile-menu-toggle {
                display: block;
                order: 3;
            }
            
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--white-color);
                box-shadow: var(--shadow-md);
                padding: 10px 0;
                z-index: 9999;
                margin-top: 15px;
                border-radius: 4px;
            }
            
            .nav-menu.active {
                display: block;
            }
            
            .nav-menu ul {
                flex-direction: column;
                padding: 10px;
            }
            
            .nav-menu li {
                padding: 10px 0;
                width: 100%;
                text-align: left;
            }
            
            .nav-menu .dropdown-menu {
                position: static;
                display: none;
                min-width: 100%;
                padding: 10px 15px;
                box-shadow: none;
                background: var(--light-color);
                opacity: 1;
                visibility: visible;
                transform: none;
            }
            
            .nav-menu .dropdown.active .dropdown-menu {
                display: block;
            }
            
            .nav-menu .dropdown > a i {
                position: absolute;
                right: 10px;
                top: 50%;
                transform: translateY(-50%);
            }
            
            .header-actions {
                order: 2;
            }
            
            .user-name {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 0 15px;
            }
            
            .logo h1 {
                font-size: 24px;
            }
            
            .appointment-btn {
                display: none;
            }
            
            .user-menu {
                margin-right: 15px;
            }
        }

        @media (max-width: 576px) {
            .header {
                padding: 10px 0;
            }
            
            .logo h1 {
                font-size: 22px;
            }
        }
</style>