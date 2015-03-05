<?php namespace Vi\Display\Components;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\View\Factory as ViewFactory;

class ComponentGenerator {

	/**
	 * The error format
	 *
	 * @var string
	 */
	protected $errorFormat = '<div class="error">:message</div>';

	public function __construct( HtmlBuilder $html, FormBuilder $form, ViewFactory $view )
	{
		$this->html = $html;
		$this->form = $form;
		$this->view = $view;
	}

	// ! Form Macros

	/**
	 * Display a text input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array  $options
	 * @return string
	 */
	public function text( $name, $label, $value = null, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->text( $name, $value, $options )
		);
	}

	/**
	 * Display a password field
	 *
	 * @param  string  $name
	 * @param  string  $label
	 * @param  array   $options
	 * @return string
	 */
	public function password( $name, $label, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->password( $name, $options )
			// @todo - password strength meter
		);
	}
	/**
	 * Display an email input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function email( $name, $label, $value = null, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->email( $name, $value, $options )
		);
	}

	/**
	 * Display a number input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function number( $name, $label, $value = null, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->number( $name, $value, $options )
		);
	}

	/**
	 * Display a date input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function date( $name, $label, $value = null, $options = [] )
	{
		// @todo - date formats, js, etc.
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->date( $name, $value, $options )
		);
	}

	/**
	 * Display a url input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function url( $name, $label, $value = null, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->url( $name, $value, $options )
		);
	}

	/**
	 * Display a file input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function file( $name, $label, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->file( $name, $options )
		);
	}

	/**
	 * Display a textarea input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function textarea( $name, $label, $value = null, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->textarea( $name, $value, $options )
		);
	}

	/**
	 * Display a select input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function select( $name, $label, $list, $value = null, $options = [] )
	{
		return $this->concat(
			$this->form->label( $name, $label ),
			$this->errors( $name ),
			$this->form->select( $name, $list, $value, $options )
		);
	}

	/**
	 * Display a range input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function range( $name, $label, $value = null, $options = [] )
	{
		// @todo - range input - js?
	}

	/**
	 * Display a checkbox input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function checkbox( $name, $label, $list, $value = null, $options = [] )
	{
		$output = '';

		// @todo - test checkbox
		foreach( $list as $itemKey => $itemLabel )
		{
			$output .= $this->form->label( $name, $itemLabel );
			$output .= $this->form->checkbox( $name, $itemKey, ($itemKey == $value) );
		}

		return $this->concat(
			"<span>$label</span>",
			$this->errors( $name ),
			$output
		);
	}

	/**
	 * Display a radio input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function radio( $name, $label, $list, $value = null, $options = [] )
	{
		$output = '';

		// @todo - test radio
		foreach( $list as $itemKey => $itemLabel )
		{
			$output .= $this->form->label( $name, $itemLabel );
			$output .= $this->form->radio( $name, $itemKey, ($itemKey == $value) );
		}

		return $this->concat(
			"<span>$label</span>",
			$this->errors( $name ),
			$output
		);
	}

	/**
	 * Display a submit input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function submit( $name, $label, $value = null, $options = [] )
	{
		// @todo - figure out useful button arguments
		return $this->html->button( $label, $options );
	}

	/**
	 * Display a button input
	 *
	 * @param  string $name
	 * @param  string $label
	 * @param  array $options
	 * @return string
	 */
	public function button( $name, $label, $value = null, $options = [] )
	{
		// @todo - figure out useful button arguments
		return $this->html->button( $label, $options );
	}

	// ! Helpers

	/**
	 * Display any errors for the given key
	 *
	 * @param  string $key
	 * @return string
	 */
	protected function errors( $key )
	{
		$errors = $this->view->shared('errors')
			->get( $key, $this->errorFormat );

		if ( empty($errors) )
		{
			return '';
		}

		return '<div class="errors">' . $this->concat($errors) . '</div>';
	}

	/**
	 * Concatenate all arguments
	 *
	 * I know this is lazy, but i wanted an easy common place to change the glue
	 * argument to implode
	 *
	 * @param string $args...
	 * @return string
	 */
	protected function concat($arg)
	{
		return implode( PHP_EOL, is_array($arg) ? $arg : func_get_args() );
	}

}
