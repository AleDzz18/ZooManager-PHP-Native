<?php
// views/errors/403.php
// Ajusta esta ruta si tu carpeta se llama distinto
$base_url = "/zoo-system/";
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
        }
        
        .troll-container {
            border: 2px solid #0f0;
            padding: 2rem;
            background: rgba(0, 20, 0, 0.95);
            box-shadow: 0 0 20px #0f0;
            
            /* --- AJUSTES DE TAMAÑO --- */
            max-width: 500px; /* Contenedor más estrecho */
            width: 90%;       /* Para que no se salga en móviles */
            border-radius: 10px;
        }

        h1 {
            font-size: 2.5rem; /* Texto un poco más pequeño */
            margin-top: 0;
            margin-bottom: 1rem;
            text-shadow: 2px 2px #fff;
            animation: parpadeo 0.5s infinite alternate;
        }

        img {
            width: 100%;
            /* --- IMAGEN MÁS PEQUEÑA --- */
            max-width: 250px; /* Tamaño ideal para el meme */
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
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #000;
            color: #0f0;
            box-shadow: 0 0 10px #0f0;
        }

        @keyframes parpadeo {
            from { opacity: 1; }
            to { opacity: 0.3; }
        }
    </style>
</head>
<body onclick="playAudio()">

    <div class="troll-container">
        <h1>🚫 ACCESO DENEGADO 🚫</h1>
        
        <img src="<?php echo $base_url; ?>assets/img/troll.gif" alt="Nedry Troll">
        
        <h2 id="msg">¡Ah ah ah! ¡No dijiste la palabra mágica!</h2>
        <p style="font-size: 0.9rem; opacity: 0.8;">Sistema de Seguridad ZooManager v1.0 activado.</p>

        <a href="<?php echo $base_url; ?>index.php" class="btn-back">
            🏃‍♂️ ESCAPAR AHORA
        </a>
    </div>

    <audio id="trollSound" loop>
        <source src="<?php echo $base_url; ?>assets/sounds/magic_word.mp3" type="audio/mpeg">
    </audio>

    <script>
        var audio = document.getElementById("trollSound");
        
        window.onload = function() {
            var promise = audio.play();
            if (promise !== undefined) {
                promise.then(_ => {}).catch(error => {
                    console.log("Autoplay bloqueado. Esperando clic.");
                });
            }
        };

        function playAudio() {
            audio.play();
        }
        document.body.addEventListener('mousemove', playAudio);
    </script>

</body>
</html>