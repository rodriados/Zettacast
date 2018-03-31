<?php
/**
 * Zettacast\Uri\Punycode class file.
 * @package Zettacast
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2015-2018 Rodrigo Siqueira
 */
namespace Zettacast\Uri;

class Punycode
{
	/**
	 * Defines the delimiter to be used to separate the basic part.
	 * @var string The punycode delimiter.
	 */
	const DELIMITER = '-';
	
	/**
	 * Defines the constant values of the bootstring parameters.
	 * @var int[] The bootstring parameters.
	 */
	private static $params = [
		'n' => 128,
		'bias' => 72,
		'base' => 36,
		'tmin' => 1,
		'tmax' => 26,
		'skew' => 38,
		'damp' => 700,
		'maxint' => 0x7FFFFFFF,
	];
	
	/**
	 * Converts a string of Unicode symbols to a string of ASCII-only symbols.
	 * @param string $input The string of Unicode symbols.
	 * @return string The resulting Punycode string of ASCII-only symbols.
	 * @throws PunycodeException The input needs wider integers to process.
	 */
	final public static function encode(string $input): string
	{
		$input  = self::ucs2decode($input);
		$inputl = count($input);
		$output = [];
		
		/**
		 * @var int $n
		 * @var int $bias
		 * @var int $base
		 * @var int $tmin
		 * @var int $tmax
		 * @var int $maxint
		 */
		extract(self::$params);
		$delta = 0;
		
		foreach($input as $code)
			if($code < 0x80)
				$output[] = chr($code);
		
		if($hdled = $basicl = count($output))
			$output[] = self::DELIMITER;
		
		while($hdled < $inputl) {
			for($m = $maxint, $i = 0; $i < $inputl; ++$i)
				if($input[$i] >= $n && $input[$i] < $m)
					$m = $input[$i];

			if($m - $n > floor(($maxint - $delta) / ($hdled + 1)))
				throw PunycodeException::overflow();
			
			$delta += ($m - $n) * ($hdled + 1);
			$n = $m;
			
			foreach($input as $code) {
				if($code < $n && ++$delta > $maxint)
					throw PunycodeException::overflow();
				
				if($code == $n) {
					for($q = $delta, $k = $base; ; $k += $base) {
						$t = $k > $bias
							? ($k >= $bias + $tmax ? $tmax : $k - $bias)
							: $tmin;
						
						if($q < $t)
							break;
						
						$output[] =
							chr(self::tobasic($t + ($q - $t) % ($base - $t)));
						$q = floor(($q - $t) / ($base - $t));
					}
					
					$output[] = chr(self::tobasic($q));
					$bias = self::adapt($delta, $hdled + 1, $hdled == $basicl);
					$delta = 0;
					++$hdled;
				}
			}
			
			++$delta; ++$n;
		}
		
		return implode('', $output);
	}
	
	/**
	 * Converts a string of ASCII symbols to a string of Unicode symbols.
	 * @param string $input The string of ASCII symbols.
	 * @return string The resulting string of Unicode symbols.
	 * @throws PunycodeException The input cannot be decoded.
	 */
	final public static function decode(string $input): string
	{
		$inputl = strlen($input);
		$output = [];
		
		/**
		 * @var int $n
		 * @var int $bias
		 * @var int $base
		 * @var int $tmin
		 * @var int $tmax
		 * @var int $maxint
		 */
		extract(self::$params);
		$basic = (int)strrpos($input, self::DELIMITER);
		
		for($i = 0; $i < $basic; ++$i)
			if($input[$i] >= 0x80)
				throw PunycodeException::notbasic();
			else
				$output[] = $input[$i];
			
		for($i = $basic ? $basic + 1 : 0, $j = 0; $i < $inputl; ) {
			for($oldj = $j, $w = 1, $k = $base; ; $k += $base) {
				if($i >= $inputl)
					throw PunycodeException::invalid();
				
				$digit = self::todigit(ord($input[$i++]));
				
				if($digit >= $base || $digit > floor(($maxint - $j) / $w))
					throw PunycodeException::overflow();
				
				$j += $digit * $w;
				$t = $k > $bias
					? ($k >= $bias + $tmax ? $tmax : $k - $bias)
					: $tmin;
				
				if($digit < $t)
					break;
				
				if($w > floor($maxint / ($base - $t)))
					throw PunycodeException::overflow();
				
				$w *= $base - $t;
			}
			
			$outl = count($output) + 1;
			$bias = self::adapt($j - $oldj, $outl, $oldj == 0);
			
			if(floor($j / $outl) > $maxint - $n)
				throw PunycodeException::overflow();
			
			$n += floor($j / $outl);
			$j %= $outl;
			array_splice($output, $j++, 0, $n);
		}
		
		return self::ucs2encode($output);
	}
	
