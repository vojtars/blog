<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Helpers;

use Nette\Utils\Strings;

class StringHelpers extends BaseHelper
{


	/**
	 * @param string $phoneNumber
	 * @param bool   $withSpaces
	 * @param bool   $withCode
	 * @param bool   $forHref
	 * @return string|null
	 */
    public static function phone(string $phoneNumber, bool $withSpaces = TRUE, bool $withCode = TRUE, bool $forHref = FALSE)
    {
        $phoneNumber = preg_replace('/[^0-9]+/', '', $phoneNumber);
        if (empty($phoneNumber)) {
            return NULL;
        } else {
            $phoneLength = Strings::length($phoneNumber);
            if ($phoneLength <= 14) { // 906112567
                $part3 = substr($phoneNumber, -3);
                $cutPhone1 = substr( $phoneNumber, 0, -3 );
                $part2 = substr($cutPhone1, -3);
                $cutPhone2 = substr( $cutPhone1, 0, -3 );
                $part1 = substr($cutPhone2, -3);
                $cutPhone3 = substr( $cutPhone2, 0, -3 );
                $code = substr($cutPhone3, -3);

                if (Strings::length($code) != 3) {
                    $code = '420';
                }

                if ($forHref) {
	                return '+'.$code.'-'.$part1.'-'.$part2.'-'.$part3;
                } else {
	                if ($withSpaces) {
		                if ($withCode) {
			                return '+'.$code.' '.$part1.' '.$part2.' '.$part3;
		                } else {
			                return $part1.' '.$part2.' '.$part3;
		                }
	                } else {
		                if ($withCode) {
			                return '+'.$code.$part1.$part2.$part3;
		                } else {
			                return $part1.$part2.$part3;
		                }
	                }
                }


            } else {
                return $phoneNumber;
            }
        }
    }
}