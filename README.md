

![](https://www.agora.io/cn/wp-content/uploads/2018/12/Agora-logo.png)



## Agora-Live-SDK-PHP

![](https://img.shields.io/badge/php-%5E5.3-green.svg)

### 背景

公司目前一较古老产品需要接入直播，定了声网，但其未提供PHP-SDK，而且github上已有的也是composer的包，且几乎都要求php>7.0；

1.目前的老产品不支持composer，委婉且痛苦的解决了；

2.PHP5.4的环境，跑上那些PHP7的包后 报错数量让人窒息，数个小时的尝试也没能解决完，所以放弃，决定自己再造个轮子；



### 要求

🏆**php>=5.3**



### 说明

---

**Client.php** —— 请求类

> 相信你这边集成的产品中已有了类似的类，可不比引入此新的轮子，不过要注意Agora使用的Basic HTTP验证需要CURLOPT_USERPWD，假如你这边使用的CURL类没有此，加上即可
>
> ps：此处userpwd参数生成位于 Agora.php 24

```php
if (!empty($this->options['userpwd'])) {
            curl_setopt($curl, CURLOPT_USERPWD, $this->options['userpwd']);
        }
```

**SDK：**

```php
Agora.php //Api父类，处理验证及请求

Api —— TokenBuilder.php//Token生成(权限管理)

    —— Project.php//项目管理

    —— Rule.php//规则管理(踢人等)

    —— Usage.php//用量管理
Src //具体逻辑
```



### 使用

---

```php
				$channel = '7d72365eb983485397e3e3f9d460bdda';//频道
        $uid     = 123;//用户
        /**
         * 普通权限
         */
        echo (new \Helper\Agora\Api\TokenBuilder())->token($channel,$uid);

        /**
         * 简单权限token
         */
        echo (new \Helper\Agora\Api\TokenBuilder())->simpleToken($channel,$uid,101);

        /**
         * 获取所有项目
         */
        $res = (new \Helper\Agora\Api\Project())->all();
        var_dump($res);
```



### PS

---

权限如下

```php
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
```

