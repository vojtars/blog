<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Helpers;

use Nette\Utils\DateTime;

class DateHelpers extends BaseHelper
{


    /**
     * @param int $days
     * @return string|null
     */
    public static function days(int $days) : ?string
    {
        if ($days == 0)
            return 'Dnes';
        elseif ($days == 1)
            return $days . ' den';
        elseif ($days >= 2 && $days <= 4)
            return $days . ' dny';
        elseif ($days >= 5)
            return $days . ' dní';
        else
            return NULL;
    }

    /**
     * @param DateTime|\DateTime $dateTime
     * @return string
     */
    public static function dayOfWeek($dateTime) : string
    {
        $dayOfWeek = date('D',$dateTime->getTimestamp());
        $days = [
            'Mon' => 'Pondělí',
            'Tue' => 'Úterý',
            'Wed' => 'Středa',
            'Thu' => 'Čtvrtek',
            'Fri' => 'Pátek',
            'Sat' => 'Sobota',
            'Sun' => 'Neděle',
        ];

        return $days[$dayOfWeek];
    }

    /**
     * @param DateTime $dateTime
     * @param bool $vocative
     * @param bool $showYear
     * @param bool $showDay
     * @return string
     */
    public static function translateMonth(DateTime $dateTime, bool $vocative = FALSE, bool $showYear = TRUE, bool $showDay = TRUE) : string
    {
        $day = date('j',$dateTime->getTimestamp());
        $month = date('n',$dateTime->getTimestamp());
        $year = date('Y',$dateTime->getTimestamp());

        if ($vocative) {
            $months = [
                1 => 'Ledna',
                2 => 'Února',
                3 => 'Března',
                4 => 'Dubna',
                5 => 'Května',
                6 => 'Června',
                7 => 'Července',
                8 => 'Srpna',
                9 => 'Září',
                10 => 'Října',
                11 => 'Listopadu',
                12 => 'Prosince',
            ];
        } else {
            $months = [
                1 => 'Leden',
                2 => 'Únor',
                3 => 'Březen',
                4 => 'Duben',
                5 => 'Květen',
                6 => 'Červen',
                7 => 'Červenec',
                8 => 'Srpen',
                9 => 'Září',
                10 => 'Říjen',
                11 => 'Listopad',
                12 => 'Prosinec',
            ];
        }

        if ($showYear) {
            if ($showDay) {
                return $day.'. '.$months[$month].' '.$year;
            } else {
                return $months[$month].' '.$year;
            }
        } else {
            if ($showDay) {
                return $day.'. '.$months[$month];
            } else {
                return $months[$month];
            }
        }
    }

   
    /**
     * @param int|\DateTime|DateTime $time
     * @return boolean|string 
     */
    public static function timeAgoInWords($time) {
        if (!$time) {
            return FALSE;
        } elseif (is_numeric($time)) {
            $time = (int) $time;
        } elseif ($time instanceof DateTime || $time instanceof \DateTime) {
            $time = $time->getTimestamp();
        } else {
            $time = strtotime($time);
        }

        $delta = time() - $time;

        if ($delta < 0) {
            $delta = round(abs($delta) / 60);
            if ($delta == 0)
                return 'Za pár vteřin';
            if ($delta == 1)
                return 'Za minutu';
            if ($delta < 45)
                return 'za ' . $delta . ' ' . self::plural($delta, 'minuta', 'minuty', 'minut');
            if ($delta < 90)
                return 'za hodinu';
            if ($delta < 1440)
                return 'za ' . round($delta / 60) . ' ' . self::plural(round($delta / 60), 'hodina', 'hodiny', 'hodin');
            if ($delta < 2880)
                return 'zítra';
            if ($delta < 43200)
                return 'za ' . round($delta / 1440) . ' ' . self::plural(round($delta / 1440), 'den', 'dny', 'dní');
            if ($delta < 86400)
                return 'za měsíc';
            if ($delta < 525960)
                return 'za ' . round($delta / 43200) . ' ' . self::plural(round($delta / 43200), 'měsíc', 'měsíce', 'měsíců');
            if ($delta < 1051920)
                return 'za rok';
            return 'za ' . round($delta / 525960) . ' ' . self::plural(round($delta / 525960), 'rok', 'roky', 'let');
        }
        $date = strtotime(date("Y-m-d",$time));
        $today = strtotime(date("Y-m-d",time()));

        $delta = round($delta / 60);
        if ($delta == 0) return 'nyní';
        if ($delta == 1) return 'před minutou';
        if ($delta < 45) return 'před '.$delta.' minutami';
        if ($delta < 90) return 'před hodinou';
        if ($delta < 1440) return 'před '.round($delta / 60).' hodinami';

        $daysDiff = ($today - $date)/86400;
        if ($daysDiff == 1)
            return 'včera '.(new DateTime())->setTimestamp($time)->format('H:m:i');
        if ($daysDiff <= 5)
            return 'před '.$daysDiff.' dny'.' v '.(new DateTime())->setTimestamp($time)->format('H:m:i');

        $year = date("Y",$time);
        $thisYear = date("Y",time());
        $date = new DateTime();
        $date->setTimestamp($time);

        if ($thisYear == $year)
            return (new DateTime())->setTimestamp($time)->format('d.m.Y');
        if ($thisYear != $year)
            return (new DateTime())->setTimestamp($time)->format('d.m.Y');
    }

    /**
     * Plural: three forms, special cases for 1 and 2, 3, 4.
     * (Slavic family: Slovak, Czech)
     * @param  int
     * @return mixed
     */
    private static function plural($n) {
        $args = func_get_args();
        return $args[($n == 1) ? 1 : (($n >= 2 && $n <= 4) ? 2 : 3)];
    }

}