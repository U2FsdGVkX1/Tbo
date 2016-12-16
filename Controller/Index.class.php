<?php
    class Index extends FLController {
        function run () {
            $this->view->render ();
        }
        function init () {
            session_start ();
            if ($_SESSION['logined'] != true) {
                header ('Location: ' . APP_URL . '/index.php/login');
                exit ();
            }
        }
    }
