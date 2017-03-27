# 游戏兜平台接入php-SDK

- 你在阅读本文之前确认你已经仔细阅读了： [游戏兜平台接入文档](https://github.com/youxidou/doc/blob/master/development_doc.md)

### 配置

```php

<?php

use Yxd\Game\Foundation\Application;

$config = [
    'app_key'    => 'aserfasfe',
    'app_secret' => 'asdfawefasfasefas',
    'notify_url' => 'http://www.baidu.com',
];

$app = new Application($config);

```

### 获取用户信息

```php
$app->user->get()->all();
```

### 创建订单

```php

$attributes = [
    'open_id'       => '840fc54345471c718a131f93e36b8269',
    'money'         => '0.01',
    'game_order_no' => time(),
    'title'         => 'aaaaaaa',
    'description'   => 'bbbbbbb',
];
$order = new Order($attributes);

```

### 统一下单接口

```php

$result = $app->payment->prepare($order);
if ($result->result_code == 'SUCCESS'){
    $prepay_id = $result->prepay_id;
}
```


### 生成支付 JS 配置

```
$json = $payment->configForPay($prepay_id); // 返回 json 字符串，如果想返回数组，传第二个参数 false
```

### 支付结果通知

- 在用户成功支付后，游戏兜服务器会向该订单中设置的回调notify_url发起一个 POST 请求

- 接口调用:

```php
$response = $app->payment->handleNotify(function($notify, $successful){
    // 你的逻辑
    return true; // 或者错误消息
});
return $response;
```

- 处理逻辑大概是下面这样（以下只是伪代码）：

```php
$response = $app->payment->handleNotify(function($notify, $successful){

	// 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
    $order = 查询订单($notify->game_order_no); 
    if (!$order) { // 如果订单不存在
        return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
    }
    // 如果订单存在
    // 检查订单是否已经更新过支付状态
    if ($order->paid_at) { // 假设订单字段“支付时间”不为空代表已经支付
        return true; // 已经支付成功了就不再更新了
    }
    // 用户是否支付成功
    if ($successful) {
        // 不是已经支付状态则修改为已经支付状态
        $order->paid_at = time(); // 更新支付时间为当前时间
        $order->status = 'paid';
    } else { // 用户支付失败
        $order->status = 'paid_fail';
    }
    $order->save(); // 保存订单
    return true; // 返回处理完成
});	

return $response;
``