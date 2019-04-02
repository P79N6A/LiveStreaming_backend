<?php

class RSA
{
    private $_priContent;
    private $_pubContent;

    private $_privKey;
    private $_pubKey;

    private $_algo;
    private $_psw;

    public function __construct($conf = array('pfx' => '', 'cer' => '', 'pws' => ''))
    {
        $this->_priContent = $conf['pfx'];
        $this->_pubContent = $conf['cer'];
        $this->_algo = OPENSSL_ALGO_SHA1;
        $this->_psw = $conf['pws'];
    }

    public function __destruct()
    {
        @fclose($this->_privKey);
        @fclose($this->_pubKey);
    }

    public function setupPrivKey()
    {
        if (is_resource($this->_privKey)) {
            return true;
        }

        $this->_privKey = openssl_pkey_get_private($this->_priContent);
        return true;
    }

    public function setupPubKey()
    {
        if (is_resource($this->_pubKey)) {
            return true;
        }

        $this->_pubKey = openssl_pkey_get_public($this->_pubContent);
        return true;
    }

    public function pubEncrypt($data)
    {
        if (!is_string($data)) {
            return null;
        }

        $this->setupPubKey();

        $r = openssl_public_encrypt($data, $encrypted, $this->_pubKey);
        if ($r) {
            return base64_encode($encrypted);
        }
        return null;
    }

    public function sign($data)
    {
        $digest = $data . $this->_psw;
        openssl_sign($digest, $signature, $this->_priContent, $this->_algo);
        return base64_encode($signature);
    }

    public function privDecrypt($encrypted)
    {
        if (!is_string($encrypted)) {
            return null;
        }

        $this->setupPrivKey();

        $encrypted = base64_decode($encrypted);

        $r = openssl_private_decrypt($encrypted, $decrypted, $this->_privKey);
        if ($r) {
            return $decrypted;
        }
        return null;
    }

    public function verify($data, $signature)
    {
        $digest = $data . $this->_psw;
        return openssl_verify($digest, base64_decode($signature), $this->_pubContent, $this->_algo);
    }

    public function privEncrypt($data)
    {
        if (!is_string($data)) {
            return null;
        }

        $this->setupPrivKey();

        $r = openssl_private_encrypt($data, $encrypted, $this->_privKey);
        if ($r) {
            return base64_encode($encrypted);
        }
        return null;
    }

    public function pubDecrypt($crypted)
    {
        if (!is_string($crypted)) {
            return null;
        }

        $this->setupPubKey();

        $crypted = base64_decode($crypted);

        $r = openssl_public_decrypt($crypted, $decrypted, $this->_pubKey);
        if ($r) {
            return $decrypted;
        }
        return null;
    }

}
