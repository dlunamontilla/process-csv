<?php
// Procesar recapcha de Google:
function reCAPTCHA($response) {

    // Ruta de la petición:
    $url = "https://www.google.com/recaptcha/api/siteverify";

    $ip = @$_SERVER['REMOTE_ADDR'];

    // Datos de envío:
    $datos = [
        "secret" => "{{Clave secreta}}",
        "response" => $response,
        "remoteip" => $ip
    ];

    // Opciones de envío:
    $opciones = [
        "http" => [
            "header" => "Content-type: application/x-www-form-urlencoded\r\n",
            "method" => "POST",
            "content" => http_build_query($datos)
        ]
    ];

    // Preparando la petición:
    $contexto = stream_context_create($opciones);

    // Enviar la petición:
    $resultados = file_get_contents($url, false, $contexto);
    $resultados = json_decode($resultados);

    return $resultados->success;
}
