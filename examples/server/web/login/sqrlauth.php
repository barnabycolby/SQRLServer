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

    // Make sure that the content-type is UTF-8
    ini_set('default_charset', 'utf-8');

    // Include dependencies
    require_once(__DIR__.'/../../vendor/autoload.php');
    require_once(__DIR__.'/../../includes/ExampleStatefulStorage.php');

    // Initialise the config, store and generator
    $config = new \Trianglman\Sqrl\SqrlConfiguration();
    $config->load(__DIR__.'/../../config/sqrlconfig.json');
    $store = new ExampleStatefulStorage(new \PDO('mysql:host=localhost;dbname=sqrl', 'example', 'bar'), $_SERVER['REMOTE_ADDR'], $_SESSION);
    $generator = new \Trianglman\Sqrl\SqrlGenerate($config, $store);

    // Initialise the validator
    if (extension_loaded("ellipticCurveSignature")) {
        $cryptoValidator = new \Trianglman\Sqrl\EcEd25519NonceValidator();
    } else {
        $cryptoValidator = new \Trianglman\Sqrl\Ed25519NonceValidator();
    }
    $validator = new \Trianglman\Sqrl\SqrlValidate($config, $cryptoValidator, $store);

    // Initialise the request handler
    $requestHandler = new \Trianglman\Sqrl\SqrlRequestHandler($config, $validator, $store, $generator);
    $requestHandler->parseRequest($_GET, $_POST, $_SERVER);

    // Send the response
    $requestHandler->sendResponse();
?>
