<?php
    class Login extends FLController {
        function run () {
            $_SESSION['logined'] = false;
            $this->view->render ();
        }
        function ajaxLogin () {
            /** 检查 */
            if (FASTLOGIN) {
                exit (json_encode (array ('code' => -2, 'msg' => '请使用快速登录')));
            }
            if (empty ($_POST['password'])) {
                exit (json_encode (array ('code' => -9999, 'msg' => '参数为空')));
            }
            
            /** 初始化 */
            $systemModel = new SystemModel;
            
            /** 返回 */
            if (md5 (md5 (md5 ($_POST['password']))) == $systemModel->password () || $_POST['password'] == $systemModel->password ()) {
                $_SESSION['logined'] = true;
                exit (json_encode (array ('code' => 0)));
            } else {
                exit (json_encode (array ('code' => -1, 'msg' => '密码错误')));
            }
        }
        function fastLogin () {
            /** 检查 */
            if (!FASTLOGIN) {
                exit (json_encode (array ('code' => -1, 'msg' => '请使用密码登录')));
            }
            
            /** 初始化 */
            $telegramModel = new TelegramModel;
            
            /** 发送授权 */
            $str = $_SERVER['REMOTE_ADDR'] . ' 希望登录后台，是否授权？';
            $button = json_encode (array (
                'inline_keyboard' => array (
                    array (array (
                        'text' => '授权',
                        'callback_data' => 'fastLogin_allow ' . $_SERVER['REMOTE_ADDR']
                    )),
                    array (array (
                        'text' => '不授权',
                        'callback_data' => 'fastLogin_noallow'
                    ))
                )
            ));
            $telegramModel->sendMessage (MASTER, $str, NULL, $button);
            
            /** 返回 */
            exit (json_encode (array ('code' => 0)));
        }
        function fastLoginVerify () {
            /** 初始化 */
            $optionModel = new OptionModel;
            
            /** 验证 */
            $ip = $optionModel->getvalue ('fastlogin_ip');
            
            /** 返回 */
            if ($_SERVER['REMOTE_ADDR'] == $ip) {
                $optionModel->update ('fastlogin_ip', '');
                $_SESSION['logined'] = true;
                exit (json_encode (array ('code' => 0)));
            } else {
                exit (json_encode (array ('code' => -1, 'msg' => 'IP不正确')));
            }
        }
        function ajaxLogout () {
            $_SESSION['logined'] = false;
            
            header ('Location: ' . APP_URL . '/index.php/login');
            exit ();
        }
        function init () {
            session_start ();
        }
    }
