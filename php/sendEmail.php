<?php
// php/sendEmail.php
// Contact form email handler using SendGrid HTTP API

// Load configuration if available (not in Git)
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Name is required (minimum 2 characters)';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Message is required (minimum 10 characters)';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

// Sanitize inputs
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Email configuration
$to = 'remrem0415@gmail.com';
$subject = 'Portfolio Contact: ' . $name;

// SendGrid API key — set this as an environment variable or in config.php
// For local development: create php/config.php with define('SENDGRID_API_KEY', 'your-key')
// For production: set SENDGRID_API_KEY environment variable or create config.php
$apiKey = defined('SENDGRID_API_KEY') ? SENDGRID_API_KEY : getenv('SENDGRID_API_KEY');

// Email body (HTML version)
$emailBodyHtml = "<html><body>";
$emailBodyHtml .= "<h2>New contact form submission from sigfrid.freehosting.dev</h2>";
$emailBodyHtml .= "<p><strong>Name:</strong> $name</p>";
$emailBodyHtml .= "<p><strong>Email:</strong> $email</p>";
$emailBodyHtml .= "<p><strong>Message:</strong><br>" . nl2br($message) . "</p>";
$emailBodyHtml .= "</body></html>";

// Plain text version
$emailBodyText = "New contact form submission from sigfrid.freehosting.dev\n\n";
$emailBodyText .= "Name: $name\n";
$emailBodyText .= "Email: $email\n";
$emailBodyText .= "Message:\n$message\n";

// Send email using SendGrid HTTP API
try {
    $payload = json_encode([
        'personalizations' => [
            [
                'to' => [
                    ['email' => $to, 'name' => 'Sigfrid Barrera']
                ],
                'subject' => $subject
            ]
        ],
        'from' => [
            'email' => 'remrem0415@gmail.com',
            'name' => 'Sigfrid Portfolio'
        ],
        'reply_to' => [
            'email' => $email,
            'name' => $name
        ],
        'content' => [
            [
                'type' => 'text/plain',
                'value' => $emailBodyText
            ],
            [
                'type' => 'text/html',
                'value' => $emailBodyHtml
            ]
        ]
    ]);

    $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new Exception($curlError);
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully! I\'ll get back to you soon.'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send message. SendGrid returned HTTP ' . $httpCode . ': ' . $response,
            'debug' => $response
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Error: ' . $e->getMessage(),
        'debug' => $e->getMessage()
    ]);
}
