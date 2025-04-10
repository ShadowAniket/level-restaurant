<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and trim form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $no_of_people = intval($_POST['no_of_people']);
    $time = $_POST['time'];
    $date = $_POST['date'];
    $phone = trim($_POST['phone']);

    // Basic server-side validations
    if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        die("Invalid name. Letters and spaces only.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email.");
    }
    if ($no_of_people < 1 || $no_of_people > 10) {
        die("Number of people must be between 1 and 10.");
    }
    if (!preg_match('/^\d{10,15}$/', $phone)) {
        die("Invalid phone number.");
    }
    // Combine date and time for a timestamp check
    $reservationDatetime = strtotime("$date $time");
    if ($reservationDatetime <= time()) {
        die("Reservation date and time must be in the future.");
    }

    // Include database configuration
    require_once 'db_config.php';

    // Ensure the reservations table exists with proper constraints
    $query = "CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        no_of_people INT NOT NULL CHECK (no_of_people BETWEEN 1 AND 10),
        time TIME NOT NULL,
        date DATE NOT NULL,
        phone VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CHECK (name NOT REGEXP '[0-9]'),
        CHECK (email REGEXP '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'),
        CHECK (phone REGEXP '^[0-9]{10,15}$')
    )";
    if (!$conn->query($query)) {
        die("Table creation failed: " . $conn->error);
    }

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO reservations (name, email, no_of_people, time, date, phone) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssisss", $name, $email, $no_of_people, $time, $date, $phone);

    if ($stmt->execute()) {
        echo "Reservation submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
