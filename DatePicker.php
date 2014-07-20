<?php

namespace jDate;

use yii\helpers\Json;
use yii\base\Model;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
/**
 * Jalali date & time.
 * @author Mohammad Mahdi Gholomian.
 * @copyright 2014 mm.gholamian@yahoo.com
 */
class DatePicker extends \yii\base\Widget
{
	/**
	 * @var Model the data model that this widget is associated with.
	 */
	public $model;
	/**
	 * @var string the model attribute that this widget is associated with.
	 */
	public $attribute;
	/**
	 * @var string the input name. This must be set if [[model]] and [[attribute]] are not set.
	 */
	public $name;
	/**
	 * @var string the input value.
	 */
	public $value;
	/**
	 * @var array Input options.
	 */
	public $options = [];
	/**
	 * @var array Date picker options.
	 */
	public $clientOptions = ['formatDate' => "YYYY/0M/0D"];

	public function init()
	{
		parent::init();
		if (!isset($this->options['id'])) {
			$this->options['id'] = $this->getId();
		}
		if (!$this->hasModel() && $this->name === null) {
			throw new InvalidConfigException("Either 'name', or 'model' and 'attribute' properties must be specified.");
		}
		if ($this->hasModel() && !isset($this->options['id'])) {
			$this->options['id'] = Html::getInputId($this->model, $this->attribute);
		}
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
	/**
	 * @return boolean whether this widget is associated with a data model.
	 */
	protected function hasModel()
	{
		return $this->model instanceof Model && $this->attribute !== null;
	}
}