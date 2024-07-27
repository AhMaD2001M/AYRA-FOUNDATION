<?php

$email_to = "ahmadaslam2001m@gmail.com"; 
$email_from = "ahmadaslam2003m@gmail.com"; // Ensure this is a valid email address on your server
$email_subject = "Donation Form Submitted";

if(isset($_POST['email'])) {

    function return_error($error) {
        echo json_encode(array('success'=>0, 'message'=>$error));
        die();
    }

    // Check for empty required fields
    if (!isset($_POST['firstName']) ||
        !isset($_POST['lastName']) ||
        !isset($_POST['email']) ||
        !isset($_POST['amount'])) {
        return_error('Please fill in all required fields.');
    }

    // Form field values
    $firstName = $_POST['firstName']; // required
    $lastName = $_POST['lastName']; // required
    $email = $_POST['email']; // required
    $amount = $_POST['amount']; // required
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $note = isset($_POST['note']) ? $_POST['note'] : '';

    // Form validation
    $error_message = "";

    // Name validation
    $name_exp = "/^[a-z0-9 .\-]+$/i";
    if (!preg_match($name_exp, $firstName) || !preg_match($name_exp, $lastName)) {
        $this_error = 'Please enter a valid name.';
        $error_message .= ($error_message == "") ? $this_error : "<br/>".$this_error;
    }        

    // Email validation
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp, $email)) {
        $this_error = 'Please enter a valid email address.';
        $error_message .= ($error_message == "") ? $this_error : "<br/>".$this_error;
    }

    // If there are validation errors
    if(strlen($error_message) > 0) {
        return_error($error_message);
    }

    // Prepare email message
    $email_message = "Form details below.\n\n";
    
    function clean_string($string) {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $email_message .= "First Name: ".clean_string($firstName)."\n";
    $email_message .= "Last Name: ".clean_string($lastName)."\n";
    $email_message .= "Email: ".clean_string($email)."\n";
    $email_message .= "Amount: €".clean_string($amount)."\n";
    $email_message .= "Phone: ".clean_string($phone)."\n";
    $email_message .= "Address: ".clean_string($address)."\n";
    $email_message .= "Note: ".clean_string($note)."\n";

    // Handle file attachment
    $attachment = isset($_FILES['attachment']) ? $_FILES['attachment'] : null;
    if ($attachment && $attachment['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $attachment['tmp_name'];
        $file_name = $attachment['name'];
        $file_size = $attachment['size'];
        $file_type = $attachment['type'];

        $content = file_get_contents($file_tmp);
        $content = chunk_split(base64_encode($content));

        $separator = md5(time());

        $headers = "From: ".$email_from."\r\nReply-To: ".$email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

        $body = "--".$separator."\r\n";
        $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $email_message."\r\n";

        $body .= "--".$separator."\r\n";
        $body .= "Content-Type: ".$file_type."; name=\"".$file_name."\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "Content-Disposition: attachment\r\n\r\n";
        $body .= $content."\r\n";
        $body .= "--".$separator."--";

        if (@mail($email_to, $email_subject, $body, $headers)) {
            echo json_encode(array('success'=>1, 'message'=>'Form submitted successfully.'));
        } else {
            return_error('An error occurred while sending the email. Please try again later.');
        }
    } else {
        // Create email headers
        $headers = 'From: '.$email_from."\r\n".
        'Reply-To: '.$email."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        
        if (@mail($email_to, $email_subject, $email_message, $headers)) {
            echo json_encode(array('success'=>1, 'message'=>'Form submitted successfully.'));
        } else {
            return_error('An error occurred while sending the email. Please try again later.');
        }
    }
} else {
    return_error('Please fill in all required fields.');
}
?>
