<?php
/*
 * The MIT License (MIT)
 * 
 * Copyright (c) 2013 John Judy
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
namespace Trianglman\Sqrl;

/**
 * An object to handle storing and retrieving SQRL data
 *
 * @author johnj
 */
interface SqrlStoreInterface
{
    
    const IDENTITY_ACTIVE = 1;
    
    const IDENTITY_UNKNOWN = 2;
    
    const IDENTITY_LOCKED = 3;

    // Possible return values for the isNonceValidated function
    const NONCE_UNKNOWN = 4;
    const NONCE_VERIFIED = 5;
    const NONCE_NOT_VERIFIED = 6;

    /**
     * Stores a nonce and the related information
     *
     * @param string $nonce  The nonce to store
     * @param int $action The tif related to the nonce
     * @param string $key [Optional] The identity key related to the nonce
     * @param string $previousNonce [Optional] The previous nonce related to the nonce
     *
     * @return void
     */
    public function storeNonce($nonce, $action, $key='', $previousNonce='');

    /**
     * Retrieves information about the supplied nut
     *
     * @param string $nut    The nonce to retrieve information on
     *
     * @return array:
     *      'tif'=> int The tif stored with the nut (0 for first request nuts)
     *      'originalKey'=> string The key associated with the nut, if any
     *      'originalNut'=> string The nut that came before this one in the transaction, if any
     *      'createdDate'=> \DateTime The time the nut was created
     *      'nutIP'=> string the IP address that requested the nut
     *      'sessionId'=> string the session ID for the nut [this is only required in stateless nuts]
     */
    public function getNutDetails($nut);

    /**
     * Checks the status of an identity key
     * 
     * @param string $key
     * 
     * @return int One of the class key status constants
     */
    public function checkIdentityKey($key);
    
    /**
     * Activates a session
     * 
     * @param string $requestNut The nut of the current request that is being logged in
     * 
     * @return void
     */
    public function logSessionIn($requestNut);
    
    /**
     * Stores a new identity key along with the Identity Lock information
     * 
     * @param string $key
     * @param string $suk
     * @param string $vuk
     * 
     * @return void
     */
    public function createIdentity($key,$suk,$vuk);
    
    /**
     * Flags a session as no longer valid.
     * 
     * This should either immediatly destroy the session, or mark the session
     * in such a way that it will be destroyed the next time it is accessed.
     * 
     * @param string $requestNut The nut of the curret request related to the session
     *      to be destroyed
     * 
     * @return void
     */
    public function endSession($requestNut);
    
    /**
     * Locks an authentication key against further use until a successful unlock
     *
     * @param string $key The authentication key to lock
     *
     * @return void
     */
    public function lockIdentityKey($key);
    
    /**
     * Unlocks an authentication key allowing future authentication
     *
     * @param string $key The authentication key to lock
     *
     * @return void
     */
    public function unlockIdentityKey($key);
    
    /**
     * Gets an identity's SUK value in order for the client to use the Identity Unlock protocol
     * 
     * @param string $key The identity key
     * 
     * @return string The SUK value
     */
    public function getIdentitySUK($key);
    
    /**
     * Gets an identity's VUK value in order for the client to use the Identity Unlock protocol
     * 
     * @param string $key The identity key
     * 
     * @return string The VUK value
     */
    public function getIdentityVUK($key);
    
    /**
     * Updates a user's key information after an identity update action
     *
     * @param string $oldKey The key getting new information
     * @param string $newKey The authentication key replacing the old key
     * @param string $newSuk The replacement SUK
     * @param string $newVuk The replacement VUK
     *
     * @return void
     */
    public function updateIdentityKey($oldKey, $newKey, $newSuk, $newVuk);
    
    /**
     * Gets the current active nonce for the user's session if there is any
     * 
     * @return string
     */
    public function getSessionNonce();

    /**
     * Checks whether a nonce is validated i.e. whether the corresponding public key has been logged in
     *
     * @param string $nonce The nonce to check.
     */
    public function isNonceValidated($nonce);

    /**
     * Gets the public key associated with the given original nonce.
     * This nonce may be the first in a chain of nonces, so the public key may not be found at the start of the chain.
     *
     * @param string $originalNonce  The nonce that starts the transaction chain.
     */
    public function getPublicKeyForOriginalNonce($originalNonce);

    /**
     * Retrieves the account information for an account, identified by the public key. This information contains the SUK and VUK.
     *
     * @param string $publicKey  The public key that identities the account.
     */
    public function retrieveAuthenticationRecord($publicKey);
}
