<?php
require_once 'config.php';

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function login(string $username, string $password): bool {
    if ($username === APP_USER && $password === APP_PASS_PLAIN) {
        $_SESSION['user'] = $username;
        return true;
    }
    return false;
}

function logout(): void {
    $_SESSION = [];
    if (session_id() !== '') {
        session_destroy();
    }
}

// --- Se in futuro usi una vera API ---
// function call_api(string $url): array|false {
//     $ch = curl_init($url);
//     curl_setopt_array($ch, [
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_TIMEOUT => 10,
//     ]);
//     $res = curl_exec($ch);
//     curl_close($ch);
//
//     if ($res === false) return false;
//     $data = json_decode($res, true);
//     return is_array($data) ? $data : false;
// }

// MOCK: lettura da JSON locale per testare senza API
function get_flight_data_mock(string $flightNumber): array|false {
    $jsonPath = __DIR__ . '/flights_sample.json';
    if (!file_exists($jsonPath)) return false;

    $json = file_get_contents($jsonPath);
    if ($json === false) return false;

    $data = json_decode($json, true);
    if (!is_array($data) || empty($data['flights'])) return false;

    $flightNumber = strtoupper($flightNumber);

    foreach ($data['flights'] as $flight) {
        if (strtoupper($flight['flight_number']) === $flightNumber) {
            return $flight;
        }
    }

    return false;
}

/**
 * Ritorna info sul volo + rotazione precedente.
 *
 * Ritorno:
 * [
 *   'flight'              => 'AZ5969',
 *   'registration'        => 'EI-ABC',
 *   'prev_flight'         => 'AZ5968',
 *   'prev_from'           => 'LIML',
 *   'prev_to'             => 'LIEE',
 *   'prev_sched_arrival'  => '2025-11-16T13:00:00Z',
 *   'prev_actual_arrival' => '2025-11-16T13:32:00Z',
 *   'prev_delay_minutes'  => 32,
 *   'impact'              => 'High chance of delay'
 * ]
 */
function get_previous_leg_info(string $flightNumber): array|false {
    // Per ora usiamo il mock locale
    $flight = get_flight_data_mock($flightNumber);
    if ($flight === false) return false;

    $prev = $flight['previous_leg'] ?? null;
    if (!$prev) return false;

    $delay = (int)($prev['arrival_delay_minutes'] ?? 0);

    if ($delay <= 15) {
        $impact = 'Likely on time';
    } elseif ($delay <= 40) {
        $impact = 'Possible minor delay';
    } else {
        $impact = 'High chance of delay';
    }

    return [
        'flight'              => $flight['flight_number'] ?? strtoupper($flightNumber),
        'registration'        => $flight['registration'] ?? 'N/A',
        'prev_flight'         => $prev['flight_number'] ?? 'N/A',
        'prev_from'           => $prev['from'] ?? 'N/A',
        'prev_to'             => $prev['to'] ?? 'N/A',
        'prev_sched_arrival'  => $prev['scheduled_arrival'] ?? 'N/A',
        'prev_actual_arrival' => $prev['actual_arrival'] ?? 'N/A',
        'prev_delay_minutes'  => $delay,
        'impact'              => $impact,
    ];
}

