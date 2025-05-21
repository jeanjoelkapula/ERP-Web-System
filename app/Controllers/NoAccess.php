<?php namespace App\Controllers;

class Noaccess extends BaseController
{

	public function index()
	{

		$data = $this->data;

		echo view('no_access', $data);

	}


}
