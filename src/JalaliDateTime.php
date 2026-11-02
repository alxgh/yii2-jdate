<?php

namespace jDate;

use yii\base\InvalidParamException;

/**
 * A Jalali date and time with its Gregorian equivalent and Unix timestamp.
 * @author Hossein Dehnavi (fork contribution)
 */
class JalaliDateTime
{
	public $year;
	public $month;
	public $day;
	public $hour;
	public $minute;
	public $second;
	public $dayOfWeek;
	public $dayOfWeekName;
	public $monthName;
	public $jalaliDate;
	public $gregorianYear;
	public $gregorianMonth;
	public $gregorianDay;
	public $gregorianDate;
	public $gregorianDateTime;
	public $timestamp;
	public $timezone;

	private $dateTime;
	private $dateConverter;

	/**
	 * @param string $jalaliDate Date as YYYY/MM/DD, optionally followed by HH:MM[:SS].
	 * @param string|\DateTimeZone|null $timezone Defaults to Asia/Tehran.
	 */
	public function __construct($jalaliDate, $timezone = null)
	{
		if (!is_string($jalaliDate)) {
			throw new InvalidParamException('Jalali date must be a string.');
		}

		$jalaliDate = strtr($jalaliDate, [
			'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
			'۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
			'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
			'٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
		]);
		$pattern = '/^\s*(\d{1,4})[\/-](\d{1,2})[\/-](\d{1,2})(?:[ T](\d{1,2}):(\d{1,2})(?::(\d{1,2}))?)?\s*$/';
		if (!preg_match($pattern, $jalaliDate, $matches)) {
			throw new InvalidParamException('Jalali date must use YYYY/MM/DD [HH:MM:SS] format.');
		}

		$this->year = (int) $matches[1];
		$this->month = (int) $matches[2];
		$this->day = (int) $matches[3];
		$this->hour = isset($matches[4]) ? (int) $matches[4] : 0;
		$this->minute = isset($matches[5]) ? (int) $matches[5] : 0;
		$this->second = isset($matches[6]) ? (int) $matches[6] : 0;

		$this->dateConverter = new DateTime();
		$monthLength = $this->dateConverter->getDaysInJalaliMonth($this->year, $this->month);
		if ($this->day < 1 || $this->day > $monthLength || $this->hour > 23 || $this->minute > 59 || $this->second > 59) {
			throw new InvalidParamException('Jalali date or time is out of range.');
		}

		if ($timezone === null) {
			$timezone = new \DateTimeZone('Asia/Tehran');
		} elseif (is_string($timezone)) {
			$timezone = new \DateTimeZone($timezone);
		} elseif (!$timezone instanceof \DateTimeZone) {
			throw new InvalidParamException('Timezone must be a timezone name or DateTimeZone instance.');
		}
		$this->timezone = $timezone->getName();

		list($gregorianYear, $gregorianMonth, $gregorianDay) = $this->jalaliToGregorian(
			$this->year,
			$this->month,
			$this->day
		);
		$dateString = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $gregorianYear, $gregorianMonth, $gregorianDay, $this->hour, $this->minute, $this->second);
		$this->dateTime = new \DateTime($dateString, $timezone);
		$this->refreshProperties();
	}

	/** Create an instance from a Jalali date/time string. */
	public static function parse($jalaliDate, $timezone = null)
	{
		return new self($jalaliDate, $timezone);
	}

	/** Move this instance to the first second of its day. */
	public function startOfDay()
	{
		$this->dateTime->setTime(0, 0, 0);
		$this->refreshProperties();
		return $this;
	}

	/** Set the Jalali date while keeping the current time. */
	public function setDate($year, $month, $day)
	{
		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;
		$monthLength = $this->dateConverter->getDaysInJalaliMonth($year, $month);
		if ($day < 1 || $day > $monthLength) {
			throw new InvalidParamException('Jalali date is out of range.');
		}
		list($gregorianYear, $gregorianMonth, $gregorianDay) = $this->jalaliToGregorian($year, $month, $day);
		$dateString = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $gregorianYear, $gregorianMonth, $gregorianDay, $this->hour, $this->minute, $this->second);
		$this->dateTime = new \DateTime($dateString, $this->dateTime->getTimezone());
		$this->refreshProperties();
		return $this;
	}

	/** Move this instance to the last second of its day. */
	public function endOfDay()
	{
		$this->dateTime->setTime(23, 59, 59);
		$this->refreshProperties();
		return $this;
	}

	/** Set the time and return this instance for chaining. */
	public function setTime($hour, $minute, $second = 0)
	{
		$hour = (int) $hour;
		$minute = (int) $minute;
		$second = (int) $second;
		if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59 || $second < 0 || $second > 59) {
			throw new InvalidParamException('Time is out of range.');
		}
		$this->dateTime->setTime($hour, $minute, $second);
		$this->refreshProperties();
		return $this;
	}

	/** Return the Unix timestamp. */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/** Return the Jalali date and time in a readable string. */
	public function toJalaliString($includeTime = true)
	{
		$date = $this->jalaliDate;
		return $includeTime ? $date . sprintf(' %02d:%02d:%02d', $this->hour, $this->minute, $this->second) : $date;
	}

	/** Return the Gregorian date and time in a readable string. */
	public function toGregorianString($includeTime = true)
	{
		return $includeTime ? $this->gregorianDateTime : $this->gregorianDate;
	}

	/** Return the exposed date and time fields as an array. */
	public function toArray()
	{
		return [
			'year' => $this->year,
			'month' => $this->month,
			'day' => $this->day,
			'hour' => $this->hour,
			'minute' => $this->minute,
			'second' => $this->second,
			'dayOfWeek' => $this->dayOfWeek,
			'dayOfWeekName' => $this->dayOfWeekName,
			'monthName' => $this->monthName,
			'jalaliDate' => $this->jalaliDate,
			'gregorianYear' => $this->gregorianYear,
			'gregorianMonth' => $this->gregorianMonth,
			'gregorianDay' => $this->gregorianDay,
			'gregorianDate' => $this->gregorianDate,
			'gregorianDateTime' => $this->gregorianDateTime,
			'timestamp' => $this->timestamp,
			'timezone' => $this->timezone,
		];
	}

	public function __toString()
	{
		return $this->toJalaliString();
	}

	private function refreshProperties()
	{
		$this->gregorianYear = (int) $this->dateTime->format('Y');
		$this->gregorianMonth = (int) $this->dateTime->format('n');
		$this->gregorianDay = (int) $this->dateTime->format('j');
		$this->gregorianDate = $this->dateTime->format('Y-m-d');
		$this->gregorianDateTime = $this->dateTime->format('Y-m-d H:i:s');
		$this->timestamp = $this->dateTime->getTimestamp();

		list($this->year, $this->month, $this->day) = $this->dateConverter->toJalaliDate(
			$this->gregorianYear,
			$this->gregorianMonth,
			$this->gregorianDay
		);
		$this->hour = (int) $this->dateTime->format('G');
		$this->minute = (int) $this->dateTime->format('i');
		$this->second = (int) $this->dateTime->format('s');
		$this->jalaliDate = sprintf('%04d/%02d/%02d', $this->year, $this->month, $this->day);
		$this->dayOfWeek = (((int) $this->dateTime->format('w')) + 1) % 7;
		$this->dayOfWeekName = $this->dateConverter->getDayName($this->dateTime->format('l'));
		$this->monthName = $this->dateConverter->getMonthName($this->month);
		$this->timezone = $this->dateTime->getTimezone()->getName();
	}

	private function jalaliToGregorian($year, $month, $day)
	{
		return $this->dateConverter->toGregorianDate($year, $month, $day);
	}
}
