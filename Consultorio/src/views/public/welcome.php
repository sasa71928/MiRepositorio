<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest</title>
    <link href="main.css" rel="stylesheet">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="main">

        <!-- Sección inicial -->
    <section id="hero" class="hero section">
      
        <div class="container position-relative">
        <div class="welcome position-relative" data-aos="fade-down" data-aos-delay="100">
            <h2>Bienvenido a Clinigest</h2>
            <p>Somos un equipo de profesionales dedicados a la gestión clínica</p>
        </div>

        <div class="content">
        <div class="why-box" data-aos="zoom-out" data-aos-delay="200">
            <h3>¿Por qué elegir CliniGest?</h3>
            <p>
                En CliniGest, combinamos tecnología, atención médica de calidad y un equipo humano comprometido con el bienestar del paciente. Nos especializamos en facilitar una gestión clínica eficiente, segura y personalizada. Nuestro objetivo es que cada paciente reciba la mejor atención posible, desde el primer contacto hasta su recuperación.
            </p>
        <div>
          <a href="#about" class="more-btn"><span>Learn More</span> <i class="bi bi-chevron-right"></i></a>
        </div>
      </div>

      <div class="d-flex">
        <div class="icon-box" data-aos="zoom-out" data-aos-delay="300">
          <i class="bi bi-clipboard-data"></i>
          <h4>Atención integral en un solo lugar</h4>
          <p>Desde medicina general hasta servicios de imagenología, en CliniGest centralizamos múltiples especialidades para brindarte comodidad, rapidez y confianza en tu atención médica.</p>
        </div>

        <div class="icon-box" data-aos="zoom-out" data-aos-delay="400">
          <i class="bi bi-gem"></i>
          <h4>Tecnología que cuida</h4>
          <p>Contamos con equipos modernos para radiografías, ultrasonidos, laboratorio clínico y más. Esto nos permite ofrecer diagnósticos más precisos y tratamientos más efectivos en menos tiempo.</p>
        </div>

        <div class="icon-box" data-aos="zoom-out" data-aos-delay="500">
          <i class="bi bi-inboxes"></i>
          <h4>Calidez y profesionalismo</h4>
          <p>Nuestro personal médico y administrativo está preparado para brindarte una experiencia amable, respetuosa y humana. Nos enfocamos en el trato cercano, la confianza y el acompañamiento constante.</p>
        </div>
      </div>
    </div>
  </div>
</section>

    <!-- Sección de servicios -->
    <section id="servicios" class="services section">
  
    <div class="container section-title" data-aos="fade-up">
        <h2>Servicios</h2>
        <p>Lista de servicios que ofrece CliniGest</p>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item">
                <div class="icon">
                <i class="fas fa-heartbeat"></i>
            </div>
                <h3>Inyecciones</h3>
                <p>Aplicamos medicamentos por vía intramuscular o subcutánea bajo supervisión profesional, asegurando una administración segura y eficaz para cada paciente.</p>
            </div>
        </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="service-item">
                <div class="icon">
                <i class="fas fa-pills"></i>
            </div>
                <h3>Canalizaciones</h3>
                <p>Realizamos canalizaciones intravenosas para la administración de sueros, medicamentos y toma de muestras, utilizando técnicas seguras y personal capacitado.</p>
            </div>
        </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="service-item">
                <div class="icon">
                <i class="fas fa-hospital-user"></i>
            </div>
                <h3>Ultrasonidos</h3>
                <p>Estudios de ultrasonido (ecografías) para diagnóstico no invasivo de tejidos blandos, órganos internos y control de embarazo. Resultados rápidos y precisos.</p>
            </div>
        </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="service-item">
                <div class="icon">
                <i class="fas fa-dna"></i>
            </div>
                <h3>Muestras de laboratorio</h3>
                <p>Procesamos muestras de sangre, orina y otros fluidos para exámenes como biometría hemática, glucosa, perfil lipídico, y pruebas de embarazo. Resultados confiables y rápidos.</p>
            </div>
        </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                <div class="service-item">
                <div class="icon">
                <i class="fas fa-wheelchair"></i>
            </div>
                <h3>Curaciones básicas</h3>
                <p>Tratamos heridas leves, quemaduras, úlceras y otras afecciones superficiales con técnicas asépticas y material estéril, promoviendo una recuperación segura.</p>
            </div>
        </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                <div class="service-item">
                <div class="icon">
                <i class="fas fa-notes-medical"></i>
            </div>
                <h3>Radiografías</h3>
                <p>Servicio de rayos X para detectar fracturas, anomalías pulmonares, problemas articulares y más. Imágenes digitales con alta resolución y bajo nivel de radiación.</p>
            </div>
        </div>
    </div>
  </div>
</section>

