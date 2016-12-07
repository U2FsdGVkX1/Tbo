<?php
    class Status extends FLController {
        function run () {
            $this->view->render ();
        }
        function init () {
            $systemModel = new SystemModel;
            if ($_COOKIE['password'] != $systemModel->password ()) {
                header ('Location: index.php/login');
                exit ();
            }
        }
    }
