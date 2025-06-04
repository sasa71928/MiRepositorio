<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - CliniGest</title>
</head>
<body>
    <div class="login-container">
        <h1>CliniGest</h1>
        <p>Recuperación de contraseña</p>
        
        <!-- Paso 1: Solicitar correo -->
        <div id="paso-1" class="recovery-step active">
            <p class="recovery-info">Ingrese su correo electrónico para recibir un código de verificación</p>
            <form id="form-paso-1">
                <input type="email" name="correo" placeholder="Correo electrónico" required>
                <button type="submit" class="btn btn-login">Enviar código</button>
            </form>
            <div class="forgot-password">
                <a href="<?= BASE_URL ?>/" class="back-link">Volver al inicio de sesión</a>
            </div>
        </div>
        
        <!-- Paso 2: Verificar código -->
        <div id="paso-2" class="recovery-step">
            <p class="recovery-info">Ingrese el código de verificación enviado a su correo</p>
            <form id="form-paso-2">
                <div class="verification-code">
                    <input type="text" maxlength="1" class="code-input" required>
                    <input type="text" maxlength="1" class="code-input" required>
                    <input type="text" maxlength="1" class="code-input" required>
                    <input type="text" maxlength="1" class="code-input" required>
                    <input type="text" maxlength="1" class="code-input" required>
                    <input type="text" maxlength="1" class="code-input" required>
                </div>
                <button type="submit" class="btn btn-login">Verificar código</button>
            </form>
            <div class="forgot-password">
                <a href="#" id="reenviar-codigo" class="resend-link">Reenviar código</a>
                <a href="#" id="volver-paso-1" class="back-link">Cambiar correo</a>
            </div>
        </div>
        
        <!-- Paso 3: Nueva contraseña -->
        <div id="paso-3" class="recovery-step">
            <p class="recovery-info">Establezca su nueva contraseña</p>
            <form id="form-paso-3" action="procesar_recuperacion.php" method="POST">
                <input type="password" name="nueva_contrasena" id="nueva_contrasena" placeholder="Nueva contraseña" required>
                <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" placeholder="Confirmar contraseña" required>
                <div class="password-requirements">
                    <p>La contraseña debe contener:</p>
                    <ul>
                        <li id="req-length">Al menos 8 caracteres</li>
                        <li id="req-uppercase">Al menos una mayúscula</li>
                        <li id="req-lowercase">Al menos una minúscula</li>
                        <li id="req-number">Al menos un número</li>
                    </ul>
                </div>
                <button type="submit" class="btn btn-login">Cambiar contraseña</button>
            </form>
            <div class="forgot-password">
                <a href="<?= BASE_URL ?>/"  class="back-link">Volver al inicio de sesión</a>
            </div>
        </div>
        
        <!-- Paso 4: Confirmación -->
        <div id="paso-4" class="recovery-step">
            <div class="success-icon">
                <i class="checkmark">✓</i>
            </div>
            <h2 class="success-title">¡Contraseña actualizada!</h2>
            <p class="recovery-info">Su contraseña ha sido actualizada correctamente.</p>
            <a href="<?= BASE_URL ?>/login" class="btn btn-login">Iniciar sesión</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Referencias a los pasos
            const paso1 = document.getElementById('paso-1');
            const paso2 = document.getElementById('paso-2');
            const paso3 = document.getElementById('paso-3');
            const paso4 = document.getElementById('paso-4');
            
            // Formularios
            const formPaso1 = document.getElementById('form-paso-1');
            const formPaso2 = document.getElementById('form-paso-2');
            const formPaso3 = document.getElementById('form-paso-3');
            
            // Botones y enlaces
            const reenviarCodigo = document.getElementById('reenviar-codigo');
            const volverPaso1 = document.getElementById('volver-paso-1');
            
            // Inputs de código de verificación
            const codeInputs = document.querySelectorAll('.code-input');
            
            // Inputs de contraseña
            const nuevaContrasena = document.getElementById('nueva_contrasena');
            const confirmarContrasena = document.getElementById('confirmar_contrasena');
            
            // Requisitos de contraseña
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqNumber = document.getElementById('req-number');
            
            // Paso 1: Enviar correo
        formPaso1.addEventListener('submit', async function(e) {
            e.preventDefault();
            const correo = formPaso1.correo.value;

            const response = await fetch("<?= BASE_URL ?>/recuperarContrasena/enviarCodigo", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ correo })
            });

            const result = await response.json();
            console.log("Respuesta del servidor:", result);

            if (result.status === 'ok') {
                paso1.classList.remove('active');
                paso2.classList.add('active');
            } else {
                alert("❌ " + (result.message || "Correo no encontrado o inválido"));
            }
        });
        //Reenviar Codigo
        reenviarCodigo.addEventListener('click', async function(e) {
            e.preventDefault();

            const correo = formPaso1.correo.value;
            if (!correo) {
                alert('Primero ingrese un correo válido en el Paso 1.');
                return;
            }

            const response = await fetch("<?= BASE_URL ?>/recuperarContrasena/enviarCodigo", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ correo })
            });

            const result = await response.json();
            console.log("Código reenviado:", result);

            if (result.status === 'ok') {
                alert("📩 Código reenviado al correo: " + correo);
            } else {
                alert("❌ Error al reenviar código.");
            }
        });

            
            // Paso 2: Verificar código
            formPaso2.addEventListener('submit', async function(e) {
                e.preventDefault();
                const codeInputs = document.querySelectorAll('.code-input');
                const codigo = Array.from(codeInputs).map(input => input.value).join('');

                const res = await fetch("<?= BASE_URL ?>/recuperarContrasena/verificarCodigo", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ codigo })
                });

                const result = await res.json();
                console.log("Verificación:", result);

                if (result.status === 'ok') {
                    paso2.classList.remove('active');
                    paso3.classList.add('active');
                } else {
                    alert(result.message);
                }
            });

            
            // Paso 3: Cambiar contraseña
            formPaso3.addEventListener('submit', async function(e) {
                e.preventDefault();

                const nueva = nuevaContrasena.value;
                const confirmar = confirmarContrasena.value;

                if (nueva !== confirmar) {
                    alert('Las contraseñas no coinciden');
                    return;
                }

                const res = await fetch("<?= BASE_URL ?>/recuperarContrasena/resetPassword", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        nueva_contrasena: nueva,
                        confirmar_contrasena: confirmar
                    })
                });

                const result = await res.json();
                console.log("Reset:", result);

                if (result.status === 'ok') {
                    paso3.classList.remove('active');
                    paso4.classList.add('active');
                } else {
                    alert(result.message);
                }
            });

            
            // Reenviar código
            reenviarCodigo.addEventListener('click', function(e) {
                e.preventDefault();
                // Aquí iría la lógica para reenviar el código
                alert('Se ha enviado un nuevo código a su correo');
            });
            
            // Volver al paso 1
            volverPaso1.addEventListener('click', function(e) {
                e.preventDefault();
                paso2.classList.remove('active');
                paso1.classList.add('active');
            });
            
            // Manejar inputs de código de verificación
            codeInputs.forEach((input, index) => {
                input.addEventListener('keyup', function(e) {
                    // Si se ingresó un dígito, mover al siguiente input
                    if (this.value.length === 1) {
                        if (index < codeInputs.length - 1) {
                            codeInputs[index + 1].focus();
                        }
                    }
                });
                
                input.addEventListener('keydown', function(e) {
                    // Si se presiona backspace y está vacío, mover al input anterior
                    if (e.key === 'Backspace' && this.value.length === 0) {
                        if (index > 0) {
                            codeInputs[index - 1].focus();
                        }
                    }
                });
            });
            
            // Validar requisitos de contraseña en tiempo real
            nuevaContrasena.addEventListener('input', function() {
                const value = this.value;
                
                // Longitud mínima
                if (value.length >= 8) {
                    reqLength.classList.add('valid');
                } else {
                    reqLength.classList.remove('valid');
                }
                
                // Al menos una mayúscula
                if (/[A-Z]/.test(value)) {
                    reqUppercase.classList.add('valid');
                } else {
                    reqUppercase.classList.remove('valid');
                }
                
                // Al menos una minúscula
                if (/[a-z]/.test(value)) {
                    reqLowercase.classList.add('valid');
                } else {
                    reqLowercase.classList.remove('valid');
                }
                
                // Al menos un número
                if (/[0-9]/.test(value)) {
                    reqNumber.classList.add('valid');
                } else {
                    reqNumber.classList.remove('valid');
                }
            });
        });
    </script>
