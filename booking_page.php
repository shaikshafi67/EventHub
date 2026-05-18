<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}

require_once 'databaseConnection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No event specified.");
}

$event_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Event not found.");
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Event - <?= htmlspecialchars($event['event_name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            background: #fff;
            color: #333;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 30px;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
        }

        .btn-primary {
            background: #764ba2;
            border: none;
        }

        .btn-primary:hover {
            background: #5e3e9c;
        }

        .btn-gradient {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: white;
        }

        .btn-gradient:hover {
            background: linear-gradient(to right, #5a67d8, #6b46c1);
        }

        .qr-container {
            text-align: center;
            margin-top: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
        }

        .qr-container img {
            width: 180px;
            height: 180px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .upi-id {
            font-family: monospace;
            background: #e2e6ea;
            padding: 5px 10px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 5px;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        h1,
        h2 {
            color: #764ba2;
        }

        /* Countdown */
        .countdown-box {
            background: linear-gradient(135deg, #764ba2, #667eea);
            border-radius: 14px;
            padding: 16px 20px;
            margin-top: 18px;
            color: white;
            text-align: center;
        }
        .countdown-box .cd-label {
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            opacity: 0.85;
            margin-bottom: 10px;
        }
        .cd-units {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .cd-unit {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 8px 14px;
            min-width: 60px;
        }
        .cd-unit .cd-num { font-size: 1.7rem; font-weight: 900; line-height: 1; }
        .cd-unit .cd-txt { font-size: 0.7rem; text-transform: uppercase; opacity: 0.8; }
        .cd-expired { color: #ff6ec4; font-weight: 700; font-size: 1rem; }
    </style>
</head>

<body>

    <div class="toast-container position-fixed">
        <div id="copyToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">UPI ID copied to clipboard!</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <div class="container">
        <form id="multiStepForm" action="process_booking.php" method="POST" enctype="multipart/form-data"
            onsubmit="return validateForm()">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">

            <!-- Step 1: Event Summary -->
            <div class="step active" id="step1">
                <h1 class="mb-3">📌 Event Summary</h1>
                <h3><?= htmlspecialchars($event['event_name']) ?></h3>
                <p><strong>Category:</strong>
                    <?= htmlspecialchars($event['event_category'] ?: $event['other_category']) ?></p>
                <p><?= nl2br(htmlspecialchars($event['event_description'])) ?></p>
                <p><strong>Date & Time:</strong> <?= date("M j, Y", strtotime($event['event_date'])) ?>,
                    <?= $event['start_time'] ?> - <?= $event['end_time'] ?>
                </p>
                <p><strong>Venue:</strong> <?= htmlspecialchars($event['venue']) ?></p>
                <p><strong>Speaker:</strong> <?= htmlspecialchars($event['speaker']) ?></p>

                <!-- Countdown Timer -->
                <div class="countdown-box" id="countdownBox">
                  <div class="cd-label">&#9200; Event starts in</div>
                  <div class="cd-units">
                    <div class="cd-unit"><div class="cd-num" id="cd-days">--</div><div class="cd-txt">Days</div></div>
                    <div class="cd-unit"><div class="cd-num" id="cd-hours">--</div><div class="cd-txt">Hours</div></div>
                    <div class="cd-unit"><div class="cd-num" id="cd-mins">--</div><div class="cd-txt">Mins</div></div>
                    <div class="cd-unit"><div class="cd-num" id="cd-secs">--</div><div class="cd-txt">Secs</div></div>
                  </div>
                </div>

                <button type="button" class="btn btn-gradient mt-3" onclick="nextStep(2)">Next</button>
            </div>

            <!-- Step 2: Personal Details -->
            <div class="step" id="step2">
                <h1>🙋‍♂️ Your Details</h1>
                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mobile Number *</label>
                    <input type="tel" name="mobile" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Special Requirements (Optional)</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="button" class="btn btn-secondary me-2" onclick="nextStep(1)">Back</button>
                <button type="button" class="btn btn-gradient" onclick="nextStep(3)">Next</button>
            </div>

            <!-- Step 3: Tickets & Payment -->
            <div class="step" id="step3">
                <h1>🎟️ Tickets & Payment</h1>
                <div class="mb-3">
                    <label class="form-label">Number of Tickets *</label>
                    <input type="number" name="tickets" id="tickets" value="1" min="1" class="form-control" required
                        onchange="updateTotal()">
                </div>
                <p class="fw-bold">Total: ₹<span id="totalAmount"><?= $event['event_price'] ?></span></p>

                <div class="qr-container mt-4">
                    <img src="QR_Code.JPG" alt="UPI QR">
                    <p class="mb-0">Pay using UPI:</p>
                    <span class="upi-id" id="upiId">9951489478@axl</span><br>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="copyUPI()">Copy UPI
                        ID</button>
                </div>

                <div class="mt-4">
                    <label class="form-label">Transaction ID *</label>
                    <input type="text" name="transaction_id" class="form-control" id="transactionId" required>
                </div>

                <div class="mt-3">
                    <label class="form-label">Upload Payment Screenshot (Optional)</label>
                    <input type="file" name="payment_proof" accept="image/*" class="form-control">
                </div>

                <button type="button" class="btn btn-secondary mt-3 me-2" onclick="nextStep(2)">Back</button>
                <button type="submit" class="btn btn-gradient mt-3">📨 Book Now</button>
            </div>
        </form>
    </div>

    <script>
        // Countdown timer
        (function() {
            const eventDateTime = new Date("<?= $event['event_date'] ?>T<?= $event['start_time'] ?>");
            function tick() {
                const now = new Date();
                const diff = eventDateTime - now;
                if (diff <= 0) {
                    document.getElementById('countdownBox').innerHTML = '<span class="cd-expired">&#127881; This event has already started!</span>';
                    return;
                }
                const d = Math.floor(diff / 86400000);
                const h = Math.floor((diff % 86400000) / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                document.getElementById('cd-days').textContent  = String(d).padStart(2,'0');
                document.getElementById('cd-hours').textContent = String(h).padStart(2,'0');
                document.getElementById('cd-mins').textContent  = String(m).padStart(2,'0');
                document.getElementById('cd-secs').textContent  = String(s).padStart(2,'0');
            }
            tick();
            setInterval(tick, 1000);
        })();

        const eventPrice = <?= floatval($event['event_price']) ?>;

        function nextStep(stepNum) {
            if (stepNum === 3) {
                const fullName = document.querySelector('[name="full_name"]').value.trim();
                const email = document.querySelector('[name="email"]').value.trim();
                const mobile = document.querySelector('[name="mobile"]').value.trim();

                if (!fullName || !email || !mobile) {
                    alert("Please fill in all required personal details.");
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert("Please enter a valid email address.");
                    return;
                }

                const mobileRegex = /^\d{10}$/;
                if (!mobileRegex.test(mobile)) {
                    alert("Please enter a valid 10-digit mobile number.");
                    return;
                }
            }

            document.querySelectorAll('.step').forEach(step => step.classList.remove('active'));
            document.getElementById('step' + stepNum).classList.add('active');
        }

        function updateTotal() {
            const tickets = document.getElementById("tickets").value;
            const total = tickets * eventPrice;
            document.getElementById("totalAmount").innerText = total.toFixed(2);
        }

        function copyUPI() {
            const upiId = document.getElementById("upiId").innerText;
            navigator.clipboard.writeText(upiId).then(() => {
                const toast = new bootstrap.Toast(document.getElementById('copyToast'));
                toast.show();
            }).catch(err => {
                alert("Failed to copy UPI ID");
            });
        }

        function validateForm() {
            const tickets = parseInt(document.getElementById('tickets').value);
            const transactionId = document.getElementById('transactionId').value.trim();

            if (isNaN(tickets) || tickets < 1) {
                alert("Please enter a valid number of tickets.");
                return false;
            }

            if (!transactionId) {
                alert("Please enter a valid Transaction ID.");
                return false;
            }

            return true;
        }
    </script>
</body>

</html>