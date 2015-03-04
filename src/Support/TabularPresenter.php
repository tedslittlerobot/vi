<?php namespace Vi\Support;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Routing\UrlGenerator;

// @todo add actions for rows
// @todo add header and footer section

class TabularPresenter implements Renderable {

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

	protected $view = 'vi::support.table';

	protected $viewFactory;
	protected $url;

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
	 * @param  string $column
	 * @return string
	 */
	public function sortingLink( $column )
	{
		$direction = $this->url->getRequest()->query->get($column) == 'ascending' ?
			'descending' : 'ascending';

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

		return in_array($direction, ['ascending' : 'descending']) ?
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
	 * Get the header text for the given field
	 *
	 * @param  string $column
	 * @return string
	 */
	public function formatHeader( $column )
	{
		$method = 'format' . studly_case($column) . 'Header';

		return method_exists($this, $method)
			$this->{$method}($column) :
			ucwords($column);
	}

	/**
	 * Get the field value for an item
	 *
	 * @param  mixed  $item
	 * @param  string $column
	 * @return string
	 */
	public function formatField( $item, $column )
	{
		$method = 'format' . studly_case($column) . 'Value';

		return method_exists($this, $method) ?
			$this->{$method}($column) :
			$item->{$column};
	}

}
