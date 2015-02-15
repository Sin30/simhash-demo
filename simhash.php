<?php

class simhash{

	const HASHBITS = 128;
	
	private $hash = NULL;

	public function __construct($text){
		$this->hash = $this->calcHash($text);
	}

	public function getHash(){
		return $this->hash;
	}

	public function __toString(){
		return $this->hash;
	}

	protected function tokenize($text){
		return explode(" ", $text);
	}

	protected function calcHash($text){
		$vector = array_fill(0, self::HASHBITS, 0);
		$tokens = $this->tokenize($text);
		foreach($tokens as $token){
			$token_hex = md5($token);
			$token_bin = '';
			foreach(range(0, strlen($token_hex)-1) as $i){
				$token_bin .= sprintf('%04s', decbin(hexdec($token_hex[$i])));
			}
			foreach(range(0, self::HASHBITS-1) as $i){
				if($token_bin[$i] == '1'){
					$vector[$i]++;
				}else{
					$vector[$i]--;
				}
			}
		}
		$fingerprint = str_pad('', self::HASHBITS, '0');
		foreach(range(0, self::HASHBITS-1) as $i){
			if($vector[$i] >= 0){
				$fingerprint[$i] = '1';
			}
		}
		return $fingerprint;
	}
}

function hamming_distance($hash1, $hash2){
	$hash1_bin = $hash1->getHash();
	$hash2_bin = $hash2->getHash();
	$distance = 0;
	foreach(range(0, strlen($hash2_bin)-1) as $i){
		if($hash1_bin[$i] !== $hash2_bin[$i]){
			$distance++;
		}
	}
	return $distance;
}

function similarity($hash1, $hash2){
	$hash1_bin = $hash1->getHash();
	$hash2_bin = $hash2->getHash();
	$hash1_dec = bindec($hash1_bin);
	$hash2_dec = bindec($hash2_bin);
	if($hash1_dec > $hash2_dec){
		return $hash2_dec / $hash1_dec;
	}else{
		return $hash1_dec / $hash2_dec;
	}
}

$hash1 = new simhash("This is a test string for testing");
$hash2 = new simhash("This is a test string for testing also!");
echo "Hamming Distance: ", hamming_distance($hash1, $hash2), "\n";
echo "Similarity: ", round(similarity($hash1, $hash2),4) * 100, "%\n";
