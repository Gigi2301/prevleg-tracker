<?php
require_once 'functions.php';
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
    <title>Tracker – Previous Leg</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-left">
        <span class="logo">PrevLeg<span>Tracker</span></span>
    </div>
    <div class="topbar-right">
        <span class="user">Logged in as <?= htmlspecialchars($_SESSION['user']) ?></span>
        <a href="logout.php" class="btn-out">Logout</a>
    </div>
</header>

<main class="container">
    <section class="card">
        <h1>Check previous leg delay</h1>
        <p class="subtitle">
            Enter your flight number (e.g. AZ5969) to see the previous leg and its delay.
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
        <section class="card result-card">
            <h2>Result for <?= htmlspecialchars($info['flight']) ?></h2>

            <div class="grid">
                <div>
                    <h3>Aircraft</h3>
                    <p>Registration: <strong><?= htmlspecialchars($info['registration']) ?></strong></p>
                </div>
                <div>
                    <h3>Previous leg</h3>
                    <p>
                        <strong><?= htmlspecialchars($info['prev_flight']) ?></strong><br>
                        <?= htmlspecialchars($info['prev_from']) ?>
                        →
                        <?= htmlspecialchars($info['prev_to']) ?>
                    </p>
                </div>
                <div>
                    <h3>Arrival</h3>
                    <p>Scheduled: <?= htmlspecialchars($info['prev_sched_arrival']) ?></p>
                    <p>Actual: <?= htmlspecialchars($info['prev_actual_arrival']) ?></p>
                </div>
                <div>
                    <h3>Delay & impact</h3>
                    <p>Delay: <strong><?= (int)$info['prev_delay_minutes'] ?> min</strong></p>
                    <p>Impact: <strong><?= htmlspecialchars($info['impact']) ?></strong></p>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
</body>
</html>

