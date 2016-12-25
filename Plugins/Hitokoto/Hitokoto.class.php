<?php
    /*
    -----BEGIN INFO-----
    @PluginName 一言
    @Description 发送/hitokoto即可膜蛤
    @Author U2FsdGVkX1
    @AuthorEmail U2FsdGVkX1@gmail.com
    @Version 1.0
    -----END INFO-----
    */
    class Hitokoto extends Base {
        private $data = array ();
        
        public function __construct ($data) {
            $this->data = $data;
            parent::__construct ();
        }
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/hitokoto') {
                $array = json_decode (file_get_contents (__DIR__ . '/hitokoto.json'), true);
                $hitokoto = $array[array_rand ($array)]['hitokoto'];
                
                $button = json_encode (array (
                    'inline_keyboard' => array (
                        array (array (
                            'text' => '再来一条',
                            'callback_data' => 'hitokoto'
                        ))
                    )
                ));
                $this->telegram->sendMessage ($chat['id'], $hitokoto, $message_id, $button);
            }
        }
        public function message ($message, $message_id, $from, $chat, $date) {
            //throw new Exception('Uncaught Exception');
        }
        public function callback_query ($callback_data, $callback_id, $callback_from, $message_id, $from, $chat, $date) {
            if ($callback_data == 'hitokoto') {
                $array = json_decode (file_get_contents (__DIR__ . '/hitokoto.json'), true);
                $hitokoto = $array[array_rand ($array)]['hitokoto'];
                
                $this->telegram->editMessage ($chat['id'], $message_id, $hitokoto);
            }
        }
        public function new_member ($new_member, $message_id, $from, $chat, $date) {
            //throw new Exception('Uncaught Exception');
        }
        public function left_member ($left_member, $message_id, $from, $chat, $date) {
            //throw new Exception('Uncaught Exception');
        }
    }
