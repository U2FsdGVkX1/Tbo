<?php
    class Base extends FLModel {
        protected $telegram;
        protected $option;
        
        public function __construct () {
            $this->telegram = new TelegramModel;
            $this->option = new OptionModel;
            parent::__construct ();
        }
        public function fetch ($url, $postdata = null, $cookie = null, $header = array (), $convert = false) {
            // 访问
            $ch = curl_init ();
            curl_setopt ($ch, CURLOPT_URL, $url);
            if (!is_null ($postdata)) {
                curl_setopt ($ch, CURLOPT_POSTFIELDS, http_build_query ($postdata));
            }
            if (!is_null ($cookie)) {
                curl_setopt ($ch, CURLOPT_COOKIE, $cookie);
            }
            if (!empty ($header)) {
                curl_setopt ($ch, CURLOPT_HTTPHEADER, $header);
            }
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_HEADER, false);
            curl_setopt ($ch, CURLOPT_TIMEOUT, 20);
            $re = curl_exec ($ch);
            curl_close ($ch);
            if ($convert == true) {
                $re = mb_convert_encoding ($re, 'UTF-8', 'GBK');
            }
            
            return $re;
        }
        public function debug ($expression) {
            $text = print_r ($expression, true);
            $this->telegram->sendMessage (MASTER, $text, NULL, [], '');
        }
    }
