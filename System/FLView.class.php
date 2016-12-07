<?php
    /**
     * 视图基类
     */
    class FLView extends FLBase {
        /** 
         * 当前的控制器
         * 
         * @var    string
         */
        private $controller;
        
        /** 
         * 当前的动作
         * 
         * @var    string
         */
        private $action;
        
        /** 
         * 当前的模块
         * 
         * @var    string
         */
        private $module;
        
        /** 
         * 当前的参数
         * 
         * @var    array
         */
        protected $param;
        
        /**
         * 构造类
         *
         * @param  string $controller 控制器
         * @param  string $action     动作
         * @return void
         */
        public function __construct ($controller, $action, $module, $param) {
            $this->controller = $controller;
            $this->action = $action;
            $this->module = $module;
            $this->param = $param;
        }

        /**
         * 渲染视图
         * 
         * @param  string $view 渲染的视图名
         * @param  string $data 传递给视图的数据 
         * @return void
         */
        public function render ($view = NULL, $data = array (), $noModule = false) {
            if ($view === NULL)
                $view = $this->controller;
            foreach ($data as $dataKey => $dataValue)
                $$dataKey = $dataValue;
            if ($this->module == '' || $noModule == true)
                require_once VIEW_PATH . '/' . $view . '.php';
            else
                require_once VIEW_PATH . '/' . $this->module . '/' . $view . '.php';
        }

        /**
         * 加载资源
         *
         * @param  string $name 文件名
         * @return string       资源地址
         */
        public function loadSource ($name) {
            return VIEW_URL . '/' . $name;
        }
    }
