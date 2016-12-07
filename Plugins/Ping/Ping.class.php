<?php
    class Ping extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/ping') {
                $t1 = microtime (true);
                $pong_id = $this->telegram->sendMessage ($chat['id'], 'Pong!', $message_id);
                $t2 = microtime (true);
                
                $time = round (($t2 - $t1) * 1000, 2);
                $this->telegram->editMessage ($chat['id'], $pong_id, 'Pong!' . "\n" . 'Time:<code>' . $time . ' ns</code>');
            }
        }
    }