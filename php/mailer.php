<!doctype html>
<html lang="en">

<head>
    <title>Forgot Password</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        h1 {
            font-family: "Montserrat";
        }

        h4 {
            font-family: "Arial";
        }
    </style>
</head>

<body>

    <?php
    require './connection.php';
    require "../PHPMailer/PHPMailerAutoload.php";

    session_start();
    date_default_timezone_set('Asia/Kolkata');

    function rand_string($length)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz@#$&*";
        $size = strlen($chars);
        $s = "";
        for ($i = 0; $i < $length; $i++) {
            $str = $chars[rand(0, $size - 1)];
            $s .= $str;
        }
        return $s;
    }
    function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    $invalid = 0;
    $invalid_count = 0;
    function smtpmailer($to, $from, $subject, $body)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';

        $mail->Host = 'MAIL_HOST_NAME';
        $mail->Port = 465; // For Secure Server  
        $mail->Username = 'SMTP_EMAIL_ID';
        $mail->Password = 'PASSWORD';

        $mail->SetFrom($from); //SMTP_EMAIL_ID 
        $mail->AddAddress($to);

        $mail->IsHTML(true);
        $mail->Charset = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->SMTPOptions = array('ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        ));

        if (!$mail->Send()) {
            $error = "Please try Later, Error Occured while Processing...";
            return $error;
        } else {
            $error = "
            <div class='container-fluid jumbotron text-center'>
                <h1><b>Thank You</b></h1>
                <h4>Your request has been registered. Check out your email.</h4><br />
                // <a class='btn btn-success' href='../../login.html'>Click here Login</a>
            </div>
            ";
            return $error;
        }
    }

    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        $query = "SELECT * FROM `register` where `emailid` = '$email'";

        $result = mysqli_query($con, $query);
        if (mysqli_num_rows($result) == 0) {
    ?>
            <script>
                alert("Email doesn\'t exist!");
                window.open('../forgot-password.html', '_self');
            </script>
            <?php
        } else if (mysqli_num_rows($result) == 1) {
            $id = mysqli_fetch_assoc($result)['id'];
            $query = "SELECT * FROM `forgot_password_emails` WHERE id = '$id'";
            $result = mysqli_query($con, $query);
            if (mysqli_num_rows($result) == 0) {
                mysqli_query($con, "INSERT INTO `forgot_password_emails`(`id`, `time`, `count`) VALUES ('$id','" . date('Y-m-d H:i:s') . "','1')");
            } else {
                $b = mysqli_fetch_assoc($result);
                $bdate = $b['time'];
                if (date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($bdate))) < date('Y-m-d H:i:s')) {
                    mysqli_query($con, "UPDATE `forgot_password_emails` SET `time`='" . date('Y-m-d H:i:s') . "',`count`=1 WHERE id='$id'");
                } else {
                    if ($b['count'] >= 3) {
            ?>
                        <div class="container-fluid jumbotron text-center">
                            <h3><b>You already sent 3 requests in an hour.</b></h3>
                            <h4>Please, come back after 1 hour.</h4> <br />
                            <!-- <a class="btn btn-success" href='../../login.html'>Click here Login</a> -->
                        </div>
            <?php
                        exit();
                    } else {
                        mysqli_query($con, "UPDATE `forgot_password_emails` SET `time`='" . date('Y-m-d H:i:s') . "',`count`=count+1 WHERE '$id
              '");
                    }
                }
            }
            $from = 'SMTP_EMAIL_ID';
            $to   = $email;

            $subj = 'Forgot password?';
            $random_string = rand_string(20);
            $modified_email = $random_string . $email . $random_string;
            $email_string = encrypt_decrypt('encrypt', $modified_email);
            $link = "http://groupofprofessional.com/sss/forgot-password/php/user_password_entry.php?email=" . $email_string;
            $msg = "
            <p>
                Hello User,
                <br>
                We have received a request to reset your password for the user account associated with " . $email . " . No
                changes have been made to your account yet.
                <br>
                You can reset your passsword by clicking the below link:<br>
                <a href='" . $link . "'>
                    <button style='border: 0; background: #6db961;
                            padding: 10px; border-radius: 20px;
                            margin-top: 20px; font-weight: bold;
                            color: white; curser:pointer;'>
                        Click Here to Reset Password
                    </button>
                </a>
            </p>
            ";

            $error = smtpmailer($to, $from, $subj, $msg);
            ?>
            <?php echo $error; ?><br>
        <?php
            exit();
        } else {
        ?>
            <script type="text/javascript">
                alert("Something went wrong! Contact administrator");
                window.location.href = '../../login.html';
            </script>
    <?php
        }
    }
    ?>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>