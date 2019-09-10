<?php
    /*
    -----BEGIN INFO-----
    @PluginName 必应美图
    @Description 发送/bing即可查看美♂图
    @Author U2FsdGVkX1
    @AuthorEmail U2FsdGVkX1@gmail.com
    @Version 1.0
    -----END INFO-----
    */
    class Bing extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/bing') {
                $webdata = json_decode ($this->fetch ('http://cn.bing.com/HPImageArchive.aspx?format=js&n=1&idx=0'), true);
                $imgurl = 'http://cn.bing.com' . $webdata['images'][0]['url'];
                $copyright = $webdata['images'][0]['copyright'];
                $button = json_encode (array (
                    'inline_keyboard' => array (
                        array (array (
                            'text' => '下一张',
                            'callback_data' => 'bing 1'
                        ))
                    )
                ));
                $this->telegram->sendPhoto ($chat['id'], $imgurl, $copyright, $message_id, $button);
            }
        }
        public function callback_query ($callback_data, $callback_id, $callback_from, $message_id, $from, $chat, $date) {
            $callbackExplode = explode (' ', $callback_data);
            if ($callbackExplode[0] == 'bing' && isset ($callbackExplode[1])) {
                $i = $callbackExplode[1];
                
                $webdata = json_decode ($this->fetch ('http://cn.bing.com/HPImageArchive.aspx?format=js&n=1&idx=' . $i), true);
                $imgurl = 'http://cn.bing.com' . $webdata['images'][0]['url'];
                $copyright = $webdata['images'][0]['copyright'];
                if ($i == 0) {
                    $button = json_encode (array (
                        'inline_keyboard' => array (
                            array (array (
                                'text' => '下一张',
                                'callback_data' => 'bing 1'
                            ))
                        )
                    ));
                } else {
                    $button = json_encode (array (
                        'inline_keyboard' => array (
                            array (array (
                                'text' => '上一张',
                                'callback_data' => 'bing ' . ($i - 1)
                            )),
                            array (array (
                                'text' => '下一张',
                                'callback_data' => 'bing ' . ($i + 1)
                            ))
                        )
                    ));
                }
                $media = array(
                    'type'  => 'photo',
                    'media' => $imgurl,
                    'caption' => $copyright
                );
                // $this->telegram->editMessageCaption($chat['id'], $message_id, NULL, $copyright);
                $this->telegram->editMessageMedia($chat['id'], $message_id, NULL, json_encode($media), $button);
                $this->telegram->answerCallback($callback_id);
            }
        }
    }
