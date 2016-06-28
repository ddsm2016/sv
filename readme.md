# 短信验证码类

> 通过Composer `ddsm2016/sv` 包可以获取该类库

## 依赖模块/类库
1. 该类依赖ddsm2016/ddsm类库包，以提供短信发送服务
2. 该类依赖特定结构的Eloquent模型，用于保存验证码相关状态信息

## Eloquent模型表结构

| 序号 | 字段名 | 中文名 | 类型及长度 | 备注 |
| :---: | :---: | --- | --- | --- |
| 1 | id | ID | varchar(255) |  主键 |
| 2 | code | 验证码 | varchar(8) | 验证码 最多长度8位 |
| 3 | dayget | 当天已获取次数 | tinyint | 无符号 |
| 4 | verifyed | 此验证码已验证次数 | tinyint | 无符号 |
| 5 | created_at | 验证码创建时间 | datetime | - |
| 6 | updated_at | 验证码记录更新时间 | datetime | - |

**说明**
- ID用于标识某条特定的短信验证码记录

**Laravel Migration字段定义**

```php
$table->string('id');
$table->string('code', 8);
$table->tinyInteger('dayget', false, true)->default(1);
$table->tinyInteger('verifyed', false, true)->default(0);
$table->timestamps();
// 主键定义
$table->primary('id');
```

## 初始化配置项

```php
$config = [
	'expire_time' => 1800,  // 验证码有效期（秒）
	'get_max'     => 3, // 每天最多可获取验证码次数
	'verify_max'  => 3, // 每条验证码最大可试错次数
];
```

## 初始化类实例

```php
$sv = SmsVerify::instance($id);  // 设置验证码记录ID，返回当前对象$this

$sv->config($config); // 设置配置项，返回当前对象$this

$sv->model($model);  // $model为短信验证码Eloquent模型，返回当前对象$this
```

## 方法清单

- 发送短信验证码
- 短信验证码验证
- 获取最后产生错误信息

### 发送短信验证码

```php
/**
 * 发送短信验证码
 * @param string $phone 接收验证码手机号码
 * @return bool
 */
$sv->sms($sms)->content($template)->send($phone);
```

- 如果验证码发送失败，可通过`$sv->error()`获取错误信息
- `sms`方法用于设定短信发送类实例
- `content`方法用于订制验证码短信模板，%s表示验证码占位符

### 短信验证码验证

```php
/**
 * 短信验证码验证
 * @param string $inputCode 粉丝输入待验证的验证码
 * @return bool
 */
$sv->verify($inputCode);
```

> 如果验证码验证失败，可通过$sv->error()获取错误信息

### 获取最后产生错误信息

```php
$sv->error();  // 用于获取最后一次产生的错误信息 
```


