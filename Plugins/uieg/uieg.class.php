<?php
/*
-----BEGIN INFO-----
@PluginName 设置界面示例
@Description 演示如何实现设置界面并进行数据交互
@Author TG@heydust
@AuthorURL http://logs.ee
@Version 1.0
@UIarea 640px,640px
-----END INFO-----
UIarea 为设置界面打开时的宽高,单位为px也可以为百分比%
*/

class uieg extends Base {

    /** 载入设置 **/
    public function settings () {
        include_once 'settings.html';
    }

    /** 保存前端设置 **/
    public function saveSettings () {
        //强行演示...
       return '喵:' . http_build_query($_POST);
        
        foreach($_POST as $k => $v){
            if(!in_array($k,['pcn','method'])){
                $this->option->iou($k,$v);
            }
        }
        return;
    }

}