<!-- Sección departamentos -->
        <section id="departamentos" class="departments section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Departamentos</h2>
                <p>Nuestros departamentos especializados para su atención integral</p>
            </div>

            <div class="container">
                <div class="departments-container">
                    <!-- Lista de departamentos -->
                    <div class="departments-list" data-aos="fade-right" data-aos-delay="100">
                        <ul>
                            <li class="active"><a href="#pediatria" data-department="pediatria" data-image="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Pediatria.jpg-avJsM4L5GMdbwrJUspD69cLgziWFKs.jpeg">Pediatría</a></li>
                            <li><a href="#enfermeria" data-department="enfermeria" data-image="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Enfermeria.jpg-GK4Bg2pOVGd3dVCRGiIu4acVrAJXIA.jpeg">Enfermería</a></li>
                            <li><a href="#imagenologia" data-department="imagenologia" data-image="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Imagenologia.jpg-XaNEdpFIs43j6N68Kyx7aFx7QkhyKe.jpeg">Imagenología</a></li>
                            <li><a href="#laboratorio" data-department="laboratorio" data-image="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/LaboratorioClinico.jpg-4ZJwUJK07endL3mCvl0ojarvxJqvx3.jpeg">Laboratorio Clínico</a></li>
                            <li><a href="#medicina-general" data-department="medicina-general" data-image="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/MedicinaGeneral.jpg-zCqmWpjHjuj8QtIYRl90zb1tds7V1a.jpeg">Medicina General</a></li>
                        </ul>
                    </div>

                    <!-- Contenido de departamentos -->
                    <div class="departments-content" data-aos="fade-up" data-aos-delay="200">
                        <div id="pediatria" class="department-item active">
                            <h3>Departamento de Pediatría</h3>
                            <p class="fst-italic">Cuidado especializado para niños y adolescentes</p>
                            <p>Nuestro departamento de pediatría ofrece atención médica integral para bebés, niños y adolescentes. Contamos con especialistas altamente capacitados que brindan un cuidado compasivo y personalizado, desde chequeos de rutina hasta tratamientos para condiciones complejas. Nuestro enfoque centrado en la familia garantiza que tanto los pacientes como sus padres se sientan cómodos y bien informados durante todo el proceso de atención.</p>
                        </div>

                        <div id="enfermeria" class="department-item">
                            <h3>Departamento de Enfermería</h3>
                            <p class="fst-italic">Cuidado profesional y compasivo para todos los pacientes</p>
                            <p>Nuestro equipo de enfermería está compuesto por profesionales dedicados que proporcionan cuidados de alta calidad a todos nuestros pacientes. Trabajan en estrecha colaboración con médicos y otros profesionales de la salud para garantizar una atención integral. Desde la administración de medicamentos hasta el apoyo emocional, nuestras enfermeras están comprometidas con el bienestar y la recuperación de cada paciente.</p>
                        </div>

                        <div id="imagenologia" class="department-item">
                            <h3>Departamento de Imagenología</h3>
                            <p class="fst-italic">Diagnóstico preciso mediante tecnología avanzada de imágenes</p>
                            <p>El departamento de imagenología ofrece servicios diagnósticos de vanguardia utilizando equipos de última generación. Realizamos radiografías, ultrasonidos, tomografías computarizadas y resonancias magnéticas para proporcionar a nuestros médicos imágenes detalladas que ayudan en el diagnóstico y tratamiento de diversas condiciones. Nuestros técnicos y radiólogos están altamente capacitados para garantizar resultados precisos y una experiencia cómoda para el paciente.</p>
                        </div>

                        <div id="laboratorio" class="department-item">
                            <h3>Laboratorio Clínico</h3>
                            <p class="fst-italic">Análisis precisos para diagnósticos confiables</p>
                            <p>Nuestro laboratorio clínico realiza una amplia gama de pruebas diagnósticas con la más alta precisión y rapidez. Desde análisis de sangre rutinarios hasta pruebas especializadas, nuestro equipo de tecnólogos médicos utiliza equipos avanzados para proporcionar resultados confiables que ayudan a los médicos a diagnosticar enfermedades, monitorear tratamientos y prevenir problemas de salud.</p>
                        </div>

                        <div id="medicina-general" class="department-item">
                            <h3>Medicina General</h3>
                            <p class="fst-italic">Atención primaria integral para pacientes de todas las edades</p>
                            <p>El departamento de medicina general es la puerta de entrada a nuestros servicios de salud. Nuestros médicos generales ofrecen atención primaria completa, desde chequeos preventivos hasta el manejo de enfermedades crónicas. Con un enfoque holístico, nuestros profesionales no solo tratan enfermedades, sino que también promueven hábitos saludables y prevención, estableciendo relaciones duraderas con los pacientes para garantizar su bienestar a largo plazo.</p>
                        </div>
                    </div>

                    <!-- Imágenes de departamentos -->
                    <div class="departments-image" data-aos="fade-left" data-aos-delay="300">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Pediatria.jpg-avJsM4L5GMdbwrJUspD69cLgziWFKs.jpeg" alt="Departamento de Pediatría" class="img-fluid">
                    </div>
                </div>
            </div>
        </section>

<!-- JavaScript para la sección de departamentos -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const departmentLinks = document.querySelectorAll('.departments-list a');
        const departmentItems = document.querySelectorAll('.department-item');
        const departmentImage = document.querySelector('.departments-image img');
        
        departmentLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remover clases activas
                departmentLinks.forEach(link => link.parentElement.classList.remove('active'));
                departmentItems.forEach(item => item.classList.remove('active'));
                
                // Añadir clase activa al enlace actual
                this.parentElement.classList.add('active');
                
                // Mostrar el contenido del departamento seleccionado
                const departmentId = this.getAttribute('data-department');
                document.getElementById(departmentId).classList.add('active');
                
                // Actualizar la imagen
                const imageUrl = this.getAttribute('data-image');
                departmentImage.src = imageUrl;
                departmentImage.alt = 'Departamento de ' + this.textContent;
                
                // Añadir efecto de transición a la imagen
                departmentImage.classList.add('image-transition');
                setTimeout(() => {
                    departmentImage.classList.remove('image-transition');
                }, 500);
            });
        });
    });
    </script>

</main>


    <?php include 'footer.php'; ?>

</body>
</html>
  

  