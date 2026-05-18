<?php
require_once 'databaseConnection.php';

if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    die("No booking ID provided.");
}

$booking_id = intval($_GET['booking_id']);

$stmt = $conn->prepare("
    SELECT 
        eb.id AS booking_id,
        eb.full_name,
        eb.email,
        eb.mobile,
        eb.tickets,
        eb.total_amount,
        eb.transaction_id,
        eb.payment_proof,
        eb.booking_date,
        e.event_name,
        e.event_description,
        e.event_price,
        e.event_date,
        e.venue,
        e.event_poster AS event_image
    FROM event_bookings eb
    JOIN events e ON eb.event_id = e.id
    WHERE eb.id = ?
");

$stmt->bind_param("i", $booking_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Booking Ticket - #<?php echo htmlspecialchars($booking['booking_id']); ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        /* Background with vibrant gradient */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ff6ec4 0%, #7873f5 50%, #4ade80 100%);
            color: #222;
            margin: 30px auto;
            padding: 0 15px;
            max-width: 480px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.5s ease;
        }

        /* Card container with glowing neon shadow */
        .ticket {
            background: linear-gradient(145deg, #ffffffcc, #ffffffdd);
            border-radius: 20px;
            box-shadow:
                0 0 20px #ff6ec4,
                0 0 40px #7873f5,
                0 0 60px #4ade80,
                inset 0 0 10px #fff;
            padding: 35px 40px;
            border: 2px solid #fff;
            position: relative;
            width: 100%;
            max-width: 480px;
            animation: pulse 6s ease-in-out infinite alternate;
        }

        /* Pulse animation for the glow */
        @keyframes pulse {
            0% {
                box-shadow:
                    0 0 15px #ff6ec4,
                    0 0 30px #7873f5,
                    0 0 45px #4ade80,
                    inset 0 0 10px #fff;
            }

            100% {
                box-shadow:
                    0 0 30px #ff6ec4,
                    0 0 50px #7873f5,
                    0 0 70px #4ade80,
                    inset 0 0 15px #fff;
            }
        }

        h1 {
            font-weight: 700;
            font-size: 2.4rem;
            color: #4ade80;
            /* bright green */
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 0 8px #4ade80, 0 0 15px #4ade80;
        }

        h2 {
            font-weight: 700;
            font-size: 1.4rem;
            text-align: center;
            color: #7873f5;
            /* soft violet */
            margin-bottom: 28px;
            letter-spacing: 1.5px;
            text-shadow: 0 0 5px #7873f5;
        }

        .event-image {
            display: block;
            margin: 0 auto 28px;
            max-width: 100%;
            border-radius: 15px;
            border: 3px solid #ff6ec4;
            box-shadow: 0 0 15px #ff6ec4;
            transition: transform 0.3s ease;
        }

        .event-image:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px #ff6ec4, 0 0 40px #7873f5;
        }

        .details {
            font-size: 1rem;
            line-height: 1.6;
            color: #333;
            font-weight: 500;
            margin-bottom: 30px;
            border-radius: 12px;
            background: #f5f7ffcc;
            padding: 20px 25px;
            box-shadow: inset 0 0 12px #4ade8088;
        }

        .details div {
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #c4c7ff88;
            display: flex;
            justify-content: space-between;
            font-weight: 600;
            color: #4a4a4a;
            letter-spacing: 0.05em;
        }

        .details div:last-child {
            border: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .label {
            color: #7873f5;
            min-width: 140px;
            font-weight: 700;
            text-shadow: 0 0 3px #7873f5;
        }

        /* Neon style back button */
        .btn-back {
            display: block;
            width: 100%;
            background: linear-gradient(90deg, #ff6ec4, #7873f5, #4ade80);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px 0;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            margin-top: 30px;
            text-decoration: none;
            box-shadow:
                0 0 10px #ff6ec4,
                0 0 20px #7873f5,
                0 0 30px #4ade80;
            transition: all 0.3s ease;
            user-select: none;
        }

        .btn-back:hover,
        .btn-back:focus {
            background: linear-gradient(90deg, #4ade80, #7873f5, #ff6ec4);
            box-shadow:
                0 0 20px #4ade80,
                0 0 40px #7873f5,
                0 0 60px #ff6ec4;
            outline: none;
            transform: scale(1.05);
        }

        /* Print button */
        .btn-print {
            display: block;
            width: 100%;
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: 2px solid rgba(255,255,255,0.6);
            border-radius: 12px;
            padding: 12px 0;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            margin-top: 14px;
            transition: background 0.3s;
        }
        .btn-print:hover { background: rgba(255,255,255,0.35); }

        @media print {
            body { background: white !important; margin: 0; }
            .btn-back, .btn-print { display: none !important; }
            .ticket {
                box-shadow: none !important;
                border: 2px solid #ccc !important;
                max-width: 100%;
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                max-width: 100%;
                margin: 20px 10px;
                padding: 0 10px;
            }

            h1 {
                font-size: 2rem;
            }

            h2 {
                font-size: 1.2rem;
            }

            .details div {
                flex-direction: column;
                align-items: flex-start;
            }

            .label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>

<body>

    <div class="ticket">
        <h1>Ticket</h1>
        <h2>Booking ID: #<?php echo htmlspecialchars($booking['booking_id']); ?></h2>

        <img src="<?php echo htmlspecialchars($booking['event_image']); ?>" alt="Event Image" class="event-image" />

        <div class="details">
            <div><span class="label">Event Name:</span> <?php echo htmlspecialchars($booking['event_name']); ?></div>
            <div><span class="label">Venue:</span> <?php echo htmlspecialchars($booking['venue']); ?></div>
            <div><span class="label">Event Date:</span> <?php echo htmlspecialchars($booking['event_date']); ?></div>
            <div><span class="label">Number of Tickets:</span> <?php echo htmlspecialchars($booking['tickets']); ?>
            </div>
            <div><span class="label">Total Amount:</span>
                $<?php echo htmlspecialchars(number_format($booking['total_amount'], 2)); ?></div>
            <div><span class="label">Transaction ID:</span> <?php echo htmlspecialchars($booking['transaction_id']); ?>
            </div>
            <div><span class="label">Booked By:</span> <?php echo htmlspecialchars($booking['full_name']); ?></div>
            <div><span class="label">Email:</span> <?php echo htmlspecialchars($booking['email']); ?></div>
            <div><span class="label">Mobile:</span> <?php echo htmlspecialchars($booking['mobile']); ?></div>
            <div><span class="label">Booking Date:</span> <?php echo htmlspecialchars($booking['booking_date']); ?>
            </div>
        </div>

        <button onclick="window.print()" class="btn-print">&#128438; Print / Save Ticket</button>
        <a href="main.php" class="btn-back">&#8592; Back to Home</a>
    </div>

</body>

</html>