<?php
session_start();
require_once 'functions.php';

// se vuoi il login attivo, lascia questa riga
require_login();

$info  = null;
$error = null;

if (!empty($_GET['flight'])) {
    $flightNumber = strtoupper(trim($_GET['flight']));
    $info = get_previous_leg_info($flightNumber);

    if ($info === false) {
        $error = "No data available for flight " . htmlspecialchars($flightNumber);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PrevLeg Tracker</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-left">
        <span class="logo">PrevLeg<span>Tracker</span></span>
    </div>
    <div class="topbar-right">
        <?php if (isset($_SESSION['user'])): ?>
            <span class="user">Logged in as <?= htmlspecialchars($_SESSION['user']) ?></span>
            <a href="logout.php" class="btn-out">Logout</a>
        <?php endif; ?>
    </div>
</header>

<main class="container">
    <section class="card">
        <h1>Check flight data &amp; delay</h1>
        <p class="subtitle">
            Enter your flight number (e.g. AZ5969) to see route, schedule, predicted arrival
            and an estimated delay.
        </p>

        <form method="get" class="inline-form">
            <label for="flight">Flight number</label>
            <input
                type="text"
                name="flight"
                id="flight"
                placeholder="AZ5969"
                value="<?= isset($_GET['flight']) ? htmlspecialchars($_GET['flight']) : '' ?>"
                required
            >
            <button type="submit">Check</button>
        </form>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
    </section>

    <?php if ($info): ?>
        <?php
            // classe per il badge del delay
            $delayClass = 'delay-on-time';
            if ($info['delay_minutes'] > 15 && $info['delay_minutes'] <= 40) {
                $delayClass = 'delay-minor';
            } elseif ($info['delay_minutes'] > 40) {
                $delayClass = 'delay-major';
            }
        ?>
        <section class="card result-card">
            <!-- HEADER RIASSUNTIVO TIPO APP -->
            <div class="result-header">
                <div>
                    <h2><?= htmlspecialchars($info['flight']) ?></h2>
                    <p class="route-small">
                        <?= htmlspecialchars($info['from_iata']) ?> →
                        <?= htmlspecialchars($info['to_iata']) ?>
                    </p>
                </div>
                <div class="status-block">
                    <span class="status-label">
                        Status:
                        <strong><?= htmlspecialchars($info['status']) ?></strong>
                    </span>
                    <span class="delay-badge <?= $delayClass ?>">
                        <?= (int)$info['delay_minutes'] ?> min
                    </span>
                </div>
            </div>

            <!-- DETTAGLI IN GRIGLIA -->
            <div class="grid">
                <div>
                    <h3>Route</h3>
                    <p>
                        <strong><?= htmlspecialchars($info['from_iata']) ?></strong>
                        (<?= htmlspecialchars($info['from_name']) ?>)
                        →
                        <strong><?= htmlspecialchars($info['to_iata']) ?></strong>
                        (<?= htmlspecialchars($info['to_name']) ?>)
                    </p>
                </div>

                <div>
                    <h3>Airline &amp; aircraft</h3>
                    <p>
                        Airline:
                        <strong><?= htmlspecialchars($info['airline']) ?></strong>
                        (<?= htmlspecialchars($info['airline_iata']) ?>)
                    </p>
                    <p>
                        Aircraft:
                        <strong><?= htmlspecialchars($info['aircraft_model']) ?></strong>
                    </p>
                </div>

                <div>
                    <h3>Schedule</h3>
                    <p>Scheduled departure:<br>
                        <strong><?= htmlspecialchars($info['sched_dep_local']) ?></strong>
                    </p>
                    <p>Scheduled arrival:<br>
                        <strong><?= htmlspecialchars($info['sched_arr_local']) ?></strong>
                    </p>
                    <p>Predicted arrival:<br>
                        <strong><?= htmlspecialchars($info['pred_arr_local']) ?></strong>
                    </p>
                </div>

                <div>
                    <h3>Distance &amp; delay</h3>
                    <p>Great circle distance:<br>
                        <strong><?= htmlspecialchars($info['distance_nm']) ?> nm</strong>
                    </p>
                    <p>Estimated delay:<br>
                        <strong><?= (int)$info['delay_minutes'] ?> min</strong>
                    </p>
                    <p>Status:<br>
                        <strong><?= htmlspecialchars($info['status']) ?></strong>
                    </p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
</body>
</html>
