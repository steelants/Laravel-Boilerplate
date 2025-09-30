<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

trait SystemPage
{
	public function __construct()
    {
		parent::__construct();
		$view = config('boilerplate.layouts.system');
		$this->layout = $view ;
	}
}
