<?php
namespace App\Controllers;


use CodeIgniter\Controller;
use App\Models\HelperFunctions;

class BaseController extends Controller
{

	protected $helpers = ['pepinstall'];
	protected $ionAuth;
	protected $db;
	protected $fn;
    protected $session;





	// global data variable 
	public $data = array();

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		 $this->session = \Config\Services::session();

		$this->ionAuth = new \IonAuth\Libraries\IonAuth();


		helper('pepinstall');
		if (!$this->ionAuth->loggedIn()) {
			echo view('/login');
		   exit();
   		}
		
		
	
		$this->data = $this->ionAuth->get_user_data()->getRowArray();


		$this->data['ionAuth'] = $this->ionAuth; 

		$this->db = \Config\Database::connect();

		$this->data['db'] = $this->db;


		$this->session = \Config\Services::session();

		$this->data['session'] = $this->session;


		  
		$this->data['_http'] = 'http'.(!empty(filter_input(INPUT_SERVER, 'HTTPS')) ? 's' : '').'://'.filter_input(INPUT_SERVER, 'HTTP_HOST');
	}

}
