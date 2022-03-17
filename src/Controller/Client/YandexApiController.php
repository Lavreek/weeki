<?php
	namespace App\Controller\Client;

    use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpClient\HttpClient;


	/** Взаимодействие с Yandex DISK API
	 * 
	 *	$token - access_token = результат обработки oAuth (Yandex)
	 */
	class YandexApiController
	{
		private $token;
		private $http_client; // HTTP Client - для запросов к Rest Api

		function __construct($token)
		{
			$this->token = $token;
			$this->http_client = HttpClient::create();
		}

		public function getToken()
		{
			return $this->token;
		}

		/** Получение профиля пользователя
		 *	
		 *	Api scope: 
		 		Доступ к логину, имени и фамилии, полу,
		 		Доступ к адресу электронной почты,
		 		Доступ к дате рождения,
		 		Доступ к портрету пользователя,
		 */
		public function get_profileInfo()
		{
			$response = $this->http_client->request(
				'GET', "https://login.yandex.ru/info?", [
					'headers' => [
						'Accept' => 'application/json',
						'Authorization' => 'OAuth '.$this->token,
					],
				],
			);

			return $response->toArray();
		}

		/** Получение информации о корневом каталоге диска
		 *
		 */
		public function get_rootFolder()
		{
			$path = "/";	$limit = 9999;	$sub = "&preview_crop=true&preview_size=300x300";

			$response = $this->http_client->request(
				'GET', "https://cloud-api.yandex.net/v1/disk/resources?path=".$path."&limit=".$limit.$sub, [
					'headers' => [
						'Accept' => 'application/json',
						'Authorization' => 'OAuth '.$this->token,
					],
				],
			);

			return $response->toArray();
		}

		/** Получение информации о заданном каталоге диска
		 *
		 *	path - путь к объекту от корневого элемента
		 */
		public function get_openFolder(string $path)
		{
			$limit = 9999;	$sub = "&preview_crop=true&preview_size=300x300";

			$response = $this->http_client->request(
				'GET', "https://cloud-api.yandex.net/v1/disk/resources?path=".$path."&limit=".$limit.$sub, [
					'headers' => [
						'Accept' => 'application/json',
						'Authorization' => 'OAuth '.$this->token,
					],
				],
			);

			return $response->toArray();	
		}

		/** Возвращение элементов массива из тела ответа о каталоге
		 *
		 *	response - Тело ответа от запроса к серверу cloud-api.yandex.net
		 */
		public function get_folderItems($response)
		{
			$array = [];

			foreach ($response['_embedded']['items'] as $key => $value) {
				array_push($array, $value);
			}

			return $array;
		}
	}