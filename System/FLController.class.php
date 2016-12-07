<?php
    /**
     * 控制器基类
     */
    class FLController extends FLBase {
        /** 
         * 当前的控制器
         * 
         * @var    string
         */
        protected $controller;
        
        /** 
         * 当前的动作
         * 
         * @var    string
         */
        protected $action;

        /** 
         * 视图
         * 
         * @var    string
         */
        protected $view;
        
        /** 
         * 模块
         * 
         * @var    string
         */
        protected $module;
        
        /** 
         * 参数
         * 
         * @var    string
         */
        protected $param;
        
        /**
         * 构造类
         *
         * @param  string $controller 控制器
         * @param  string $action 动作
         * @return void
         */
        public function __construct ($controller, $action, $module, $param) {
            $this->controller = $controller;
            $this->action = $action;
            $this->module = $module;
            $this->param = $param;
            $this->view = new FLView ($controller, $action, $module, $param);
        }
    }
