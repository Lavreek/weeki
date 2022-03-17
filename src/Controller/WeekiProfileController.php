<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Controller\Client\YandexApiController;

// Session
use Symfony\Component\HttpFoundation\Session\Session;
//

class WeekiProfileController extends AbstractController
{
	private $session;

	public function __construct() {
		$this->session = new Session();
		$this->session->start();
	}

    #[Route('/Профиль', name: 'app_weeki_profile')]
    public function index(): Response
    {
        $route = "/Профиль";

        $auth = $this->session->get('access_token');

        if (!empty($auth))
    	{
            $auth = ture;

            $yandex = new YandexApiController($this->session->get('access_token'));

            $items = $yandex->get_profileInfo();

            if ($items['sex'] == "male") {
                $items['sex'] = "profile/male.svg";
            }
            elseif ($items['sex'] == "female") {
                $items['sex'] = "profile/female.svg";
            }
            else
                $items['sex'] = "profile/undefined.svg";

    		return $this->render('weeki_profile/index.html.twig', [
	        	'Route' => $route, 'Auth' => $auth,

                'login' => $items['login'],
                'default_email' => $items['default_email'],
                'display_name' => $items['display_name'],
                'real_name' => $items['real_name'],
                'sex' => $items['sex'],
                'birthday' => $items['birthday'],
	        ]);
    	}
        else
        {
        	return $this->render('not_auth/index.html.twig', [
	        	'Route' => $route, 'Auth' => $auth,
	        ]);
        }
    }
}
