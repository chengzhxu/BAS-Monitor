<?php

$config = [
    //频道
    'channel' => [
        [
            'id' => 1,
            'name' => '综艺'
        ],
        [
            'id' => 2,
            'name' => '电视剧'
        ],
        [
            'id' => 3,
            'name' => '电影'
        ],
        [
            'id' => 20,
            'name' => '音乐'
        ],
        [
            'id' => 50,
            'name' => '动漫'
        ],
        [
            'id' => 51,
            'name' => '纪录片'
        ],
        [
            'id' => 99,
            'name' => '财经'
        ],
        [
            'id' => 104,
            'name' => '女性'
        ],
        [
            'id' => 105,
            'name' => '生活'
        ],
        [
            'id' => 106,
            'name' => '新闻'
        ],
        [
            'id' => 111,
            'name' => '原创'
        ],
        [
            'id' => 112,
            'name' => 'IPTV轮播'
        ],
        [
            'id' => 113,
            'name' => '快乐购'
        ],
        [
            'id' => 115,
            'name' => '教育'
        ],
        [
            'id' => 116,
            'name' => '游戏'
        ],
        [
            'id' => 117,
            'name' => '体育'
        ],
        [
            'id' => 118,
            'name' => '厂家视频'
        ],
        [
            'id' => 119,
            'name' => '短视频'
        ],
    ],

    //广告状态
    'ad_status' => [
        [
            'status_id' => 1,
            'status_name' => '开启'
        ],
        [
            'status_id' => 0,
            'status_name' => '关闭'
        ],
        [
            'status_id' => 9,
            'status_name' => '测试'
        ],
        [
            'status_id' => -1,
            'status_name' => '删除'
        ],
    ],

    //监测
    'track_event' => [
        [
            'value' => 'start',
            'name' => '开始（start）'
        ],
        [
            'value' => 'firstQuartile',
            'name' => '四分之一（firstQuartile）'
        ],
        [
            'value' => 'midpoint',
            'name' => '二分之一（midpoint）'
        ],
        [
            'value' => 'thirdQuartile',
            'name' => '四分之三（thirdQuartile）'
        ],
        [
            'value' => 'complete',
            'name' => '完成（complete）'
        ],
    ],

    //频控
    'limit_detail' => [
        [
            'value' => 0,
            'name' => '每日'
        ],
        [
            'value' => 1,
            'name' => '每周'
        ],
        [
            'value' => 2,
            'name' => '周期'
        ],
    ],

    //优先级
    'priority' => [
        [
            'value' => 0,
            'text' => '0级 - 最快',
        ],
        [
            'value' => 1,
            'text' => '1级',
        ],
        [
            'value' => 2,
            'text' => '2级',
        ],
        [
            'value' => 3,
            'text' => '3级',
        ],
        [
            'value' => 4,
            'text' => '4级',
        ],
        [
            'value' => 5,
            'text' => '5级',
        ],
        [
            'value' => 6,
            'text' => '6级 - 默认',
        ],
        [
            'value' => 7,
            'text' => '7级',
        ],
        [
            'value' => 8,
            'text' => '8级',
        ],
        [
            'value' => 9,
            'text' => '9级 - 最慢',
        ],
    ],

    //投放代码域名
    'monitor_domain' => [
        'bnc66.com'
    ],

    //mgtv ott 广告位
    'mgtv_ott' => [
        [
            'id' => 127,
            'text' => 'OTT-开机大图',
        ],
        [
            'id' => 200028,
            'text' => 'OTT-前贴片',
        ],
        [
            'id' => 200032,
            'text' => 'OTT-暂停',
        ],
        [
            'id' => 200031,
            'text' => 'OTT-非常驻角标',
        ],
        [
            'id' => 9000092,
            'text' => 'OTT-常驻角标',
        ],
    ],

    //媒体属性
    'media_type' => [
        [
            'type' => 1,
            'text' => '游戏',
        ],
        [
            'type' => 2,
            'text' => '点播',
        ],
        [
            'type' => 3,
            'text' => '直播',
        ],
        [
            'type' => 4,
            'text' => '综合',
        ],
        [
            'type' => 5,
            'text' => '其他',
        ],
    ],


    //Campaign 投放监测代码
    'campaign_extra' => [
        'put_url' => 'http://ppsport.bnc66.com/v1/pdb?campaign_id={CAMPAIGN_ID}&ip=__IP__&mac_raw=[MAC]&ts=__TIMESTAMP__&mac1=__MAC1__&mac=__MAC__&chan=__CHID__&iesid=__IESID__&make=__MAKE__&sys=__SYSTEM__&model=__MODEL__&os=[OS]&osv=__OSV__&ua=__UA__&android=__ANDROIDID__&imei=__IMEI__&rs=__RESOLUTION__'
    ]
];



return $config;
