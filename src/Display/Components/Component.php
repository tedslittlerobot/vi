<?php namespace Vi\Display\Components;

use Collective\Html\FormBuilder;
use Collective\Html\HtmlBuilder;
use Illuminate\View\Factory as ViewFactory;

class Component {

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

	/**
	 * Display a text input
	 *
	 * @param  string $name
	 * @param  string $value
	 * @param  array  $options
	 * @return string
	 */
	public function text( $name, $value = null, $options = [] )
	{
		return $this->form->text( $name, $value, $options )
	}

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

		return '<div class="errors">' . implode('', $errors) . '</div>';
	}

}
