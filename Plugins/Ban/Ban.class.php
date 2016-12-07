<?php
    class Ban extends Base {
        private $data = array ();
        
        public function __construct ($data) {
            $this->data = $data;
            parent::__construct ();
        }
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/ban') {
                if ($this->telegram->isAdmin ($chat['id'], $from['id'])) {
                    $this->db->insert ('banlist', [
                        'id' => $param[0]
                    ]);
                }
            }
        }
        public function message ($message, $message_id, $from, $chat, $date) {
            if ($this->db->has ('banlist', [
                'id' => $from['id']
            ])) {
                for ($i = 0; $i < 20; $i++) {
                    //$this->telegram->sendMessage ($chat['id'], '不要看上面的消息哦~');
                }
            }
        }
    }