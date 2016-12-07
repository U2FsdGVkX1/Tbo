<?php
    class Block extends Base {
        private $data = array ();
        
        public function __construct ($data) {
            $this->data = $data;
            parent::__construct ();
        }
        public function init ($func, $from, $chat, $date) {
            if ($func == 'command' || $func == 'callback_query') {
                // 获取信息
                $msginfo = $this->db->get ('msglist', '*', [
                    'id' => $from['id']
                ]);
                
                // 更新记录或插入记录
                if ($msginfo) {
                    if ($date - $msginfo['time'] < 60) {
                        if ($msginfo['count'] > 3) {
                            $str = '@' . $from['username'] . ' 恭喜作死成功，你已被 ban' . "\n" . '距离解 ban 还有 ' . (60 - ($date - $msginfo['time'])) . ' s';
                            $this->telegram->sendMessage ($chat['id'], $str, NULL, array (), '');
                            die ();
                        }
                        
                        $this->db->update ('msglist', [
                            'count[+]' => 1,
                            'time' => $date
                        ], [
                            'id' => $from['id']
                        ]);
                    } else {
                        $this->db->update ('msglist', [
                            'count' => 1,
                            'time' => $date
                        ], [
                            'id' => $from['id']
                        ]);
                    }
                } else {
                    $this->db->insert ('msglist', [
                        'id' => $from['id'],
                        'count' => 1,
                        'time' => $date
                    ]);
                }
            }
        }
    }