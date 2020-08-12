<?php

// Copyright: 2011 Spencer Mortensen
// License: GPLv2 <http://www.gnu.org/licenses/gpl-2.0.html>

class CTR
{
	public $counter;
	public $cipher;

	public function __construct ($cipher)
	{
		$this->cipher = $cipher;
	}

	public function encrypt ($text)
	{
		return self::new_nonce().self::translate($text);
	}

	public function decrypt ($text)
	{
		self::set_nonce(substr($text, 0, 8));
		return self::translate(substr($text,8));
	}

	private function new_nonce ()
	{
		$t = microtime(true);
		$this->counter = array((int)$t & 0xFFFFFFFF, (($t - (int)$t) * 0x100000000) & 0xFFFFFFFF, 0, 0);
		return pack('NN', $this->counter[0], $this->counter[1]);
	}

	private function set_nonce ($nonce)
	{
		$this->counter = unpack('N*', $nonce);
		$this->counter = array($this->counter[1], $this->counter[2], 0, 0);
	}

	public function inc () { return self::ninc($this->counter[3]) || self::ninc($this->counter[2]) || self::ninc($this->counter[1]); }

	private static function ninc (&$n)
	{
		if ($n == 0xFFFFFFFF)
			return $n = 0;

		++$n;
		return true;
	}

	private function translate ($text)
	{
		ob_start();

		for ($i = 0, $n = strlen($text); $i < $n; $i += 16, self::inc())
		{
			$x = $this->cipher->encrypt($this->counter);
			echo substr($text, $i, 16) ^ pack('N*', $x[0], $x[1], $x[2], $x[3]);
		}

		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}