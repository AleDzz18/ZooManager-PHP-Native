<?php
// views/errors/403.php
// RUTA DE PRODUCCIÓN EN LA NUBE (RAÍZ)
$base_url = "/";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡ACCESO DENEGADO!</title>
    <style>
        body {
            background-color: #000;
            color: #0f0;
            font-family: 'Courier New', Courier, monospace;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            cursor: pointer; /* Mano en toda la pantalla para incitar clic */
            user-select: none;
        }
        
        .troll-container {
            border: 2px solid #0f0;
            padding: 2rem;
            background: rgba(0, 20, 0, 0.95);
            box-shadow: 0 0 20px #0f0;
            max-width: 500px;
            width: 90%;
            border-radius: 10px;
            position: relative;
            z-index: 2;
        }

        h1 {
            font-size: 2rem;
            margin-top: 0;
            margin-bottom: 1rem;
            text-shadow: 2px 2px #fff;
            animation: parpadeo 0.5s infinite alternate;
        }

        img {
            width: 100%;
            max-width: 250px;
            height: auto;
            border: 3px solid #fff;
            margin: 10px 0;
        }

        #msg {
            font-size: 1.2rem;
            margin-top: 1rem;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #0f0;
            color: #000;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            border: 2px solid #0f0;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #000;
            color: #0f0;
            box-shadow: 0 0 15px #0f0;
        }

        /* Estilo TRAMPA (Rojo) */
        .btn-trap {
            background: #f00 !important;
            color: #fff !important;
            border-color: #f00 !important;
            box-shadow: 0 0 20px #f00 !important;
            animation: temblor 0.2s infinite;
        }

        @keyframes parpadeo {
            from { opacity: 1; }
            to { opacity: 0.3; }
        }

        @keyframes temblor {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(2deg); }
            75% { transform: rotate(-2deg); }
            100% { transform: rotate(0deg); }
        }
    </style>
</head>
<body onclick="activarAudioGlobal()">

    <div class="troll-container">
        <h1>🚫 ACCESO DENEGADO 🚫</h1>
        
        <img src="<?php echo $base_url; ?>assets/img/troll.gif" alt="Nedry Troll">
        
        <h2 id="msg">¡Ah ah ah! ¡No dijiste la palabra mágica!</h2>
        <p style="font-size: 0.9rem; opacity: 0.8;">Sistema de Seguridad ZooManager v1.0 activado.</p>

        <a href="<?php echo $base_url; ?>index.php" class="btn-back" id="escapeBtn">
            🏃‍♂️ ESCAPAR AHORA
        </a>
    </div>

    <audio id="trollSound" loop>
        <source src="<?php echo $base_url; ?>assets/sounds/magic_word.mp3" type="audio/mpeg">
    </audio>

    <script>
        var audio = document.getElementById("trollSound");
        var btn = document.getElementById("escapeBtn");
        var msg = document.getElementById("msg");
        var trampaActivada = false;

        // FUNCIÓN MAESTRA PARA ACTIVAR AUDIO
        function activarAudioGlobal() {
            audio.play().catch(e => console.log("Esperando interacción..."));
        }

        // TRAMPA 1: EL BOTÓN DE ESCAPE (DOBLE CLICK)
        btn.addEventListener("click", function(event) {
            
            // Si el usuario intenta salir por primera vez...
            if (!trampaActivada) {
                event.preventDefault();     // Bloquear salida
                event.stopPropagation();    // Evitar conflictos con el body
                
                activarAudioGlobal();       // SONIDO ON

                // Efectos visuales de pánico
                btn.classList.add("btn-trap");
                btn.innerHTML = "⚠️ ¡INTÉNTALO OTRA VEZ! ⚠️";
                msg.innerHTML = "¡ACCESO DENEGADO! ¡ACCESO DENEGADO!";
                msg.style.color = "red";
                
                trampaActivada = true;      // Ahora sí le permitiremos salir
            }
            // La segunda vez, el evento fluye normal y sale de la página.
        });

        // TRAMPA 2: TECLADO (Cualquier tecla activa el audio)
        document.addEventListener('keydown', activarAudioGlobal);

        // INTENTO AUTOMÁTICO AL CARGAR (Por si acaso)
        window.onload = activarAudioGlobal;

    </script>

</body>
</html>