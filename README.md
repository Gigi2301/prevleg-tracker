# prevleg-tracker
Previoius leg tracker for commercial flights
# Previous Leg Tracker (PHP)

Small PHP web app to check the **previous leg** of a flight and see its delay,
using JSON data and a simple login system.

Designed as a learning / portfolio project that combines:

- **Aviation logic** (rotation / previous leg)
- **PHP + JSON handling**
- **Basic authentication (session-based)**
- **Clean HTML/CSS UI**

---

## Features

- Login page (demo credentials)
- Flight input form (e.g. `AZ5969`)
- Mock JSON data (`flights_sample.json`) simulating an API response
- Previous leg details:
  - Previous flight number
  - From / To airports
  - Scheduled vs actual arrival time
  - Arrival delay in minutes
  - Estimated impact on your flight

---

## Tech stack

- PHP (procedural, simple)
- HTML5
- CSS (no framework)
- JSON (local file for now)

---

## Demo credentials

```text
user: gigi
password: password123
