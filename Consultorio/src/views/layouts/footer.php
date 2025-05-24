<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CliniGest</title>
  <link href="footer.css" rel="stylesheet">
</head>

<body>
    <footer class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h3><a href="index.php">CliniGest</a></h3>
                    <p>Sistema integral de gestión para clínicas y centros médicos. Ofrecemos soluciones digitales para mejorar la experiencia de pacientes y profesionales de la salud.</p>
                    <div class="developers">
                        <h4>Desarrollado por:</h4>
                        <ul>
                            <li>Oscar Eduardo Salazar Salas</li>
                            <li>Christian Fiol Higuera</li>
                            <li>Ildefonso Ruiz Beltran</li>
                        </ul>
                    </div>
                </div>
              
                <div class="footer-links">
                    <h4>Nuestros Servicios</h4>
                    <ul>
                        <li>Consultas Médicas</li>
                        <li>Laboratorio Clínico</li>
                        <li>Imagenología</li>
                        <li>Especialidades</li>
                        <li>Urgencias</li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h4>Departamentos</h4>
                    <ul>
                        <li>Cardiología</li>
                        <li>Neurología</li>
                        <li>Pediatría</li>
                        <li>Cirugía</li>
                        <li>Dermatología</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Botón de volver arriba -->
    <a href="#" class="back-to-top" id="back-to-top"><i class="fas fa-arrow-up"></i></a>
</footer>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
      
        const header = document.querySelector('.header');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const navMenu = document.querySelector('.nav-menu');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
                this.innerHTML = navMenu.classList.contains('active') 
                    ? '<i class="fas fa-times"></i>' 
                    : '<i class="fas fa-bars"></i>';
            });
        }
      
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth < 992) {
                    e.preventDefault();
                    this.classList.toggle('active');
                }
            });
        });
        
        // Boton de volver al inicio
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('active');
            } else {
                backToTopButton.classList.remove('active');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
</body>
</html>

<style>
            /* Estilos para el footer */
        .footer {
            background-color: #f8f9fa;
            color: #444444;
            font-size: 14px;
            position: relative;
        }

        .footer-top {
            padding: 60px 0 30px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
        }

        .footer-info {
            flex: 1;
            min-width: 250px;
            max-width: 350px;
        }

        .footer-info h3 {
            font-size: 24px;
            margin: 0 0 15px 0;
            padding: 0;
            line-height: 1;
            font-weight: 700;
        }

        .footer-info h3 a {
            color: #2c4964;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-info h3 a:hover {
            color: #1977cc;
        }

        .footer-info p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #6c757d;
        }

        .developers {
            margin-top: 20px;
        }

        .developers h4 {
            font-size: 16px;
            font-weight: 600;
            color: #2c4964;
            margin-bottom: 10px;
        }

        .developers ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .developers li {
            color: #6c757d;
            padding: 3px 0;
            font-size: 13px;
        }

        .social-links {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background-color: #1977cc;
            color: #fff;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background-color: #166ab5;
            transform: translateY(-3px);
        }

        .footer-links {
            flex: 1;
            min-width: 170px;
        }

        .footer-links h4 {
            font-size: 16px;
            font-weight: 600;
            color: #2c4964;
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .footer-links h4::after {
            content: '';
            position: absolute;
            display: block;
            width: 50px;
            height: 2px;
            background: #1977cc;
            bottom: 0;
            left: 0;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links ul li {
            padding: 7px 0;
        }

        .footer-links ul li:first-child {
            padding-top: 0;
        }

        .footer-links ul a {
            color: #6c757d;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            position: relative;
        }

        .footer-links ul a:hover {
            color: #1977cc;
            padding-left: 5px;
        }

        .footer-links ul a::before {
            content: "\f105";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: -15px;
            top: 50%;
            transform: translateY(-50%);
            color: #1977cc;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .footer-links ul a:hover::before {
            opacity: 1;
            left: -10px;
        }

        .footer-contact {
            flex: 1;
            min-width: 250px;
        }

        .footer-contact h4 {
            font-size: 16px;
            font-weight: 600;
            color: #2c4964;
            position: relative;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .footer-contact h4::after {
            content: '';
            position: absolute;
            display: block;
            width: 50px;
            height: 2px;
            background: #1977cc;
            bottom: 0;
            left: 0;
        }

        .footer-contact p {
            line-height: 26px;
            color: #6c757d;
        }

        .footer-contact p i {
            color: #1977cc;
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .footer-bottom {
            padding: 20px 0;
            text-align: center;
            background-color: #f1f3f5;
        }

        .copyright {
            color: #6c757d;
            text-align: center;
        }

        .back-to-top {
            position: fixed;
            right: 15px;
            bottom: 15px;
            width: 40px;
            height: 40px;
            background: #1977cc;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 996;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .back-to-top.active {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: #166ab5;
            color: #fff;
            transform: translateY(-3px);
        }

        /*Responsivo*/
        @media (max-width: 991px) {
            .footer-content {
                gap: 20px;
            }
            
            .footer-info,
            .footer-links,
            .footer-contact {
                min-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .footer-top {
                padding: 40px 0 20px;
            }
            
            .footer-content {
                flex-direction: column;
                gap: 30px;
            }
            
            .footer-info,
            .footer-links,
            .footer-contact {
                max-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .footer-top {
                padding: 30px 0 10px;
            }
            
            .footer-info h3 {
                font-size: 20px;
            }
            
            .back-to-top {
                right: 10px;
                bottom: 10px;
                width: 35px;
                height: 35px;
            }
        }
</style>