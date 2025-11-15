<?php
// Avvia la sessione in tutta l'app
session_start();

// ⚠ DEMO ONLY – in produzione usa password_hash + password_verify
define('APP_USER', 'gigi');
define('APP_PASS_PLAIN', 'password123');

// (Se in futuro usi una vera API, metti qui URL e API KEY, ma NON committarle su GitHub)
// es:
// define('API_BASE_URL', 'https://api.example.com');
// define('API_KEY', 'INSERISCI_LA_TUA_API_KEY');
