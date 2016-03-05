<?php

/*
 * The MIT License
 *
 * Copyright 2014 johnj.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
    namespace sqrlexample;
    require_once(__DIR__.'/../vendor/autoload.php');
    require_once(__DIR__.'/../includes/ExampleStatefulStorage.php');
    session_start();

    // First we need to check whether the user is already logged in
    if (isset($_SESSION['publicKey'])) {
        header('Location: /account.php', true, 303);
    }

    $configuration = new \Trianglman\Sqrl\SqrlConfiguration();
    $configuration->load(__DIR__.'/../config/sqrlconfig.json');
    $store = new ExampleStatefulStorage(new \PDO('mysql:host=localhost;dbname=sqrl', 'example', 'bar'),$_SERVER['REMOTE_ADDR'],$_SESSION);
    $generator = new \Trianglman\Sqrl\SqrlGenerate($configuration, $store);
    $sqrlUrl = $generator->getUrl();

    // Store the nonce in the session
    $_SESSION['nonce'] = $generator->getNonce();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>SQRL Example Server</title>

        <!-- This embedded javascript handles the background authentication check -->
        <script type="text/javascript">
            (function () {
                /**
                 * Asynchronously checks whether the session nonce has been validated.
                 * If it has, then the browser is redirected to the account page.
                 */
                var checkIfNonceIsValidated = function () {
                    var httpRequest = new XMLHttpRequest();
                    if (!httpRequest) {
                        // If we can't create an XMLHttpRequest object then we just give up
                        return;
                    }

                    httpRequest.onreadystatechange = function() {
                        // Check that the request has completed successfully
                        if (httpRequest.readyState !== XMLHttpRequest.DONE || httpRequest.status !== 200) {
                            return;
                        }

                        // If the user is authenticated, redirect to the account page
                        if (httpRequest.responseText === '1') {
                            window.location = '/account.php';
                        }
                    };

                    httpRequest.open('GET', 'https://sqrldemo.barnabycolby.io/login/isNonceValidatedAjaxCheck.php', true);
                    httpRequest.send(null);
                };

                // We check whether the user has been authenticated on a timer, so that they are logged in without having to do anything
                window.setInterval(checkIfNonceIsValidated, 3000);
            }());
        </script>
    </head>
    <body>
        <h1>Welcome to the SQRL PHP Example Server</h1>
        
        <p>
            Please use the below link/QR code to sign in and either create a new account or view your already entered account information.
        </p>
        <a href="<?php echo $sqrlUrl;?>">
            <img src="sqrlImg.php" title="Click or scan to log in" alt="SQRL QR Code" />
        </a>

        <p>
            <a href="login/isNonceValidated.php">Click here once the QR has been scanned</a>
        </p>
    </body>
</html>