	/**
	 * Calculates the new bias value.
	 * @param int $delta The initial delta for new bias value.
	 * @param int $points Current size of output.
	 * @param bool $first Is this the first encoded character?
	 * @return int The new bias value.
	 */
	final private static function adapt(int $delta, int $points, bool $first)
		: int
	{
		/**
		 * @var int $base
		 * @var int $tmin
		 * @var int $tmax
		 * @var int $skew
		 * @var int $damp
		 */
		extract(self::$params);
		$delta = $first ? floor($delta / $damp) : $delta >> 1;
		$delta += floor($delta / $points);
		
		for($k = 0; $delta > ((($base - $tmin) * $tmax) >> 1); $k += $base)
			$delta = floor($delta / ($base - $tmin));
		
		return floor($k + ($base - $tmin + 1) * $delta / ($delta + $skew));
	}
	
	/**
	 * Converts a digit/integer to a basic code point.
	 * @param int $digit The numeric value of a basic code point.
	 * @return int The basic code point representation.
	 */
	final private static function tobasic(int $digit): int
	{
		return $digit + 22 + 75 * ($digit < 26);
	}
	
	/**
	 * Converts a basic code point to a digit/integer.
	 * @param int $basic The basic numeric code point value.
	 * @return int The numeric value of a basic code point.
	 */
	final private static function todigit(int $basic): int
	{
		if($basic - 0x30 < 0x0a)
			return $basic - 0x16;
		
		if($basic - 0x41 < 0x1a)
			return $basic - 0x41;
		
		if($basic - 0x61 < 0x1a)
			return $basic - 0x61;
		
		return self::$params['base'];
	}
	
	/**
	 * Transforms the string into an array of codepoints.
	 * @param string $input The string to be transformed.
	 * @return array The codepoints list.
	 * @throws PunycodeException Input is too big to process.
	 */
	final private static function ucs2decode(string $input): array
	{
		$length = strlen($input);
		$output = [];
		
		for($i = 0; $i < $length; ++$i) {
			$code = ord($input[$i]);
			
			if(($code & 0x80) == 0x00) {
				$bytes = 1;
			} elseif(($code & 0xe0) == 0xc0) {
				$bytes = 2;     $code &= 0x1f;
			} elseif(($code & 0xf0) == 0xe0) {
				$bytes = 3;     $code &= 0x0f;
			} elseif(($code & 0xf8) == 0xf0) {
				$bytes = 4;     $code &= 0x07;
			} else {
				throw PunycodeException::overflow();
			}
			
			while(--$bytes)
				$code = ($code << 6) | (ord($input[++$i]) & 0x3f);
			
			$output[] = $code;
		}
		
		return $output;
	}
	
	/**
	 * Transforms the array of codepoints into a string.
	 * @param int[] $input The input to form the string.
	 * @return string The resulting encoded string.
	 * @throws PunycodeException Input is too big to process.
	 */
	final private static function ucs2encode(array $input): string
	{
		$length = count($input);
		$output = [];
		
		for($i = $length - 1; $i >= 0; --$i) {
			if(is_string($input[$i])) {
				$output[] = $input[$i];
				continue;
			}
			
			for($j = 0; $input[$i] > 0x80; ++$j) {
				$output[] = chr(($input[$i] & 0x3f) | 0x80);
				$input[$i] = $input[$i] >> 6;
			}
			
			if    ($j == 0) $output[] = chr($input[$i] & 0x7f);
			elseif($j == 1) $output[] = chr($input[$i] & 0x1f | 0xc0);
			elseif($j == 2) $output[] = chr($input[$i] & 0x0f | 0xe0);
			elseif($j == 3) $output[] = chr($input[$i] & 0x07 | 0xf0);
		}
		
		return implode('', array_reverse($output));
	}
}
