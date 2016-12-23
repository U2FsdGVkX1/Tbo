<?php
    /**
     * 框架主类
     */
    class Core {
        /** 
         * 当前 URL 模式
         * 
         * @var    string
         */
        private $urlmode;
        
        /**
         * 运行框架
         *
         * @return void
         */
        public function run ($urlmode = 1) {
            $this->urlmode = $urlmode;
            spl_autoload_register (array ($this, 'autoload'));
            if (!defined ('NOROUTE'))
                $this->route ();
        }

        /**
         * 处理URL
         *
         * @return void
         */
        public function route () {
            /** 初始化变量 */
            $module = '';
            $controller = 'Index';
            $action = 'run';
            $needAction = false;
            
            /** 处理路由 */
            if ($this->urlmode == 1) {
                $mod = explode (':', @$_GET['mod']);
            } else if ($this->urlmode == 2) {
                $mod = explode ('/', @$_SERVER['QUERY_STRING']);
            } else if ($this->urlmode == 3) {
                if (!isset ($_SERVER['PATH_INFO']))
                    $_SERVER['PATH_INFO'] = '';
                if (substr($_SERVER['PATH_INFO'], -1) == '/') {
                    header ('Location: ' . APP_URL . '/index.php');
                    exit ();
                }
                    
                $mod = explode ('/', $_SERVER['PATH_INFO']);
                array_shift ($mod);
            }
            
            /** 处理变量 */
            if (isset ($mod[0]) && is_dir (CONTROLLER_PATH . '/' . ucfirst ($mod[0]))) {
                $module = ucfirst ($mod[0]);
                array_shift ($mod);
            }
            if (isset ($mod[0]) && file_exists (CONTROLLER_PATH . '/' . $module . '/' . ucfirst ($mod[0]) . '.class.php')) {
                $needAction = true;
                $controller = ucfirst ($mod[0]);
                array_shift ($mod);
            }
            
            $initFile = CONTROLLER_PATH . '/' . $module . '/Init.class.php';
            $initExists = file_exists ($initFile);
            $controllerFile = CONTROLLER_PATH . '/' . $module . '/' . $controller . '.class.php';
            if ($initExists)
                require_once $initFile;
            require_once $controllerFile;
            
            if ($needAction && isset ($mod[0]) && method_exists ($controller, $mod[0])) {
                $action = $mod[0];
                array_shift ($mod);
            }
            $param = $mod;
            
            /** 分发 */
            if ($initExists) {
                $initObject = new Init ($controller, $action, $module, $param);
                $initObject->run ();
                if (method_exists ($initObject, $controller))
                    $controllerObject->controller ();
            }
            $controllerObject = new $controller ($controller, $action, $module, $param);
            if (method_exists ($controller, 'init'))
                $controllerObject->init ();
            $controllerObject->$action ();
        }

        /**
         * 自动加载
         *
         * @param  string $class 要加载的类
         * @return void
         */
        public function autoload ($class){
            /** 初始化变量 */
            $frame = FRAME_PATH . '/' . $class . '.class.php';
            $model = MODEL_PATH . '/' . $class . '.class.php';

            /** 检查文件并引入 */
            if (file_exists ($model)) {
                require_once $model;
            } else if (file_exists ($frame)) {
                require_once $frame;
            } else {
                die ('引入了一个不存在的类:' . $class);
            }
        }
    }
