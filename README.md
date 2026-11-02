# Yii2 Jalali Date

Jalali date conversion, date and time helpers, and a Persian date picker widget for Yii 2. The PHP utilities and browser date picker use the same 33-year calendar conversion so dates, leap years, and month boundaries agree across the package.

Maintained by [Hossein Dehnavi](mailto:hossein.dehnavy@gmail.com). This repository is a fork of the original Yii2 Jalali date package.

## Requirements

- PHP 7.4 or newer
- Yii 2.0
- jQuery (provided by Yii's `JqueryAsset`)

## Installation

Install the package from Packagist with Composer:

```bash
composer require dehnavi/yii2-jdate
```

Or add the package to `composer.json`:

```json
{
  "require": {
    "dehnavi/yii2-jdate": "^1.0"
  }
}
```

## Publish a release

Push this repository to `https://github.com/dehnavi/yii2-jdate`, create a version tag (for example `1.0.0`), then register that GitHub repository in the Packagist account for `dehnavi`. Packagist uses the Composer `name` above as the package identifier. Confirm that `dehnavi/yii2-jdate` is available to your Packagist account before submitting it; if it is already claimed, choose a different package name and update both `composer.json` and the asset bundle path in `src/DatePickerAsset.php`.

The package preserves the existing `jDate` PHP namespace.

## Date and time helpers

Register `jDate\DateTime` as an application component:

```php
// config/web.php
'components' => [
    'jdate' => [
        'class' => jDate\DateTime::class,
    ],
],
```

Then format dates or use the month and conversion helpers:

```php
echo Yii::$app->jdate->date('Y/m/d');

$days = Yii::$app->jdate->getDaysInJalaliMonth(1403, 12); // 30
$first = Yii::$app->jdate->getJalaliMonthStartDate(1403, 12); // 1403/12/01
$last = Yii::$app->jdate->getJalaliMonthEndDate(1403, 12); // 1403/12/30
$range = Yii::$app->jdate->getJalaliMonthRange(1404, 12);

$gregorian = Yii::$app->jdate->toGregorianDate(1403, 12, 30); // [2025, 3, 20]
$jalali = Yii::$app->jdate->toJalaliDate(2025, 3, 20); // [1403, 12, 30]
```

`getDaysInJalaliMonth($year, $month)` returns 31 for months 1–6, 30 for months 7–11, and 29 or 30 for Esfand according to the package calendar. Invalid year/month values throw `yii\base\InvalidParamException`.

### JalaliDateTime value object

Parse a Jalali date and time into an object with public date fields, localized names, Gregorian fields, timezone, and Unix timestamp:

```php
$date = Yii::$app->jdate->parse('1403/12/30 14:25:10');

echo $date->year;             // 1403
echo $date->month;            // 12
echo $date->day;              // 30
echo $date->hour;             // 14
echo $date->minute;           // 25
echo $date->second;           // 10
echo $date->dayOfWeekName;    // نام روز هفته
echo $date->gregorianDate;    // 2025-03-20
echo $date->timestamp;        // Unix timestamp

$date->startOfDay()->setTime(9, 30)->endOfDay();
$array = $date->toArray();
```

The default timezone is `Asia/Tehran`. Pass a timezone name or `DateTimeZone` as the second argument to `parse()` or the constructor to use another timezone. `dayOfWeek` is indexed Saturday `0` through Friday `6`. Direct construction is also supported:

```php
$date = new jDate\JalaliDateTime('1403/12/30 14:25:10');
$same = jDate\JalaliDateTime::parse('۱۴۰۳/۱۲/۳۰ ۱۴:۲۵:۱۰');
```

The value object validates its Jalali date and time before creating the Gregorian date and timestamp. `getStartOfDay()` and `getEndOfDay()` return value objects at `00:00:00` and `23:59:59`; `getDayStartTime()` and `getDayEndTime()` return those time strings.

## Date picker widget

The widget registers its JavaScript and selected theme automatically:

```php
use jDate\DatePicker;

echo DatePicker::widget([
    'name' => 'appointmentDate',
    'value' => '1403/12/30',
    'theme' => 'default', // default | dark | custom
    'clientOptions' => [
        'formatDate' => 'YYYY/0M/0D',
        'persianNumbers' => true,
    ],
]);
```

It also works with ActiveForm:

```php
<?= $form->field($model, 'appointmentDate')->widget(DatePicker::class, [
    'theme' => 'dark',
]) ?>
```

### Range, limits, and disabled dates

Date limits and disabled dates use `YYYY/MM/DD` Jalali strings. In range mode, the input contains both endpoints separated by `rangeSeparator`. The widget rejects an end date earlier than its start and does not allow a range to cross disabled dates.

```php
echo DatePicker::widget([
    'name' => 'period',
    'clientOptions' => [
        'range' => true,
        'rangeSeparator' => ' تا ',
        'minDate' => '1403/01/01',
        'maxDate' => '1403/12/30',
        'disablePastDates' => true,
        'disabledDates' => ['1403/01/13'],
        'disabledWeekdays' => [6], // 0 = Saturday, 6 = Friday
        'weekStartsOn' => 0,
    ],
]);
```

### Reminders

Mark dates with a symbol and color. Selecting a marked date displays its description in the picker; the description is also available as a tooltip.

```php
'clientOptions' => [
    'reminders' => [
        [
            'date' => '1403/01/01',
            'symbol' => '●',
            'color' => '#e74c3c',
            'description' => 'تعطیلی نوروز',
        ],
    ],
],
```

### Themes and customization

The built-in `default` and `dark` themes support the picker features, including ranges, disabled days, and reminders. Set `theme` to another value to load the JavaScript without a theme stylesheet, then register your own stylesheet. The picker supports `cellWidth`, `cellHeight`, `fontSize`, `selectableYears`, `selectableMonths`, `isRTL`, and `calendarPosition` in `clientOptions`.

Use `onShow`, `onHide`, and `onSelect` for JavaScript callbacks. Pass callback expressions as strings because Yii serializes the remaining client options as JSON:

```php
'clientOptions' => [
    'onSelect' => 'function() { console.log(this); }',
],
```

The default `onSelect` handler triggers the input's `change` event. If you provide your own handler and depend on ActiveForm validation, trigger `change` from that handler as well.

The JavaScript plugin exposes the current instance and month length:

```javascript
var picker = $('#period').persianDatepicker(true);
var days = picker.getMonthLength(1403, 12); // 30
```

## Package layout

```text
src/       Yii widget, assets, converter, and JalaliDateTime
assets/    Picker JavaScript and light/dark stylesheets
```

## License

This fork preserves the original PHP package's BSD 3-Clause license and the bundled date picker's upstream MIT license. Original contributions made for this fork by Hossein Dehnavi are released under MIT; see `LICENSE-MIT`. Existing copyright notices and third-party licenses remain in place. The top-level `LICENSE` applies to the inherited PHP code; `assets/LICENSE` applies to the bundled upstream picker.
