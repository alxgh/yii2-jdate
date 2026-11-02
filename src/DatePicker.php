<?php

namespace jDate;

use yii\helpers\Json;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\widgets\InputWidget;
/**
 * Jalali date & time.
 * @author Mohammad Mahdi Gholomian.
 * @copyright 2014 mm.gholamian@yahoo.com
 * @author Hossein Dehnavi (fork contributions)
 */
class DatePicker extends InputWidget
{
	/**
	 * @var array Date picker options.
	 */
	public $clientOptions = ['formatDate' => "YYYY/0M/0D"];
	/**
	 * @var string Theme name.
	 *			   Date picker currently have 2 theme : dark, default.
	 */
	public $theme = 'default';
	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		if(! is_string($this->theme)) {
			throw new InvalidConfigException(
				"Theme name must be a string."
			);
		}
	}
	/**
     * Executes the widget.
     */
	function run()
	{
		echo $this->renderInput();

		$this->renderJsCode();
	}
	/**
	 * Register datepicker default theme asset into view.
	 */
	function registerDefaultThemeAsset()
	{
		DatePickerDefaultThemeAsset::register($this->getView());
	}
	/**
	 * Register datepicker dark theme asset into view.
	 */
	function registerDarkThemeAsset()
	{
		DatePickerDarkThemeAsset::register($this->getView());
	}
	/**
	 * Register datepicker assets without css into view.
	 */
	function registerNoThemeAsset()
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
		//Jquery plugin name.
		$name = 'persianDatepicker';

		$id = $this->options['id'];
		// Set theme name in clientOptions.
		$this->clientOptions['theme'] = $this->theme;
		// Build onSelect event.
		$callbacks = [];
		foreach (['onShow', 'onHide', 'onSelect'] as $event) {
			if (isset($this->clientOptions[$event])) {
				$callbacks[$event] = $this->clientOptions[$event];
				unset($this->clientOptions[$event]);
			}
		}
		if (!isset($callbacks['onSelect'])) {
			$callbacks['onSelect'] = 'function(){jQuery(' . Json::encode('#' . $id) . ').trigger("change");}';
		}
		// Register theme assets.
		if(in_array($this->theme, ['default', 'dark'])) {
			$this->{"register" . ucfirst($this->theme) . "ThemeAsset"}();
		} else {
				$this->registerNoThemeAsset();
		}
		$options = Json::encode((object) $this->clientOptions);
		$options = substr($options, 0, -1);
		foreach ($callbacks as $event => $callback) {
			$options .= ', ' . Json::encode($event) . ' : ' . $callback;
		}
		$options .= '}';
		$js = 'jQuery(' . Json::encode('#' . $id) . ').' . $name . '(' . $options . ');';
		// Register js code.
		$this->getView()->registerJs($js);
	}
}
