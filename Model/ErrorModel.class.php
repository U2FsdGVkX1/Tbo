<?php
    class ErrorModel extends FLModel {
        public function __construct () {
            set_error_handler (array ($this, 'errorHandler'));
            set_exception_handler (array ($this, 'exceptionHandler'));
            parent::__construct ();
        }
        public function errorHandler ($errno, $errstr, $errfile, $errline) {
            $this->checkPlugin ();
            
            $errMsg = '在 ' . $errfile . ' 的第 ' . $errline . ' 行发生了一个错误：' . "\n" . $errstr;
            $this->sendError ('-1001078722237', $this->replacePath ($errMsg));
        }
        public function exceptionHandler ($exception) {
            $this->checkPlugin ();
            
            $errMsg = '';
            foreach ($exception->getTrace () as $i => $ep_d) {
                $errMsg .= '在 ' . $ep_d['file'] . ' 的第 ' . $ep_d['line'] . ' 行发生了一个异常：' . "\n";
                if (!empty ($ep_d['class'])) {
					$errMsg .= $ep_d['class'] . '->';
				}
				if (!empty ($ep_d['function'])) {
				    $errMsg .= $ep_d['function'] . ' ';
				    $errMsg .= '(';
					
					if (!empty ($ep_d['args'])) {
						$errMsg .= var_export ($ep_d['args'], true);
					}
					
					$errMsg .= ')';
				}
				$errMsg .= "\n";
            }
            $this->sendError ('-1001078722237', $this->replacePath ($errMsg));
        }
        public function sendError ($chat_id = '-1001078722237', $text = '发生了一个错误') {
            /** 判断是否发送报告 */
            if (DEBUG) {
                /** 初始化 */
                $url = 'https://api.telegram.org/bot' . TOKEN . '/sendMessage';
                $postdata = [
                    'chat_id' => $chat_id,
                    'text' => $text
                ];
                
                /** 发送报告 */
                $ch = curl_init ();
        		curl_setopt ($ch, CURLOPT_URL, $url);
        		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
        		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        		$re = curl_exec ($ch);
        		curl_close ($ch);
            }
    		
    		/** 结束 */
            die ();
        }
        private function checkPlugin () {
            if (isset ($GLOBALS['cuPlugin'])) {
                $this->db->update ('plugins', [
                    'lasterror' => time ()
                ], [
                    'pcn' => $GLOBALS['cuPlugin']
                ]);
            }
        }
        private function replacePath ($str) {
            return str_replace (APP_PATH, '', $str);
        }
    }