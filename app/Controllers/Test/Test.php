<?php namespace App\Controllers;

class _Home extends BaseController
{

	public function index()
	{
		$data = $this->data;
		return redirect()->to('/test/test');
	}


}
