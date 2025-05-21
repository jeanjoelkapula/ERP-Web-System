<?php namespace App\Controllers;


class Login extends \CodeIgniter\Controller {

	protected $ionAuth;

	public function __construct()
	{

		$this->ionAuth = new \IonAuth\Libraries\IonAuth();

	}


    public function index()
    {
        if ($this->ionAuth->loggedIn())
        {
            return redirect()->to('/');
        }
        else
        {
            $data['error'] = false;
            if (isset($_GET['error'])){
                $data['error'] = true;
            }

            echo view('/login', $data);
        }
    }


    public function logout()
    {

        $this->ionAuth->logout();
        if($this->ionAuth->loggedIn())
        {
            $this->ionAuth->logout();
        }        
        return redirect()->to('/login');

    }

    public function process()
    {

        $data['error'] = true;


        if ($this->request->getPost('process') == "true")
        {
            if ($this->ionAuth->login($this->request->getPost('email'), $this->request->getPost('password'), true))
            {
                //if(!empty($this->session->userdata('redirect_uri'))){
                //    redirect($this->session->userdata('redirect_uri'), 'refresh');
                //}else{
                //    redirect()->to('/');
                //}             
                return redirect()->to('/');
            }
            else
            {
                return redirect()->to('/login/?error=1'); //use redirects instead of loading views for compatibility with MY_Controller libraries
            }

        } else {
            return redirect()->to('/login/?error=1');
        }

    }

}
