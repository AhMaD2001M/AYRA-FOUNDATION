<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];

    $to = 'ahmadaslam2001m@gmail.com'; // Replace with your email address
    $subject = 'New Donation Received';
    $message = "Amount: €$amount\n";
    $message .= "First Name: $firstName\n";
    $message .= "Last Name: $lastName\n";
    $message .= "Email: $email\n";
    $message .= "Phone: $phone\n";
    $message .= "Address: $address\n";
    $message .= "Note: $note\n";

    $headers = "From: $email";

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_type = $_FILES['attachment']['type'];

        $content = file_get_contents($file_tmp);
        $content = chunk_split(base64_encode($content));

        $separator = md5(time());

        $headers .= "\r\nMIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

        $body = "--".$separator."\r\n";
        $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $message."\r\n";

        $body .= "--".$separator."\r\n";
        $body .= "Content-Type: ".$file_type."; name=\"".$file_name."\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment\r\n\r\n";
        $body .= $content."\r\n";
        $body .= "--".$separator."--";

        mail($to, $subject, $body, $headers);
    } else {
        mail($to, $subject, $message, $headers);
    }

    echo 'Donation form submitted successfully.';
} else {
    echo 'Invalid request method.';
}
?>
