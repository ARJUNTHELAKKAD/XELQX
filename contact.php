<?php
// Database credentials
$host = "localhost";
$db = "xelqx";
$user = "root";       // replace with your DB username
$pass = "";           // replace with your DB password

// Admin email
$admin_email = "support@xelqx.com";

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
    die("Database connection failed: " . $conn->connect_error);
}

// Get POST data
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Basic validation
if($name == '' || $email == '' || $subject == '' || $message == ''){
    echo "error";
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)");
$stmt->bind_param("ssss",$name,$email,$subject,$message);

if($stmt->execute()){
    // Send confirmation email to user
    $user_subject = "Thanks for contacting XELQX!";
    $user_message = "Hi $name,\n\nThanks for reaching out! We received your message:\n\nSubject: $subject\nMessage: $message\n\nWe will get back to you shortly.\n\n– XELQX Team";
    $user_headers = "From: $admin_email";
    mail($email, $user_subject, $user_message, $user_headers);

    // Send notification email to admin
    $admin_subject = "New Contact Form Submission";
    $admin_message = "New message from contact form:\n\nName: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";
    mail($admin_email, $admin_subject, $admin_message, "From: $email");

    echo "success";
} else {
    echo "error";
}

$stmt->close();
$conn->close();
?>
<?php
include 'db_connect.php'; // Include your DB connection

// Get form data safely
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$subject = isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : '';
$message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

if ($name && $email && $subject && $message) {

    // Create contact table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100),
        subject VARCHAR(150),
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Database Error: " . $conn->error;
    }

    $stmt->close();

    // OPTIONAL: Send an email notification (you can remove this part if not needed)
    $to = "support@xelqx.com"; // Change this to your email
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $mailBody = "
        <h2>New Contact Message from XELQX Website</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong><br>$message</p>
    ";

    @mail($to, "Contact Form: " . $subject, $mailBody, $headers);
    
} else {
    echo "error";
}

$conn->close();
?>
