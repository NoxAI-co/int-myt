<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Integracion;

class SmsService
{
    /**
     * Envía un SMS usando el servicio configurado
     * 
     * @param string $numero Número de teléfono
     * @param string $mensaje Mensaje a enviar
     * @param int $empresaId ID de la empresa
     * @param string $contexto Contexto para logs (ej: 'PagoOportuno', 'PagoVencimiento')
     * @return array ['success' => bool, 'message' => string]
     */
    public static function enviarSMS($numero, $mensaje, $empresaId = 1, $contexto = 'SMS')
    {
        $servicio = Integracion::where('empresa', $empresaId)
                              ->where('tipo', 'SMS')
                              ->where('status', 1)
                              ->first();

        if (!$servicio) {
            Log::warning("No hay servicio SMS habilitado para empresa {$empresaId} en contexto {$contexto}");
            return ['success' => false, 'message' => 'No hay servicio SMS habilitado'];
        }

        // Limpiar número
        $numero = str_replace(['+', ' '], '', $numero);
        if (!str_starts_with($numero, '57')) {
            $numero = '57' . $numero;
        }

        if ($servicio->nombre == 'Hablame SMS v5') {
            return self::enviarHablameV5($numero, $mensaje, $servicio, $contexto);
        } elseif ($servicio->nombre == 'Hablame SMS') {
            return self::enviarHablameV3($numero, $mensaje, $servicio, $contexto);
        } elseif ($servicio->nombre == 'SmsEasySms') {
            return self::enviarSmsEasy($numero, $mensaje, $servicio, $contexto);
        } elseif ($servicio->nombre == 'Colombia RED') {
            return self::enviarColombiaRed($numero, $mensaje, $servicio, $contexto);
        } elseif ($servicio->nombre == '360nrs') {
            return self::enviar360nrs($numero, $mensaje, $servicio, $contexto);
        }

        Log::warning("Servicio SMS no reconocido: {$servicio->nombre} en contexto {$contexto}");
        return ['success' => false, 'message' => 'Servicio SMS no reconocido'];
    }

