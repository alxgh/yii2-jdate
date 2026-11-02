<?php

namespace jDate;

use yii\base\InvalidParamException;
/**
 * Jalali date & time.
 * @author Mohammad Mahdi Gholomian.
 * @copyright 2014 mm.gholamian@yahoo.com
 * @author Hossein Dehnavi (fork contributions)
 */
class DateTime
{
	function date($dateFormat, $timeStamp = false)
	{
		if(! is_string($dateFormat))
		{
			throw new InvalidParamException(
				"Date format is inavlid! " .
				"This must be a string."
			);
		}

		$dateTime = new \DateTime('@' . ($timeStamp ?: time()),  new \DateTimeZone('Asia/Tehran'));
		$dateTime->setTimeZone(new \DateTimeZone('Asia/Tehran'));
		list($jalaliYear, $jalaliMonth, $jalaliDay) = $this->toJalaliDate(
			$year	= $dateTime->format('Y'),
			$month 	= $dateTime->format('m'),
			$day 	= $dateTime->format('d'));
		
		$res = '';
		$skipNextCahr = false;
		for ($i = 0; $i < strlen($dateFormat); $i++) {
			$char = $dateFormat[$i];
			if ($skipNextCahr) {
				$skipNextCahr = false;
				$res .= $char;
				continue;
			}
			switch ($char) {
				case 'A' :
						if($dateTime->format('a') == 'pm') {
							$res .= 'بعد از ظهر';
						} else {
							$res .= 'قبل از ظهر';
						}
					break;
				case 'a' :
						if($dateTime->format('a') == 'pm') {
							$res .= 'ب.ظ';
						} else {
							$res .= 'ق.ظ';
						}
					break;
				case 'd':
					if ($jalaliDay < 10) {
						$res .= '0' . $jalaliDay;
					} else {
						$res .= $jalaliDay;
					}
					break;
				case 'D':
					$res .= $this->getDayShortName($dateTime->format('D'));
					break;
				case 'F':
					$res .= $this->getMonthName($jalaliMonth);
					break;
				case 'g':
					$res .= $dateTime->format('g');
					break;
				case 'G':
					$res .= $dateTime->format('G');
					break;
				case 'h':
					$res .= $dateTime->format('h');
					break;
				case 'H':
					$res .= $dateTime->format('H');
					break;
				case 'i':
					$res .= $dateTime->format('i');
					break;
				case 'j':
					$res .= $jalaliDay;
					break;
				case 'l':
					$res .= $this->getDayName($dateTime->format('l'));
					break;
				case 'm':
					if ($jalaliMonth < 10) {
						$res .= '0' . $jalaliMonth;
					} else {
						$res .= $jalaliMonth;
					}
					break;
				case 'M':
					$res .= $this->getShortMonthName($jalaliMonth);
					break;
				case 'n':
					$res .= $jalaliMonth;
					break;
				case 's':
					$res .= $dateTime->format('s');
					break;
				case 'S':
					$res .= 'م';
					break;
				case 't':
					$res .= $this->daysInMonth($month, $day, $year);
					break;
				case 'w':
					$res .= $dateTime->format('w');
					break;
				case "y":
					$res .= substr($jalaliYear, 2);
					break;
				case 'o':
				case "Y":
					$res .= $jalaliYear;
					break;
				case "\\" :
					$skipNextCahr = true;
					break;
				default:
					$res .= $char;
					break;
			}
		}

		return $res;
	}

	function __get($var)
	{
		return $this->date($var);
	}

	/** Parse a Jalali date/time string into a JalaliDateTime value object. */
	function parse($jalaliDate, $timezone = null)
	{
		return new JalaliDateTime($jalaliDate, $timezone);
	}

	/** Return a date value set to 00:00:00. */
	function getStartOfDay($jalaliDate, $timezone = null)
	{
		return $this->parse($jalaliDate, $timezone)->startOfDay();
	}

	/** Return a date value set to 23:59:59. */
	function getEndOfDay($jalaliDate, $timezone = null)
	{
		return $this->parse($jalaliDate, $timezone)->endOfDay();
	}

	function getDayStartTime()
	{
		return '00:00:00';
	}

	function getDayEndTime()
	{
		return '23:59:59';
	}

