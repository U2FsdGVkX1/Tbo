<?php
    /**
     * 模型基类
     */
    class FLModel extends FLBase {
        /** 
         * 数据库类
         * 
         * @var    medoo
         */
        protected $db;

        /**
         * 构造类
         *
         * @return void
         */
        public function __construct () {
            if (DBHOST != '') {
                $this->db = new Medoo (array (
                    'database_type' => 'mysql',
                    'database_name' => DBNAME,
                    'server' => DBHOST,
                    'username' => DBUSER,
                    'password' => DBPASS,
                    'charset' => 'utf8',
                    'option' => array (
                        PDO::ATTR_PERSISTENT => DBPERSISTENT
                    )
                ));
            }
        }
    }
