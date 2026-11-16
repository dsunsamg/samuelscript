<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Honeypot check: If the hidden 'website' field is filled, it's an automated bot.
    // Silently drop and redirect so bots believe it succeeded without alerting the spammer.
    if (!empty($_POST['website'])) {
        header("Location: index.php?status=success#contact");
        exit();
    }

    // 2. Submission timing check: Humans take at least 3 seconds to read and fill a form.
    // Submissions faster than 3 seconds or older than 24 hours (86400s) are rejected.
    $form_time = isset($_POST['form_time']) ? (int)$_POST['form_time'] : 0;
    $now = time();
    if ($form_time <= 0 || ($now - $form_time) < 3 || ($now - $form_time) > 86400) {
        header("Location: index.php?status=error#contact");
        exit();
    }

    // 3. Raw input retrieval
    $raw_name    = trim($_POST['name'] ?? '');
    $raw_email   = trim($_POST['email'] ?? '');
    $raw_message = trim($_POST['message'] ?? '');

    // 4. Validate required fields
    if ($raw_name === '' || $raw_email === '' || $raw_message === '') {
        header("Location: index.php?status=error#contact");
        exit();
    }

    // 5. Header injection defense: Strip newlines and carriage returns from name and email
    $clean_name  = preg_replace('/[\r\n]+/', ' ', $raw_name);
    $clean_email = preg_replace('/[\r\n]+/', '', $raw_email);

    // 6. Validate email format
    $email = filter_var($clean_email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?status=error#contact");
        exit();
    }

    // Sanitize for safe output
    $name    = htmlspecialchars($clean_name, ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($raw_message, ENT_QUOTES, 'UTF-8');

    // 7. Combined text check for spam analysis
    $combined_text = $raw_name . ' ' . $raw_message;
    $combined_lc   = strtolower($combined_text);

    $spam_detected = false;

    // A. Cyrillic / Russian script check
    // Block Russian bot blasts (e.g. "Обратный сбор... goo.su")
    if (preg_match('/[\p{Cyrillic}]/u', $combined_text)) {
        $spam_detected = true;
    }

    // B. Strict checks on the NAME field
    // Real names do not contain URLs, domain extensions, or excessive length
    if (!$spam_detected) {
        $name_has_url = preg_match('/(https?:\/\/|www\.|\.su\b|\.ru\b|\.com\b|\.net\b|\.org\b|\.xyz\b|\.top\b|\.link\b|\.click\b|\.site\b|\.online\b|\.info\b|goo\.su|bit\.ly|tinyurl)/i', $raw_name);
        $name_has_chars = preg_match('/[<>\[\]\{\}\/\\\|=]/', $raw_name);
        $name_too_long  = mb_strlen($raw_name) > 70;

        if ($name_has_url || $name_has_chars || $name_too_long) {
            $spam_detected = true;
        }
    }

    // C. URL / Link Shortener / Spam TLD detection across name & message
    if (!$spam_detected) {
        $url_patterns = [
            '/https?:\/\//i',
            '/\bwww\./i',
            '/\b(goo\.su|bit\.ly|tinyurl\.com|t\.co|cutt\.ly|rb\.gy|is\.gd|v\.gd)\b/i',
            '/\.[a-z0-9\-]+\.(su|ru|xyz|top|click|link|work|buzz|icu|fit|gq|cf|ml|ga|tk)\b/i',
            '/\[url[=\]]/i',
            '/<a\s+href/i'
        ];

        foreach ($url_patterns as $pattern) {
            if (preg_match($pattern, $combined_text)) {
                $spam_detected = true;
                break;
            }
        }
    }

    // D. Spam & Solicitation Keywords
    if (!$spam_detected) {
        $spam_keywords = [
            'seo', 'check site', 'run your free', 'click here', 'whatsapp me',
            'promotion', 'backlinks', 'cheap offer', 'boost traffic', 'crypto',
            'loan', 'bitcoin', 'speed-seo', 'dot com', '.com', '.net', '.org',
            'telegram', 'reach me on', 'contact me at', 'whatsapp:', 'status: ready',
            'status: готов', 'возврат', 'руб'
        ];

        foreach ($spam_keywords as $keyword) {
            if (stripos($combined_text, $keyword) !== false) {
                $spam_detected = true;
                break;
            }
        }
    }

    // E. Phone number spam pattern (+2348012345678, etc.) in unsolicited solicitation
    if (!$spam_detected && preg_match('/(\+?\d[\d\-\s\(\)]{8,}\d)/', $raw_message)) {
        // If message also contains solicitation phrases
        $contact_phrases = ['whatsapp', 'call me', 'text me', 'reach me', 'telegram', 'direct message'];
        foreach ($contact_phrases as $phrase) {
            if (stripos($combined_lc, $phrase) !== false) {
                $spam_detected = true;
                break;
            }
        }
    }

    // 8. If spam is detected: Silently drop it without sending ANY emails
    if ($spam_detected) {
        // Redirect as success to trick the bot into thinking it succeeded, avoiding repeated attacks
        header("Location: index.php?status=success#contact");
        exit();
    }

    // 9. Legitimate message - Prepare emails
    $to_owner      = "supports@samuelscript.com";
    $subject_owner = "New Contact Form Submission from $clean_name";
    $body_owner    = "Name: $name\nEmail: $email\n\nMessage:\n$message\n\nIP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown');

    $headers_owner  = "From: \"$clean_name\" <$email>\r\n";
    $headers_owner .= "Reply-To: $email\r\n";
    $headers_owner .= "MIME-Version: 1.0\r\n";
    $headers_owner .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers_owner .= "X-Mailer: PHP/" . phpversion();

    // Alert copy to primary personal email
    $alert_email   = "dsunsamg@gmail.com";
    $subject_alert = "[ALERT] New Contact Form Submission";
    $body_alert    = "A new contact message was submitted on samuelscript.com.\n\n" . $body_owner;
    $headers_alert  = "From: \"SamuelScript Alert\" <noreply@samuelscript.com>\r\n";
    $headers_alert .= "Reply-To: noreply@samuelscript.com\r\n";
    $headers_alert .= "MIME-Version: 1.0\r\n";
    $headers_alert .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Autoresponder to legitimate sender
    $to_user      = $email;
    $subject_user = "Thank You for Reaching Out!";
    $body_user    = "Hi $clean_name,\n\n";
    $body_user   .= "Thank you for contacting me! I've received your message and will get back to you shortly.\n\n";
    $body_user   .= "Best regards,\nSamuel Sunday\nsamuelscript.com";

    $headers_user  = "From: \"Samuel Sunday\" <noreply@samuelscript.com>\r\n";
    $headers_user .= "Reply-To: supports@samuelscript.com\r\n";
    $headers_user .= "MIME-Version: 1.0\r\n";
    $headers_user .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers_user .= "X-Mailer: PHP/" . phpversion();

    // Send messages
    $sent_owner = mail($to_owner, $subject_owner, $body_owner, $headers_owner);
    $sent_user  = mail($to_user, $subject_user, $body_user, $headers_user);
    $sent_alert = mail($alert_email, $subject_alert, $body_alert, $headers_alert);

    if ($sent_owner || $sent_alert) {
        header("Location: index.php?status=success#contact");
    } else {
        header("Location: index.php?status=error#contact");
    }
    exit();
}
?>