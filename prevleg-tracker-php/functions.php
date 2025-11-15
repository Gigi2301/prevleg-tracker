<?php
require_once 'config.php';

/**
 * L’utente è loggato?
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

/**
 * Se non loggato → vai al login
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Login molto semplice (usa APP_USER / APP_PASS_PLAIN da config.php)
 */
function login(string $username, string $password): bool {
    if ($username === APP_USER && $password === APP_PASS_PLAIN) {
        $_SESSION['user'] = $username;
        return true;
    }
    return false;
}

/**
 * Logout
 */
function logout(): void {
    $_SESSION = [];
    if (session_id() !== '') {
        session_destroy();
    }
}

/**
 * Chiamata ad AeroDataBox tramite RapidAPI
 * Ritorna info sul volo (route, orari, distanza, delay stimato).
 */
function get_previous_leg_info(string $flightNumber): array|false {

    // Pulizia del numero volo
    $flightNumber = strtoupper(trim($flightNumber));

    // Data del volo: per ora usiamo OGGI
    $date = date('Y-m-d');

    // Endpoint AeroDataBox: /flights/number/{number}/{date}
    $url = "https://" . AERODATABOX_HOST . "/flights/number/$flightNumber/$date";

    // Header RapidAPI
    $headers = [
        "X-RapidAPI-Key: "  . AERODATABOX_API_KEY,
        "X-RapidAPI-Host: " . AERODATABOX_HOST,
    ];

    // Chiamata cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 10,
    ]);

    $res     = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($res === false) {
        // Errore rete/API
        error_log("cURL error: " . $curlErr);
        return false;
    }

    // Salviamo sempre la risposta grezza per debug
    file_put_contents(__DIR__ . '/last_response.json', $res);

    $data = json_decode($res, true);
    if (!is_array($data) || !isset($data[0])) {
        return false;
    }

    $flight = $data[0];

    // ------------ ESTRAZIONE DEI CAMPI CHE ABBIAMO DAVVERO ------------

    $number    = $flight['number']             ?? $flightNumber;
    $status    = $flight['status']             ?? 'Unknown';

    $airlineName   = $flight['airline']['name']   ?? 'N/A';
    $airlineIata   = $flight['airline']['iata']   ?? 'N/A';
    $aircraftModel = $flight['aircraft']['model'] ?? 'N/A';

    $depAirport = $flight['departure']['airport'] ?? [];
    $arrAirport = $flight['arrival']['airport']   ?? [];

    $depIata = $depAirport['iata'] ?? 'N/A';
    $depName = $depAirport['name'] ?? 'N/A';
    $arrIata = $arrAirport['iata'] ?? 'N/A';
    $arrName = $arrAirport['name'] ?? 'N/A';

    $schedDepLocal = $flight['departure']['scheduledTime']['local'] ?? 'N/A';
    $schedArrLocal = $flight['arrival']['scheduledTime']['local']   ?? 'N/A';
    $predArrLocal  = $flight['arrival']['predictedTime']['local']   ?? 'N/A';

    $distanceNm = $flight['greatCircleDistance']['nm'] ?? null;

    // Calcolo "ritardo" (se c'è predicted)
    $delayMinutes = 0;
    if ($schedArrLocal !== 'N/A' && $predArrLocal !== 'N/A') {
        $d1 = strtotime($schedArrLocal);
        $d2 = strtotime($predArrLocal);
        if ($d1 && $d2) {
            $delayMinutes = round(($d2 - $d1) / 60);
        }
    }

    // Ritorniamo una struttura semplice da usare in interfaccia
    return [
        'flight'         => $number,
        'status'         => $status,
        'airline'        => $airlineName,
        'airline_iata'   => $airlineIata,
        'aircraft_model' => $aircraftModel,

        'from_iata'      => $depIata,
        'from_name'      => $depName,
        'to_iata'        => $arrIata,
        'to_name'        => $arrName,

        'sched_dep_local' => $schedDepLocal,
        'sched_arr_local' => $schedArrLocal,
        'pred_arr_local'  => $predArrLocal,

        'distance_nm'    => $distanceNm,
        'delay_minutes'  => $delayMinutes,
    ];
}
