<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderUtils;

//	Session
use Symfony\Component\HttpFoundation\Session\Session;
//

class WeekiAuthController extends AbstractController
{
	private $request;
	private $session;

    const request_url = "https://oauth.yandex.ru/authorize?";
    const request_params = "response_type=token";
    const request_clientid = "&client_id=31814c65e70d448f8b8135811059f278";
    const request_redirect_uri = "&redirect_uri=http://127.0.0.1:8000/Вход";

	public function __construct() {
        $this->session = new Session();
        $this->session->start();

        if (empty($this->session->get('access_token')))
            $this->request = new Request(
                $_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER
            );
	}

    #[Route('/Вход', name: 'app_weeki_auth')]
    public function index(): Response
    {
        $auth = $this->session->get('access_token');
        
    	if (is_null($auth))
    	{
            if (!empty($this->request->query->get('access_token')))
            {
    		    if (count($this->request->query->keys()) > 0)
                {
                    $keys_array = $this->request->query->keys();

                    foreach ($keys_array as $key => $value)
                        $this->session->set($keys_array[$key], $this->request->query->get($value));

                    if (!empty($this->session->get('access_token')))
                        $auth = true;
                }
    		}
    	}
        else
            $auth = true;

        return $this->render('weeki_auth/index.html.twig', [
        	'Route' => "/Вход",
        	'Auth' => $auth,
            'oAuth_query' => $this->constructQuery(),
        ]);
    }

    private function constructQuery()
    {
        return WeekiAuthController::request_url.WeekiAuthController::request_params.WeekiAuthController::request_clientid.WeekiAuthController::request_redirect_uri;
    }
}
