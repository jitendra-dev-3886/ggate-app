<?php

/**
    Aes encryption
 */
class ProEnDcryptV2
{

	protected $key;
	protected $data;

	/**
	 * Available OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
	 *
	 * @var type $options
	 */
	protected $options = 0;
	/**
	 * 
	 * @param type $data
	 * @param type $this->key
	 * @param type $blockSize
	 * @param type $mode
	 */
	function __construct($data = null, $key = null)
	{
		$this->setData($data);
		$this->setKey($key);
	}
	/**
	 * 
	 * @param type $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
	/**
	 * 
	 * @param type $this->key
	 */
	public function setKey($key)
	{
		$this->key = $key;
	}

	public function encrypt()
	{
		$secretKey = $this->hextobin(md5($this->key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);

		$openMode = openssl_encrypt($this->data, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		$encryptedText = bin2hex($openMode);
		return $encryptedText;
	}

	public function decrypt()
	{
		$secretKey = $this->hextobin(md5($this->key));
		$initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);

		$encryptedText = $this->hextobin($this->data);
		$decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $initVector);
		return $decryptedText;
	}
	/*********** Padding Function *********************
		
		private function pkcs5_pad ($data, $blockSize){
			$pad = $blockSize - (strlen($data) % $blockSize);
			return $data . str_repeat(chr($pad), $pad);
		}
	 */

	//********** Hexadecimal to Binary function for php 4.0 version ********

	private function hextobin($hexString)
	{
		$length = strlen($hexString);
		$binString = "";
		$count = 0;
		while ($count < $length) {
			$subString = substr($hexString, $count, 2);
			$packedString = pack("H*", $subString);
			if ($count == 0) {
				$binString = $packedString;
			} else {
				$binString .= $packedString;
			}
			$count += 2;
		}
		return $binString;
	}
}

/* Sample Code 
     * 

    $inputText = "My text to encrypt";
	$inputKey = "My text to encrypt";
	$blockSize = 256;
	$aes = new AES($inputText, $inputKey, $blockSize);
	$enc = $aes->encrypt();
	$aes->setData($enc);
	$dec=$aes->decrypt();
	echo "After encryption: ".$enc."<br/>";
	echo "After decryption: ".$dec."<br/>";

    */
?>