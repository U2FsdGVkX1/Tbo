<?php
    class Plugins extends FLController {
        function run () {
            $this->view->render ();
        }
        function init () {
            $systemModel = new SystemModel;
            if ($_COOKIE['password'] != $systemModel->password ()) {
                header ('Location: ' . APP_URL . '/index.php/login');
                exit ();
            }
        }
        function install () {
            /** 检查 */
			if (empty ($_POST['pcn'])) {
				exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
			}
			
			/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->install ($_POST['pcn']);
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function uninstall () {
            /** 检查 */
			if (empty ($_POST['pcn'])) {
				exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
			}
			
			/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->uninstall ($_POST['pcn']);
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function enable () {
            /** 检查 */
			if (empty ($_POST['pcn'])) {
				exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
			}
			
			/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->enable ($_POST['pcn']);
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function disable () {
            /** 检查 */
			if (empty ($_POST['pcn'])) {
				exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
			}
			
			/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->disable ($_POST['pcn']);
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function installAll () {
        	/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->installAll ();
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function uninstallAll () {
        	/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->uninstallAll ();
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function enableAll () {
        	/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->enableAll ();
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
        function disableAll () {
        	/** 初始化 */
			$pluginModel = new PluginModel;
			
			/** 安装 */
			$pluginModel->disableAll ();
			
			/** 返回 */
			exit (json_encode (array ('code' => 0)));
        }
    }
