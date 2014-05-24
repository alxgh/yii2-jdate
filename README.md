Jalali Date
===========
Jalali date & time is an extension for yii2.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist mohammad-mahdy/yii2-jdate "*"
```

or add

```
"mohammad-mahdy/yii2-jdate": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, add it as a component  :

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