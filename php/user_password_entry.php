<!doctype html>
<html lang="en">

<head>
    <title>Reset Password | School Of Finding Origin and End</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        * {
            font-family: "Montserrat";
        }

        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .form-container {
            padding: 3rem;
        }

        .container i {
            margin-left: -30px;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <?php

    function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    $email  = encrypt_decrypt('decrypt', $_GET['email']);
    $email = substr($email, 20, -20);

    ?>


    <div class="container" style="padding-bottom: 8rem;">
        <form class="form-container" action="./user_password_update.php?email=<?php echo $email; ?>" method="POST" onsubmit="return validateForm();">
            <h3 class="form-heading mb-3">Reset Password</h3>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="row text-center mr-3">
                            <input style="font-family: Arial;" type="text" class="form-control" name="pswd" id="pswd" placeholder="New Password" autocomplete="off" required>
                            <i class="fa fa-eye" id="togglePassword"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="row  text-center mr-3">
                            <input style="font-family: Arial;" type="password" class="form-control" name="cpswd" id="cpswd" placeholder="Confirm Password" autocomplete="off" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="row mr-3">
                        <button style="font-family: Arial;" type="submit" class="btn btn-success" id="submit" name="submit">Submit</button>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>

        </form>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <script>
        $('#submit').click(function validateForm() {

            if ($('#pswd').val().trim() !== $('#cpswd').val().trim()) {
                alert("Password not matched!");
                return false;
            } else {
                return true;
            }
        });

        $('#togglePassword').click(function() {
            const type = $('#pswd').attr('type') === 'password' ? 'text' : 'password';
            $('#pswd').attr('type', type);
            $('#togglePassword').toggleClass('fa-eye-slash');
        });
    </script>
</body>

</html>