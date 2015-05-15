<?php
//权限列表
return array(
        /**
         * ---------------------
         * 用户
         * ---------------------
         */
        'user' => array(
            'name' => '用户',
            'action' => array(
                'new'    => array(
                             'label' => '新建',
                             'urls'  => array('user/new'),
                            ),
                'edit'   => array(
                             'label' => '编辑',
                             'urls'  => array('user/edit'),
                            ),
                'delete' => array(
                             'label' => '删除',
                             'urls'  => array('user/delete'),
                            ),
            ),
            'desc' => '',
        ),

        /**
         * ---------------------
         * 用户组
         * ---------------------
         */
        'user_group' => array(
            'name'   => '权限组',
            'action' => array(

                'new'    => array(
                             'label' => '新建',
                             'urls'  => array('user-group/new'),
                            ),
                'edit'   => array(
                             'label' => '编辑',
                             'urls'  => array('user-group/edit'),
                            ),
                'delete' => array(
                             'label' => '删除',
                             'urls'  => array('user-group/delete'),
                            ),
            ),
            'desc' => '',
        ),

        /**
         * ---------------------
         * 消息
         * ---------------------
         */
        'message' => array(
            'name'   => '消息',
            'action' => array(
                'push'   => array(
                             'label' => '新建、推送到客户端',
                             'urls'  => array('message/push', 'message/new', 'message'),
                            ),
                'delete' => array(
                             'label' => '删除',
                             'urls'  => array('message/delete'),
                            ),
            ),
        ),


        /**
         * ---------------------
         * 操作日志
         * ---------------------
         */
        'log' => array(
              'name' => '操作日志',
              'action' => array(
                           'delete' => array(
                                        'label' => '删除',
                                        'urls'  => array('log/delete'),
                                       ),
                          ),
             ),


        /**
         * ---------------------
         * 标签管理
         * ---------------------
         */
        'tag' => array(
            'name' => '标签管理',
            'action' => array(
                'all'    => array(
                             'label' => '查看全部标签',
                             'urls'  => array('tag/all'),
                            ),
                'new'    => array(
                             'label' => '新建',
                             'urls'  => array('tag/new'),
                            ),
                'edit'   => array(
                             'label' => '编辑',
                             'urls'  => array('tag/edit'),
                            ),
                'delete' => array(
                             'label' => '删除',
                             'urls'  => array('tag/update'),
                            ),
            ),
        ),

        /**
         * ---------------------
         * 挂号单管理
         * ---------------------
         */
        'reg' => array(
            'name' => '挂号单管理',
            'action' => array(
                'all'    => array(
                             'label' => '查看全部挂号单',
                             'urls'  => array('reg/all'),
                            ),
                'update-status'   => array(
                             'label' => '更改状态',
                             'urls'  => array('reg/update-status'),
                            ),
                'edit'   => array(
                             'label' => '编辑',
                             'urls'  => array('reg/edit'),
                            ),
                'delete' => array(
                             'label' => '删除',
                             'urls'  => array('reg/update'),
                            ),
            ),
        ),

        /**
         * ---------------------
         * 患者管理
         * ---------------------
         */
        'patient' => array(
            'name' => '患者管理',
            'action' => array(
                'all'    => array(
                             'label' => '查看全部患者',
                             'urls'  => array('patient/all'),
                            ),
                'new'    => array(
                             'label' => '新建',
                             'urls'  => array('patient/new'),
                            ),
                'edit'   => array(
                             'label' => '编辑',
                             'urls'  => array('patient/edit'),
                            ),
                'delete' => array(
                             'label' => '删除',
                             'urls'  => array('patient/update'),
                            ),
            ),
        ),



        /**
         * ---------------------
         * 系统设置
         * ---------------------
         */
        'option' => array(
            'name' => '系统设置-版本更新',
            'action' => array(
                'edit'   => array(
                             'label' => '编辑',
                             'urls'  => array('option/edit'),
                            ),
            ),
        ),


        /**
         * ---------------------
         * 意见反馈
         * ---------------------
         */
        'feedback' => array(
            'name' => '意见反馈',
            'action' => array(
                'edit'   => array(
                             'label' => '查看',
                             'urls'  => array('feedback/all'),
                            ),
            ),
        ),

);