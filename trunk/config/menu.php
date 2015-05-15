<?php
//左侧菜单配置
return array(

        /**
         * ------------------------
         * 控制面板首页
         * ------------------------
         */
        array(
            'name'       => '首页',
            'icon'       => 'entypo-gauge',
            'url'        => 'admin',
            'pattern'    => array('admin/'),
            'permission' => '*',
            'submenu'    => array(),
        ),


        /**
         * ------------------------
         * 用户管理
         * ------------------------
         */
        array(
         'name'     => '用户',
         'icon'     => 'entypo-user',
         'pattern' => array('admin/user/*', 'admin/user-group/*'),
         'submenu' => array(
                        array(
                         'name'   => '所有用户',
                         'url'    => 'admin/user/all',
                         'permission' => 'user.*',
                        ),
                        array(
                         'name'    => '权限组',
                         'url'     => 'admin/user-group/all',
                         'permission' => 'user-group.*',
                        ),
                ),
        ),


        /**
         * ------------------------
         * 统计
         * ------------------------
         */
        array(
         'name'     => '统计',
         'icon'     => '',
         'pattern' => array('admin/category/*', 'admin/disease-symptoms/*', 'admin/feedback/*'),
         'submenu' => array(
              
                        array(
                         'name'    => '用户统计',
                         'url'    => 'admin/satistics/user',
                         'permission' => 'doctor.*',
                        ),
                      
                ),
        ),

        // /**
        //  * ------------------------
        //  * 系统设置
        //  * ------------------------
        //  */
        // array(
        //  'name'     => '系统设置',
        //  'icon'     => '',
        //  'pattern' => array('admin/category/*', 'admin/disease-symptoms/*'),
        //  'submenu' => array(
        //                 array(
        //                  'name'   => '用户反馈',
        //                  'url'    => 'admin/feedback/all',
        //                  'permission' => 'feedback.*',
        //                 ),
        //                 array(
        //                  'name'   => '版本更新',
        //                  'url'    => 'admin/option/edit',
        //                  'permission' => 'option.*',
        //                 ),
           
        //         ),
        // ),
);