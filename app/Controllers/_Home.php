<?php namespace App\Controllers;

class _Home extends BaseController
{

	public function index()
	{
		
		$data = $this->data;

        echo view('/test/test');
		//return redirect()->to('test/view'); // dashboard URL here - or welcome page

	}


}
