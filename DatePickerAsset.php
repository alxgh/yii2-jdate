<?php

namespace jDate;

use yii\web\AssetBundle;

/**
 * @author Mohammad Mahdi Gholomian.
 * @copyright 2014 mm.gholamian@yahoo.com
 */
class DatePickerAsset extends AssetBundle
{
	public $sourcePath = '@vendor/cyclops24/yii2-jdate/assets';
	public $js = [
		'js/persianDatepicker.min.js',
	];
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
