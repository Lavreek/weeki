<?php
    namespace App\Twig;

    use Symfony\Component\HttpFoundation\Response;

    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;

    class AppExtension extends AbstractExtension
    {
        public function getFunctions()
        {
            return [
                new TwigFunction('HeaderLinks', [$this, 'navbarLinks']),
                new TwigFunction('FolderResources', [$this, 'putFolderResources']),

            ];
        }

        /** Размещает кнопки навигационной панели
         *
         *  currentLink - Route/Путь как указан в контроллере
         *  auth - Состояние авторизации true/null 
         */
        public function navbarLinks(string $currentLink, $auth)
        {
            $tabList = $this->getTabList($auth);
            
            foreach ($tabList as $key => $value) {
                if ($currentLink == $value['route'])
                    echo "<a class='nav-link active' aria-current='page' href='".$value['route']."'>".$value['info']."</a>";
                else
                    echo "<a class='nav-link' aria-current='page' href='".$value['route']."'>".$value['info']."</a>";
            }
        }

        /** Возвращение листа ссылок и информации
         *
         */
        private function getTabList($auth)
        {
            if ($auth)
                return [ ['route' => "/", 'info' => "Главная страница"], ['route' => "/Профиль", 'info' => "Профиль"] ];
            else
                return [ ['route' => "/", 'info' => "Главная страница"], ['route' => "/Вход", 'info' => "Вход"] ];
        }

        /** Расположить объекты на странице
         *
         *  resources - Массив данных об объектах каталога
         */
        public function putFolderResources(array $resources)
        {
            echo $this->putHeaderCount(count($resources));
            echo $this->putCardGrid($resources);
        }
 
        /** Отображение счётчика загруженных элементов
         *
         */
        private function putHeaderCount($int)
        {
            return "<p class='lead'>Количество записей в каталоге: ".$int."</p>";
        }

        /** Отображение сетки карточек с элементами
         *
         */
        private function putCardGrid($resources)
        {
            $string = "";

            $string .= "<div class='row row-cols-md-5 text-center g-4 py-5'>";
            
            foreach ($resources as $key => $value) {
                $string .= 
                "<div class='col card-body'>
                <div class='card bg-transparent'>
                <div class='card-body'>
                <img src='".$this->get_filePreview($value)."'>
                <a class='nav-link' ".$this->get_fileType($value)."><h5 class='card-title'>".$value['name']."</h5></a>
                <div class='row justify-content-center'>
                <div class='row w-100'>
                ".$this->get_filePublicUrl($value)."
                ".$this->get_fileDownload($value)."
                </div>
                </div>
                <h5 class='text-muted lead'>".$value['type']."</h5>
                </div>
                </div>
                </div>";
            }

            return $string .= "</div>";
        }

        /** Получение превью для файла
         *
         */
        private function get_filePreview($value)
        {
            $img = [ 'dir' => 'folder_resources/folder.svg', 'file' => 'folder_resources/file.svg', ];
            
            if (isset($value['preview']))
                return $value['preview'];

            if (isset($img[$value['type']]))
                return $img[$value['type']];

            else
                return "folder_resources/error.svg";
        }

        /** Доступна ли видимость в интернете (публичная ссылка)
         *
         */
        private function get_filePublicUrl($value)
        {
            if (isset($value['public_url']))
                return "<div class='col py-3'><a href='".$value['public_url']."'><img src='folder_resources/show.svg'></a></div>";
            else
                return "";
        }

        /** Путь к файлу (для скачивания) DEMO
         *
         */
        private function get_fileDownload($value)
        {
            if (isset($value['file']))
                return "<div class='col py-3'><a target='_blank' href='".$value['file']."'><img src='folder_resources/download.svg'></a></div>";
            else
                return "";
        }

        /** Формирование ссылок на ресурс по типу
         *
         */
        private function get_fileType($value)
        {
            if ($value['type'] == "dir")
                return "href='?path=".$value['path']."&type=".$value['type']."'";
            else
                return "";
        }

        
    }
?>