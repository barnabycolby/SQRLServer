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

    require_once(__DIR__.'/../../vendor/autoload.php');
    require_once(__DIR__.'/../../includes/ExampleStatefulStorage.php');

    // We require access to the session state in order to get the nonce value of the session
    session_start();
    
    $store = new ExampleStatefulStorage(new \PDO('mysql:host=localhost;dbname=sqrl', 'example', 'bar'), $_SERVER['REMOTE_ADDR'], $_SESSION);
        
    if (isset($_SESSION['nonce'])) {
        $nonce = $_SESSION['nonce'];
        $isNonceValidated = $store->isNonceValidated($nonce);
        if ($isNonceValidated == \Trianglman\Sqrl\SqrlStoreInterface::NONCE_VERIFIED) {
            //Update the session with a user identifier instead of the nonce
            $_SESSION['publicKey'] = $store->getPublicKeyForOriginalNonce($nonce);
            unset($_SESSION['nonce']);
            unset($_SESSION['generatedTime']);
            header('Location: /account.php',true,303);
        } else if ($isNonceValidated == \Trianglman\Sqrl\SqrlStoreInterface::NONCE_UNKNOWN) {
            goto NONCE_UNKNOWN;
        }
    } else {
        NONCE_UNKNOWN:
        header('Location: /index.php',true,303);//send the user back to the index page to get a new nonce
    }
    
    
?>

<html>
  <head>
    <title>Verifying Login...</title>
    <?php if (isset($_SESSION['nonce'])): ?>
    <META http-equiv="refresh" content="5;URL=/login/isNonceValidated.php">
    <?php endif;?>
  </head>
  <body>
      <p>
          <?php if (isset($_SESSION['nonce'])): ?>
          Your log in has not been validated. This page will refresh in 5 seconds. <a href="/login/isNonceValidated.php">Click here to check again.</a>
          <?php endif;?>
      </p>
  </body>
</html>
