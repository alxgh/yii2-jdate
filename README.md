CYCLOPS Jalali Date (Yii2 Extension)
===========
CYCLOPS Jalali date & time is an extension for yii2. It's a fork of [Jalali date & time](https://github.com/mohammad-mahdy/yii2-jdate) presented by [Mohammad Mahdi Gholamian](https://github.com/mohammad-mahdy)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

add
```
"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/cyclops24/yii2-jdate"
        }
    ],
```
to your `composer.json` root section and then

```
"cyclops24/yii2-jdate": "*"
```

to the require section.


Usage of `DateTime`
===================

Once the extension is installed, add it as a component in your config file:

```php
'jdate' => [
	'class' => 'jDate\DateTime'
]
```

Now you can use in your project:

```php
Yii::$app->jdate->date('Y-m-d');
```

You can access dates and times as variable :

```php
Yii::$app->jdate->Y;
```

Usage of `DatePicker`
=====================

It is a widget to make a input, box to giver jalali date from user.

A basic usage:

```php
<?= jDate\DatePicker::widget([
	'name' => 'datepicker'
]) ?>
```

If you want set default date set `value`:

```php
<?= jDate\DatePicker::widget([
	'name' => 'datepicker', 'value' => '1394/01/01'
]) ?>
```

Using a `model`:

```php
<?= jDate\DatePicker::widget([
	'model' => $model, 'attribute' => 'date'
]) ?>
```

Use in `active form`:

```php
<?= $form->field($model, 'fieldname')->widget(jDate\DatePicker::className()) ?>
```

###Datepicker `ClientOptions`###

> Add these to `ClientOptions` var.

####Change date picker size:

```php
[
	'cellHeight' => 13,
	'cellWidth'  => 13
]
```

####Change font size:

```php
[
	'fontSize' => 25
]
```

####Use english numbers in date picker:

```php
[
	'persianNumbers' => false
]
```

####Date format:

```php
[
	'formatDate' => 'DD-NM-YYYY hh:m'
]
```

###Datepicker `Theme`###

Now date picker have 2 theme `default` and `dark`.

For set theme set `theme` var.

```php
<?= jDate\DatePicker::widget([
	'model' => $model, 'attribute' => 'date', 'theme' => 'dark'
]) ?>
```

###Datepicker `Events`#####

> **NOTE** : If you want rewriting onSelect event and using `active form` add `options[id]` and add 
> 
> ```javascript
$('#your id').trigger('change');
```
> 
> to your event function.

####`onHide` Event:

```php
<?= jDate\DatePicker::widget([
	'model' => $model, 'attribute' => 'date',
	'ClientOptions' => [
		'onHide' => 'function(){alert("Datepicker is now hidden!")}'
	]
]) ?>
```
####`onSelect` Event:

```php
<?= jDate\DatePicker::widget([
	'model' => $model, 'attribute' => 'date',
	'ClientOptions' => [
		'onSelect' => 'function(){alert("Date selected!")}'
	]
]) ?>
```

####`onShow` Event:

```php
<?= jDate\DatePicker::widget([
	'model' => $model, 'attribute' => 'date',
	'ClientOptions' => [
		'onShow' => 'function(){alert("Hello!")}'
	]
]) ?>
```
