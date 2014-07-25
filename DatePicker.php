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
		if(! isset($this->clientOptions['onSelect'])) {
			$this->clientOptions['onSelect'] = "function(){
				$('#$id').trigger('change');
			}";
		}

		$onSelect = $this->clientOptions['onSelect'];

		if(isset($this->clientOptions['onShow'])) {
			$onShow =  $this->clientOptions['onSelect'];
		}

		if(isset($this->clientOptions['onHide'])) {
			$onShow =  $this->clientOptions['onHide'];
		}

		unset($this->clientOptions['onHide'], $this->clientOptions['onShow'], $this->clientOptions['onSelect']);

		$options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);
		
		if(isset($onShow) || isset($onHide) || isset($onSelect)) {
			$options = substr($options, 0, -1);

			if(isset($onShow)) {
				$options .= ', "onShow" : ' . $onShow;
			}

			if(isset($onHide)) {
				$options .= ', "onHide" : ' . $onHide;
			}

			if(isset($onSelect)) {
				$options .= ', "onSelect" : ' . $onSelect;
			}
			$options .= '}';
		}

		$js = "jQuery('#$id').$name($options);";
		
		$this->getView()->registerJs($js);
	}
}