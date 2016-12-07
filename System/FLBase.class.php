<?php
    /**
     * 真·基类
     */
    class FLBase {
        /**
         * 加载一个外部库
         *
         * @param string  $name 文件名
         * @param boolean $once 是否检测重复加载
         * @return void
        */
        public function loadLib ($name, $once = true) {
            $file = LIB_PATH . '/' . $name;
            $once ? require_once ($file) : require ($file);
        }
    }
