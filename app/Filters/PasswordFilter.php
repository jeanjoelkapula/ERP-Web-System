<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use IonAuth\Libraries\IonAuth;
class PasswordFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ionAuth = new \IonAuth\Libraries\IonAuth();
        // Do something here
        if (!($ionAuth->loggedIn())) {
            session()->set('redirect_url', current_url());
            echo view('/login',array('error'=>0));    
                     
        } else if($ionAuth->user()->row()->temp_password == 1){
            return redirect()->to('/team/member/'.$ionAuth->user()->row()->id.'?changepassword=1');
        }
        
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}