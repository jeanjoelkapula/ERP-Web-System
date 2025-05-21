<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use IonAuth\Libraries\IonAuth;

/**
* Description of AuthFilter
*
*/
class AuthFilter implements FilterInterface {

    public function before(RequestInterface $request) {
        $this->ionAuth = new IonAuth();
        if (!($this->ionAuth->loggedIn())) {
            session()->set('redirect_url', current_url());
            echo view('/login',array('error'=>0));           
        }       
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response) {
        // Do something here
    }

} 