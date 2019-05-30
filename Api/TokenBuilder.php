<?php
/**
 * Author: liguidong94@gmail.com
 * Date: 2019/5/27 11:06 AM
 */
namespace Helper\Agora\Api;

use Helper\Agora\Src\AccessToken;
use Helper\Agora\Agora;
use Helper\Agora\Src\SimpleTokenBuilder;

class TokenBuilder extends Agora
{

    /**
     * 生产token
     * @param $channel 频道
     * @param $uid 用户唯一id
     * @return string
     */
    public function token($channel,$uid,$expireTimestamp = 0)
    {
        /**
         * 用户权限，共有4种：
        AttendeePrivileges：通话方权限
        kJoinChannel：加入频道
        kPublishAudioStream：发布音频流
        kPublishVideoStream：发布视频流
        kPublishDataStream：发布数据流
         *
        PublisherPrivileges：发布者权限
        kJoinChannel：加入频道
        kPublishAudioStream：发布音频流
        kPublishVideoStream：发布视频流
        kPublishDataStream：发布数据流
        kPublishAudiocdn：发布音频 CDN
        kPublishVideocdn：发布视频 CDN
        kInvitePublishAudioStream：邀请发布音频流 [1]
        kInvitePublishVideoStream：邀请发布视频流 [1]
        kInvitePublishDataStream：邀请发布数据流 [1]
         *
        SubscriberPrivileges：订阅者权限
        kJoinChannel：加入频道
        kRequestPublishAudioStream：申请发布音频流 [1]
        kRequestPublishVideoStream：申请发布视频流 [1]
        kRequestPublishDataStream：申请发布数据流 [1]
         *
        AdminPrivileges：管理员权限
        kJoinChannel：加入频道
        kPublishAudioStream：发布音频流
        kPublishVideoStream：发布视频流
        kPublishDataStream：发布数据流
        kAdministrateChannel：管理频道
         */
        $builder = AccessToken::init(static::$appid, static::$secret, $channel, $uid);
        $builder->addPrivilege(AccessToken::Privileges["kJoinChannel"], $expireTimestamp);
        return $builder->build();
    }


    /**
     * 简单token生成
     * @param $channel
     * @param $uid
     * @param $role
     * @param int $expireTimestamp
     * @return string
     */
    public function simpleToken($channel,$uid,$role=2,$expireTimestamp = 0)
    {
        switch ($role) {
            case 0:
                $role = 'kRoleAttendee';//通信场景下参与通话的各方
                break;
            case 1:
                $role = 'kRolePublisher';//直播场景下能发布音视频流的 Publisher
                break;
            case 2:
                $role = 'kRoleSubscriber';//直播场景下能订阅音视频流的 Subscriber
                break;
            case 101:
                $role = 'kRoleAdmin';//通信及直播场景下的管理员
                break;
            default:
                $role = 'kRoleSubscriber';
                break;
        }
        $builder = new SimpleTokenBuilder(static::$appid, static::$secret, $channel, $uid);
        $builder->initPrivilege(SimpleTokenBuilder::Role[$role]);
        return $builder->buildToken();
    }
}