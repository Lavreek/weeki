<?php
	namespace App\Controller;

	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\Routing\Annotation\Route;

    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    
//	Session
	use Symfony\Component\HttpFoundation\Session\Session;
//

//  Controllers
    use App\Controller\Client\YandexApiController; // HTTP Client запросы к Yandex Api
//

class HomePageController extends AbstractController
{
	private $session;
    private $request;

	public function __construct() {
		$this->session = new Session();
		$this->session->start();

		if (!empty($this->session->get('access_token')))
        {
            $this->request = new Request(
                $_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER
            );
        }
	}

    #[Route('/', name: 'app_home_page')]
    public function index(): Response
    {
        $route = "/";

        $auth = $this->session->get('access_token');

    	if (empty($auth))
    	   return $this->render('not_auth/index.html.twig', ['Route' => $route, 'Auth' => $auth]);

    	else
    	{
            $auth = true;

            $yandex = new YandexApiController($this->session->get('access_token'));

            if ($this->request->query->get('path') !== null && $this->request->query->get('type') !== null)
            {
                $path = $this->request->query->get('path');

                $folder = $yandex->get_openFolder($path);

                $items = $yandex->get_folderItems($folder);
            }
            else
            {
                $root = $yandex->get_rootFolder();

                $items = $yandex->get_folderItems($root);            
    		}

    		return $this->render('home_page/index.html.twig', [
        		'Route' => $route,
            	'Auth' => $auth,
            	'folder_items' => $items,
        	]);
    	}			
    }
}
