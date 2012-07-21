<?php // Example (of 256-bit AES encryption in CTR mode):

include 'aes.php';
include 'ctr.php';

// See the "README" for information about choosing an appropriate key
$key = 'V<||5DK7KdRYyBLEtY^w~0yk1"#NCZ|N';
$cipher = new CTR(new AES($key));

// Encrypt/Decrypt:
$text = 'Secret message';
$code = $cipher->encrypt($text);
$text = $cipher->decrypt($code);

// The $code variable contains the encrypted raw binary data. You can convert raw binary to a safe ASCII string using base64_encode:
echo "ciphertext: ", base64_encode($code), "<br>";
echo "plaintext: $text";