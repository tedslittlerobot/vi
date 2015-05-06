<?php namespace Vi\Display\Table;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Stringy\StaticStringy as Stringy;

// @todo add actions for rows
// @todo add header and footer section

class TabularPresenter implements Renderable {

	/**
	 * The key used to specify ascending sorting
	 *
	 * @var string
	 */
	public static $sortAscending = 'ascending';

	/**
	 * The key used to specify descending sorting
	 *
	 * @var string
	 */
	public static $sortDescending = 'descending';

	/**
	 * The view factory instance
	 *
	 * @var \Illuminate\Contracts\View\Factory
	 */
	protected $viewFactory;

	/**
	 * The Url Generator instance
	 *
	 * @var \Illuminate\Contracts\Routing\UrlGenerator
	 */
	protected $url;

	/**
	 * The items to loop over
	 *
	 * @var mixed
	 */
	protected $items = [];

	/**
	 * The columns to loop over
	 *
	 * @var array
	 */
	protected $columns = [];

	/**
	 * The sortable columns
	 *
	 * @var array
	 */
	protected $sortableColumns = [];

	/**
	 * The view to render the table with
	 *
	 * @var string
	 */
	protected $view = 'vi::support.table';

	public function __construct( Factory $view, UrlGenerator $url )
	{
		$this->viewFactory = $view;
		$this->url = $url;
	}

	// ! Getters & Setters

	/**
	 * Set the presenter's items
	 *
	 * @param mixed $items
	 */
	public function setItems( $items )
	{
		$this->items = $items;
		return $this;
	}

	/**
	 * Get the items to show
	 *
	 * @return mixed
	 */
	public function items()
	{
		return $this->items;
	}

	/**
	 * Set the columns to display
	 *
	 * @param mixed $items
	 */
	public function setColumns( $columns )
	{
		$this->columns = $columns;
		return $this;
	}

	/**
	 * Set the columns to display
	 *
	 * @param mixed $items
	 */
	public function columns()
	{
		return $this->columns;
	}

	/**
	 * Set the columns to display
	 *
	 * @param mixed $items
	 */
	public function setSortable( $sortable )
	{
		$this->sortable = $sortable;
		return $this;
	}

	// ! Public Methods

	/**
	 * Render the table
	 *
	 * @return string
	 */
	public function render()
	{
		return $this->viewFactory->make($this->view)
			->with( 'presenter', $this )
			->render();
	}

	// ! Helpers

	/**
	 * Generate a link to sort the content by the given column
	 *
	 * @todo  - test for default sorting
	 * @todo  - test query makes sense - ie currently column => direction. maybe
	 * sort => column and direction => direction?
	 *
	 * @param  string $column
	 * @return string
	 */
	public function sortingLink( $column )
	{
		$direction = $this->url->getRequest()->query->get($column) == static::$sortDescending ?
			static::$sortAscending : static::$sortDescending;

		return $this->url->current() . '?' . http_build_query([$column => $direction]);
	}

	/**
	 * Get the class for the sortable direction
	 *
	 * @param  string $column
	 * @return string
	 */
	public function sortDirectionClass( $column )
	{
		$direction = $this->url->getRequest()->query->get($column);

		return in_array($direction, [static::$sortAscending, static::$sortDescending]) ?
			$direction : null;
	}

	/**
	 * Determine if the given column is sortable
	 *
	 * @param  string  $column
	 * @return boolean
	 */
	public function isSortable( $column )
	{
		return in_array($this->sortableColumns, $column)
	}

	/**
	 * Get the header text for the given field.
	 *
	 * It will look for defined methods in the format `formatColumnHeader()`,
	 * where `Column` is the studly name of the column.
	 *
	 * If no custom methods are used, it will convert the column to a human
	 * readable string.
	 *
	 * @param  string $column
	 * @return string
	 */
	public function formatHeader( $column )
	{
		$method = 'format' . studly_case($column) . 'Header';

		return method_exists($this, $method)
			$this->{$method}() :
			ucwords( Stringy::humanize( snake_case($column) ) );
	}

	/**
	 * Get the field value for an item.
	 *
	 * It will look for defined methods in the format
	 * `formatColumnField($item)`, where `Column` is the studly name of the
	 * column.
	 *
	 * If there is a custom method for the field, that will be returned,
	 * unescaped. By default, it will attempt to get the property off the model,
	 * and escape it.
	 *
	 * @param  mixed  $item
	 * @param  string $column
	 * @return string
	 */
	public function formatField( $item, $column )
	{
		$method = 'format' . studly_case($column) . 'Value';

		return method_exists($this, $method) ?
			$this->{$method}($item) :
			$this->escape( $item->{$column} );
	}

	/**
	 * Escape the given content
	 *
	 * @param  string $value
	 * @return string
	 */
	protected function escape( $value )
	{
		return e( $value );
	}

}