	function toJalaliDate($g_y, $g_m, $g_d) 
	{

		$g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
		$j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);      
		$gy = $g_y-1600; 
		$gm = $g_m-1; 
		$gd = $g_d-1; 
		$g_day_no = 365*$gy+$this->div($gy+3,4)-$this->div($gy+99,100)+$this->div($gy+399,400); 
		for ($i=0; $i < $gm; ++$i) 
			$g_day_no += $g_days_in_month[$i]; 

		if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))) 
			/* leap and after Feb */ 
			$g_day_no++; 
		$g_day_no += $gd; 
		$j_day_no = $g_day_no-79; 
		$j_np = $this->div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */ 
		$j_day_no = $j_day_no % 12053; 
		$jy = 979+33*$j_np+4*$this->div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */ 
		$j_day_no %= 1461; 
		if ($j_day_no >= 366) { 

		  $jy += $this->div($j_day_no-1, 365); 

		  $j_day_no = ($j_day_no-1)%365; 

		} 
		for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) 
			$j_day_no -= $j_days_in_month[$i]; 

		$jm = $i+1;
		$jd = $j_day_no+1; 
		return array($jy, $jm, $jd); 

	}

	function div($a,$b) {
		return (int) ($a / $b);
	}

	/**
	 * Return the number of days in a Jalali month.
	 */
	function getDaysInJalaliMonth($year, $month)
	{
		$this->validateJalaliYearMonth($year, $month);
		$start = $this->toGregorianDate((int) $year, (int) $month, 1);
		$nextYear = (int) $year;
		$nextMonth = (int) $month + 1;
		if ($nextMonth > 12) {
			$nextMonth = 1;
			$nextYear++;
		}
		$next = $this->toGregorianDate($nextYear, $nextMonth, 1);
		$startTimestamp = gmmktime(0, 0, 0, $start[1], $start[2], $start[0]);
		$nextTimestamp = gmmktime(0, 0, 0, $next[1], $next[2], $next[0]);
		return (int) (($nextTimestamp - $startTimestamp) / 86400);
	}

	/** Convert a Jalali date to a Gregorian [year, month, day] tuple. */
	function toGregorianDate($year, $month, $day)
	{
		$this->validateJalaliYearMonth($year, $month);
		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;
		if ($day < 1 || $day > 31) {
			throw new InvalidParamException('Jalali day is invalid.');
		}

		$yearBase = $year - 474;
		$cycle = (int) floor($yearBase / 2820);
		$cycleRemainder = (($yearBase % 2820) + 2820) % 2820;
		$cycleYear = 474 + $cycleRemainder;
		$monthOffset = $month <= 7 ? ($month - 1) * 31 : (($month - 1) * 30 + 6);
		$julianDay = $day + $monthOffset
			+ (int) floor(($cycleYear * 682 - 110) / 2816)
			+ ($cycleYear - 1) * 365
			+ $cycle * 1029983
			+ 1948320;

		$l = $julianDay + 68569;
		$n = (int) floor((4 * $l) / 146097);
		$l -= (int) floor((146097 * $n + 3) / 4);
		$i = (int) floor((4000 * ($l + 1)) / 1461001);
		$l = $l - (int) floor((1461 * $i) / 4) + 31;
		$j = (int) floor((80 * $l) / 2447);
		$gregorianDay = $l - (int) floor((2447 * $j) / 80);
		$l = (int) floor($j / 11);
		$gregorianMonth = $j + 2 - 12 * $l;
		$gregorianYear = 100 * ($n - 49) + $i + $l;
		$candidate = new \DateTime(sprintf('%04d-%02d-%02d', $gregorianYear, $gregorianMonth, $gregorianDay), new \DateTimeZone('UTC'));
		$target = [$year, $month, $day];
		$low = -512;
		$high = 512;
		while ($low <= $high) {
			$offset = (int) floor(($low + $high) / 2);
			$check = clone $candidate;
			if ($offset !== 0) {
				$check->modify(($offset > 0 ? '+' : '') . $offset . ' days');
			}
			$checkYear = (int) $check->format('Y');
			$checkMonth = (int) $check->format('n');
			$checkDay = (int) $check->format('j');
			$converted = $this->toJalaliDate($checkYear, $checkMonth, $checkDay);
			if ($converted === $target) {
				return [$checkYear, $checkMonth, $checkDay];
			}
			if ($converted < $target) {
				$low = $offset + 1;
			} else {
				$high = $offset - 1;
			}
		}
		throw new InvalidParamException('Jalali date is not supported by the calendar converter.');
	}

	/** Return the first date of a Jalali month as YYYY/MM/DD. */
	function getJalaliMonthStartDate($year, $month)
	{
		$this->validateJalaliYearMonth($year, $month);
		return sprintf('%04d/%02d/01', $year, $month);
	}

	/** Return the last date of a Jalali month as YYYY/MM/DD. */
	function getJalaliMonthEndDate($year, $month)
	{
		return sprintf('%04d/%02d/%02d', $year, $month, $this->getDaysInJalaliMonth($year, $month));
	}

	/** Return the first and last dates of a Jalali month. */
	function getJalaliMonthRange($year, $month)
	{
		return [
			'start' => $this->getJalaliMonthStartDate($year, $month),
			'end' => $this->getJalaliMonthEndDate($year, $month),
		];
	}

	private function validateJalaliYearMonth($year, $month)
	{
		if (!is_numeric($year) || (int) $year < 1 || (int) $year != $year ||
			!is_numeric($month) || (int) $month < 1 || (int) $month > 12 || (int) $month != $month) {
			throw new InvalidParamException('Jalali year or month is invalid.');
		}
	}

	function getDayNames()
	{
		return [
			'saturday' => 'شنبه',
			'sunday' => 'یکشنبه',
			'monday' => 'دوشنبه',
			'tuesday' => 'سه شنبه',
			'wednesday' => 'چهارشنبه',
			'thursday' => 'پنجشنبه',
			'friday' => 'جمعه',
			
		];
	}
	function getDayShortNames()
	{
		return [
			'sat' => 'ش',
			'sun' => 'ی',
			'mon' => 'د',
			'tue' => 'س',
			'wed' => 'چ',
			'thu' => 'پ',
			'fri' => 'ج',
		];
	}

	function getDayName($day)
	{
		$day = strtolower($day);
		return isset($this->getDayNames()[$day]) ? $this->getDayNames()[$day] : false;
	}
	function getDayShortName($day)
	{
		$day = strtolower($day);
		return isset($this->getDayShortNames()[$day]) ? $this->getDayShortNames()[$day] : false;
	}
	function getMonthNames()
	{
		return [
			1 => 'فروردین',
			2 => 'اردیبهشت',
			3 => 'خرداد',
			4 => 'تیر',
			5 => 'مرداد',
			6 => 'شهریور',
			7 => 'مهر',
			8 => 'آبان',
			9 => 'آذر',
			10 => 'دی',
			11 => 'بهمن',
			12 => 'اسفند',
		];
	}

	function getShortMonthNames()
	{
		return [
			1 => 'فرو',
			2 => 'ارد',
			3 => 'خرد',
			4 => 'تیر',
			5 => 'مرد',
			6 => 'شهر',
			7 => 'مهر',
			8 => 'آبا',
			9 => 'آذر',
			10 => 'دی',
			11 => 'بهم',
			12 => 'اسف',
		];
	}

	function getMonthName($month)
	{
		if ($month < 1 || $month > 12) {
			throw new InvalidParamException(
				"Invalid month number. "
			);
		}

		$month = (int) $month;
		return $this->getMonthNames()[$month];
	}

	function getShortMonthName($month)
	{
		if ($month < 1 || $month > 12) {
			throw new InvalidParamException(
				"Invalid month number. "
			);
		}

		$month = (int) $month;
		return $this->getMonthNames()[$month];
	}
	/**
	 * Find numbers in month.
	 */
	function daysInMonth($month,$day,$year)
	{
		$jday2  = "";
		$jdate2 = "";
		$lastdayen = date("d",mktime(0,0,0,$month+1,0,$year));
		list($jyear, $jmonth, $jday) = $this->toJalaliDate($year, $month, $day);
		$lastdatep=$jday;
		$jday=$jday2;
		while ($jday2!="1") {
			if ($day<$lastdayen) {
				$day++;
				list($jyear, $jmonth, $jday2) = $this->toJalaliDate($year, $month, $day);
				if($jdate2=="1") break;
				if($jdate2!="1") $lastdatep++;
			} else {
				$day=0;
				$month++;
				if ($month==13) {
					$month="1";
					$year++;
				}
			}

		}
		return $lastdatep-1;
	}
}