</body>
</html>

<style>
        /* Estilos generales */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #bdd7e5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        padding: 20px;
    }

    .login-container {
        background-color: white;
        padding: 60px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 500px;
        min-height: 550px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .login-container h1 {
        font-size: 48px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .login-container p {
        margin-bottom: 30px;
        color: #333;
    }

    .login-container input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-login {
        background-color: #3768A7;
        color: white;
    }

    .btn-login:hover {
        background-color: #264d80;
    }

    .btn-create {
        background-color: #1f3970;
        color: white;
        margin-top: 5px;
    }

    .btn-create:hover {
        background-color: #162b56;
    }

    /* Formularios */
    form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
    }

    input {
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s;
    }

    input:focus {
    outline: none;
    border-color: #1977cc;
    }

    .btn {
    padding: 12px 15px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    }

    .btn-login {
    background-color: #1977cc;
    color: white;
    }

    .btn-login:hover {
    background-color: #166ab5;
    }

    .btn-create {
    background-color: transparent;
    color: #1977cc;
    border: 1px solid #1977cc;
    text-decoration: none;
    display: block;
    }

    .btn-create:hover {
    background-color: rgba(25, 119, 204, 0.1);
    }

    /* Enlace de contraseña olvidada */
    .forgot-password {
    margin-bottom: 20px;
    text-align: center;
    }

    .forgot-password a {
    color: #1977cc;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
    }

    .forgot-password a:hover {
    text-decoration: underline;
    }

    /* Estilos para recuperación de contraseña */
    .recovery-step {
    display: none;
    }

    .recovery-step.active {
    display: block;
    }

    .recovery-info {
    color: #6c757d;
    margin-bottom: 20px;
    font-size: 14px;
    }

    /* Código de verificación */
    .verification-code {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    }

    .code-input {
    width: 40px;
    height: 50px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    }

    /* Requisitos de contraseña */
    .password-requirements {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
    text-align: left;
    }

    .password-requirements p {
    color: #2c4964;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 14px;
    }

    .password-requirements ul {
    list-style-type: none;
    padding-left: 5px;
    }

    .password-requirements li {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 5px;
    position: relative;
    padding-left: 20px;
    }

    .password-requirements li::before {
    content: "○";
    position: absolute;
    left: 0;
    color: #6c757d;
    }

    .password-requirements li.valid {
    color: #19c079;
    }

    .password-requirements li.valid::before {
    content: "✓";
    color: #19c079;
    }

    /* Confirmación exitosa */
    .success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .checkmark {
    color: #19c079;
    font-size: 40px;
    line-height: 1;
    }

    .success-title {
    color: #2c4964;
    margin-bottom: 15px;
    font-size: 22px;
    }

    /* Responsivo */
    @media (max-width: 480px) {
    .login-container {
        padding: 30px 20px;
    }

    h1 {
        font-size: 24px;
    }

    p {
        font-size: 14px;
    }

    input,
    .btn {
        padding: 10px 12px;
        font-size: 14px;
    }

    .code-input {
        width: 35px;
        height: 45px;
        font-size: 18px;
    }

    .forgot-password {
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }
    }

</style>