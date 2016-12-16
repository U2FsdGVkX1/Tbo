<?php
    /** 定义 */
    define ('FRAME_PATH', APP_PATH . '/System');
    define ('CONFIG_PATH', APP_PATH . '/Config');
    define ('MODEL_PATH', APP_PATH . '/Model');
    define ('VIEW_PATH', APP_PATH . '/Templates');
    define ('CONTROLLER_PATH', APP_PATH . '/Controller');
    define ('LIB_PATH', APP_PATH . '/Lib');
    
    define ('FRAME_URL', APP_URL . '/System');
    define ('CONFIG_URL', APP_URL . '/Config');
    define ('MODEL_URL', APP_URL . '/Model');
    define ('VIEW_URL', APP_URL . '/Templates');
    define ('CONTROLLER_URL', APP_URL . '/Controller');
    define ('LIB_URL', APP_URL . '/Lib');
    
    define ('FRAME_VERIONS', '1.9.3');

    /** 引入 */
    require_once FRAME_PATH . '/Core.php';
    foreach (glob (CONFIG_PATH . '/*.php') as $configFile) {
        require_once $configFile;
    }

    /** 运行框架 */
    $f = new Core;
    $f->run (3);
