<?php

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
	
	public function renderDefault()
	{
		// test of database
		var_dump( get_declared_classes() );
        die();
	}

}
