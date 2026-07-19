<?php
/**
 * San Angelo Water Damage Pros — contact form handler
 * Emails form submissions to the owner and redirects to /thank-you/.
 */

// ----- Configuration -----
$owner_email = "contact@sanangelowaterdamagepros.com";
$site_name   = "San Angelo Water Damage Pros";
$redirect_to = "thank-you/";

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact/");
    exit;
}

// ----- Honeypot: real users never fill the "website" field -----
if (!empty($_POST["website"])) {
    // Likely a bot. Pretend success without sending.
    header("Location: " . $redirect_to);
    exit;
}

// ----- Collect & sanitize -----
function clean($value) {
    return trim(str_replace(array("\r", "\n", "%0a", "%0d"), " ", (string) $value));
}

$name    = clean($_POST["name"]    ?? "");
$phone   = clean($_POST["phone"]   ?? "");
$email   = clean($_POST["email"]   ?? "");
$service = clean($_POST["service"] ?? "Not specified");
$message = trim($_POST["message"]  ?? "");

// ----- Basic validation -----
$errors = array();
if ($name === "")  { $errors[] = "name"; }
if ($phone === "") { $errors[] = "phone"; }
if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "email"; }

if (!empty($errors)) {
    // Send them back to the contact page to try again.
    header("Location: contact/?error=1");
    exit;
}

// ----- Build the email -----
$subject = "New Water Damage Lead: " . $service . " — " . $name;

$body  = "You have a new lead from the {$site_name} website.\n\n";
$body .= "Name:    {$name}\n";
$body .= "Phone:   {$phone}\n";
$body .= "Email:   {$email}\n";
$body .= "Service: {$service}\n";
$body .= "Message:\n" . (htmlspecialchars_decode(strip_tags($message)) ?: "(none)") . "\n\n";
$body .= "Submitted: " . date("Y-m-d H:i:s") . "\n";
$body .= "IP: " . ($_SERVER["REMOTE_ADDR"] ?? "unknown") . "\n";

$headers  = "From: {$site_name} <no-reply@sanangelowaterdamagepros.com>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ----- Send -----
@mail($owner_email, $subject, $body, $headers);

// ----- Redirect to thank-you page -----
header("Location: " . $redirect_to);
exit;