    /**
     * Envía SMS usando Hablame v5
     */
    private static function enviarHablameV5($numero, $mensaje, $servicio, $contexto)
    {
        if (!$servicio->api_key) {
            Log::warning("Falta API Key para Hablame v5 en contexto {$contexto}");
            return ['success' => false, 'message' => 'Falta API Key para Hablame v5'];
        }

        $post = [
            'priority' => false,
            'certificate' => false,
            'from' => $servicio->numero ? $servicio->numero : 'SMS',
            'flash' => false,
            'messages' => [
                [
                    'to' => $numero,
                    'text' => $mensaje
                ]
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.hablame.co/api/sms/v5/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post),
            CURLOPT_HTTPHEADER => [
                'X-Hablame-Key: ' . $servicio->api_key,
                'accept: application/json',
                'content-type: application/json'
            ],
        ]);

        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error("Error cURL en {$contexto} Hablame v5: {$err}");
            return ['success' => false, 'message' => "Error cURL: {$err}"];
        }

        $response = json_decode($result, true);
        if (isset($response['error']) || isset($response['errors'])) {
            $errorMsg = isset($response['error']) ? $response['error'] : 'Error en el envío';
            if (isset($response['errors'])) {
                $errorMsg = is_array($response['errors']) ? implode(', ', $response['errors']) : $response['errors'];
            }
            Log::error("Error en envío SMS {$contexto} Hablame v5: {$errorMsg}");
            return ['success' => false, 'message' => $errorMsg];
        }

        $successMsg = 'SMS enviado correctamente con Hablame v5';
        if (isset($response['status'])) {
            $successMsg = 'SMS enviado - Status: ' . $response['status'];
        }
        Log::info("SMS enviado exitosamente en {$contexto} Hablame v5: {$successMsg}");
        return ['success' => true, 'message' => $successMsg];
    }

    /**
     * Envía SMS usando Hablame v3
     */
    private static function enviarHablameV3($numero, $mensaje, $servicio, $contexto)
    {
        if (!$servicio->api_key || !$servicio->user || !$servicio->pass) {
            Log::warning("Faltan credenciales para Hablame v3 en contexto {$contexto}");
            return ['success' => false, 'message' => 'Faltan credenciales para Hablame v3'];
        }

        $post = [
            'toNumber' => $numero,
            'sms' => $mensaje
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api103.hablame.co/api/sms/v3/send/marketing',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post),
            CURLOPT_HTTPHEADER => [
                'account: ' . $servicio->user,
                'apiKey: ' . $servicio->api_key,
                'token: ' . $servicio->pass,
                'Content-Type: application/json'
            ],
        ]);

        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error("Error cURL en {$contexto} Hablame v3: {$err}");
            return ['success' => false, 'message' => "Error cURL: {$err}"];
        }

        $response = json_decode($result, true);
        if (isset($response['error'])) {
            $errorMsg = $response['error']['code'] == 1000303 ? 'Cuenta no encontrada' : $response['error']['details'];
            Log::error("Error en envío SMS {$contexto} Hablame v3: {$errorMsg}");
            return ['success' => false, 'message' => $errorMsg];
        }

        $successMsg = 'SMS enviado correctamente con Hablame v3';
        if (isset($response['status'])) {
            if ($response['status'] == '1x000') {
                $successMsg = 'SMS recibido por hablame exitosamente';
            } elseif ($response['status'] == '1x152') {
                $successMsg = 'SMS entregado al operador';
            } elseif ($response['status'] == '1x153') {
                $successMsg = 'SMS entregado al celular';
            }
        }
        Log::info("SMS enviado exitosamente en {$contexto} Hablame v3: {$successMsg}");
        return ['success' => true, 'message' => $successMsg];
    }

    /**
     * Envía SMS usando SmsEasySms
     */
    private static function enviarSmsEasy($numero, $mensaje, $servicio, $contexto)
    {
        if (!$servicio->user || !$servicio->pass) {
            Log::warning("Faltan credenciales para SmsEasySms en contexto {$contexto}");
            return ['success' => false, 'message' => 'Faltan credenciales para SmsEasySms'];
        }

        $post = [
            'to' => [$numero],
            'text' => $mensaje,
            'from' => 'SMS'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://sms.istsas.com/Api/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Authorization: Basic " . base64_encode($servicio->user . ":" . $servicio->pass)
        ]);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::error("Error cURL en {$contexto} SmsEasySms: {$err}");
            return ['success' => false, 'message' => "Error cURL: {$err}"];
        }

        $response = json_decode($result, true);
        if (isset($response['error'])) {
            $errorCodes = [
                102 => "No hay destinatarios válidos",
                103 => "Nombre de usuario o contraseña desconocidos",
                104 => "Falta el mensaje de texto",
                105 => "Mensaje de texto demasiado largo",
                106 => "Falta el remitente",
                107 => "Remitente demasiado largo",
                108 => "No hay fecha y hora válida para enviar",
                109 => "URL de notificación incorrecta",
                110 => "Se superó el número máximo de piezas permitido",
                111 => "Crédito/Saldo insuficiente",
                112 => "Dirección IP no permitida",
                113 => "Codificación no válida"
            ];

            $errorMsg = $errorCodes[$response['error']['code']] ?? $response['error']['description'];
            Log::error("Error en envío SMS {$contexto} SmsEasySms: {$errorMsg}");
            return ['success' => false, 'message' => $errorMsg];
        }

        Log::info("SMS enviado exitosamente en {$contexto} SmsEasySms");
        return ['success' => true, 'message' => 'Mensaje enviado correctamente'];
    }

    /**
     * Envía SMS usando Colombia RED
     */
    private static function enviarColombiaRed($numero, $mensaje, $servicio, $contexto)
    {
        if (!$servicio->user || !$servicio->pass) {
            Log::warning("Faltan credenciales para Colombia RED en contexto {$contexto}");
            return ['success' => false, 'message' => 'Faltan credenciales para Colombia RED'];
        }

        $post = [
            'to' => [$numero],
            'text' => $mensaje,
            'from' => ''
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://masivos.colombiared.com.co/Api/rest/message");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Authorization: Basic " . base64_encode($servicio->user . ":" . $servicio->pass)
        ]);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::error("Error cURL en {$contexto} Colombia RED: {$err}");
            return ['success' => false, 'message' => "Error cURL: {$err}"];
        }

        $response = json_decode($result, true);
        if (isset($response['error'])) {
            $errorCodes = [
                102 => "No hay destinatarios válidos",
                103 => "Nombre de usuario o contraseña desconocidos",
                104 => "Falta el mensaje de texto",
                105 => "Mensaje de texto demasiado largo",
                106 => "Falta el remitente",
                107 => "Remitente demasiado largo",
                108 => "No hay fecha y hora válida para enviar",
                109 => "URL de notificación incorrecta",
                110 => "Se superó el número máximo de piezas permitido",
                111 => "Crédito/Saldo insuficiente",
                112 => "Dirección IP no permitida",
                113 => "Codificación no válida"
            ];

            $errorMsg = $errorCodes[$response['error']['code']] ?? $response['error']['description'];
            Log::error("Error en envío SMS {$contexto} Colombia RED: {$errorMsg}");
            return ['success' => false, 'message' => $errorMsg];
        }

        Log::info("SMS enviado exitosamente en {$contexto} Colombia RED");
        return ['success' => true, 'message' => 'Mensaje enviado correctamente'];
    }

    /**
     * Envía SMS usando 360nrs
     */
    private static function enviar360nrs($numero, $mensaje, $servicio, $contexto)
    {
        $post = [
            'to' => [$numero],
            'text' => $mensaje,
            'from' => 'SMS'
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://dashboard.360nrs.com/api/rest/sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Basic aW50ZWdyYTM2MDpUUHlhNzQ/Iw=='
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error("Error cURL en {$contexto} 360nrs: {$err}");
            return ['success' => false, 'message' => "Error cURL: {$err}"];
        }

        $response = json_decode($response, true);
        if (isset($response['error'])) {
            Log::error("Error en envío SMS {$contexto} 360nrs: " . json_encode($response['error']));
            return ['success' => false, 'message' => 'Error en el envío'];
        }

        Log::info("SMS enviado exitosamente en {$contexto} 360nrs");
        return ['success' => true, 'message' => 'Mensaje enviado correctamente'];
    }

    /**
     * Envía múltiples SMS
     * 
     * @param array $contactos Array de contactos con números de teléfono
     * @param string $mensaje Mensaje a enviar
     * @param int $empresaId ID de la empresa
     * @param string $contexto Contexto para logs
     * @return array ['success' => int, 'failed' => int, 'total' => int]
     */
    public static function enviarMultiplesSMS($contactos, $mensaje, $empresaId = 1, $contexto = 'SMS')
    {
        $success = 0;
        $failed = 0;

        foreach ($contactos as $contacto) {
            $numero = isset($contacto->celular) ? $contacto->celular : $contacto['celular'];
            
            if (empty($numero)) {
                $failed++;
                continue;
            }

            $resultado = self::enviarSMS($numero, $mensaje, $empresaId, $contexto);
            
            if ($resultado['success']) {
                $success++;
            } else {
                $failed++;
            }

            // Pequeña pausa para evitar sobrecargar el servicio
            usleep(100000); // 0.1 segundos
        }

        Log::info("Envío masivo completado en {$contexto}: {$success} exitosos, {$failed} fallidos");
        
        return [
            'success' => $success,
            'failed' => $failed,
            'total' => $success + $failed
        ];
    }
}
