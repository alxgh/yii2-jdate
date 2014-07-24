<?php

namespace jDate;

use yii\helpers\Json;
use yii\helpers\Html;
use yii\widgets\InputWidget;
/**
 * Jalali date & time.
 * @author Mohammad Mahdi Gholomian.
 * @copyright 2014 mm.gholamian@yahoo.com
 */
class DatePicker extends InputWidget
{
	/**
	 * @var array Date picker options.
	 */
	public $clientOptions = ['formatDate' => "YYYY/0M/0D"];

	public function init()
	{
		parent::init();
	}
	
	function run()
	{
		$this->registerAsset();

		echo $this->renderInput();

		$this->renderJsCode();
	}
	/**
	 * Register datepicker asset into view.
	 */
	function registerAsset()
	{
		DatePickerAsset::register($this->getView());
	}
	/**
	 * Render input.
	 */
	function renderInput()
	{
		if ($this->hasModel()) {
			return Html::activeTextInput($this->model, $this->attribute, $this->options);
		} else {
			return Html::textInput($this->name, $this->value, $this->options);
		}
	}
	/**
	 * Render Js code.
	 */
	function renderJsCode()
	{
		$name = 'persianDatepicker';
		$id = $this->options['id'];
		$options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
		$js = "jQuery('#$id').$name($options);";
		$this->getView()->registerJs($js);
	}
}