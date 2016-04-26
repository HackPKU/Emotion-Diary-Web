## API 说明

### 总体说明

* 所有请求均为 HTTP 的 POST 请求
* 所有请求均需含有 `version` 字段，表明客户端的版本
* 除了注册、登录和特殊说明的情况之外，所有请求均需含有 `userid` 和 `token` 字段，以进行身份的验证
* 返回码 `code` 为 `0` 时表示请求成功，`data` 字段为返回的数据，否则返回一个对应的错误信息 `message`
* 所有情况下，以下错误码对应固定的含义：
 * `-110` 未知错误
 * `-100` 参数非法
 * `-50` 服务器错误
 * `-10` 客户端版本不支持
 * `-5` 未登录
 * `-1` 参数缺失

### 注册

###### 网址

* `/api/register.php`

###### 参数

* `name` 用户名，不能超过 32 字节，不允许含有任何特殊字符
* `password` 密码，需经过 MD5 加密，大小写编码均可
* `sex` 性别，其值为 `male` `female` 之一，可为空，为空表示保密
* `email` 邮箱，可为空，不能超过 128 字节
* `icon` 头像文件名，可为空
* `type` 表明客户端的平台，其值为 `ios` `android` `web` 之一

###### 返回

* `userid` 用户 ID，以后每次请求均需要此 ID
* `token` 用户登录凭据，以后每次请求均需要此 token

### 登录

###### 网址

* `/api/login.php`

###### 参数

* `name`、`email` 二选一，参考注册 API 说明
* `password` 参考注册 API 说明
* `type` 参考注册 API 说明

###### 返回

* 参考注册 API 说明

### 发布日记

###### 网址

* `/api/post_diary.php`

###### 参数

* `emotion` 情绪值，范围是 0 ~ 100
* `selfie` 自拍像文件名，可为空
* `images` 图片文件名序列，以 ` | ` 作为分隔符（例如 `001 | 002 | 003` ），可为空
* `tags` 标签序列，以 ` | ` 作为分隔符，可为空
* `text` 日记正文，字符串
* `location_name` 地点名称，字符串，可为空
* `location_long` 地点经度，浮点数，范围 -180 ~ 180，可为空
* `location_lat` 地点纬度，浮点数，范围 -90 ~ 90，可为空
* `weather` 天气，字符串，可为空

###### 返回

* `diaryid` 日记的 ID，供以后查询使用

### 查看日记

###### 网址

* `/api/view_diary.php`

###### 参数

* `diaryid`、`share_key` 二选一，如果使用 `share_key` 作为参数，该请求不需要 `userid` 和 `token` 字段

###### 返回

* `emotion` 情绪值
* `selfie` 自拍像文件名
* `images` 图片文件名数组
* `tags` 标签数组
* `text` 日记正文
* `location_name` 地点名称
* `location_long` 地点经度
* `location_lat` 地点纬度
* `weather` 天气
* `create_time` 创建日期
* `edit_time` 修改日期

### 分享日记

###### 网址

* `/api/share_diary.php`

###### 参数

* `diaryid` 日记 ID

###### 返回

* `share_key` 分享秘钥，有此秘钥可不登陆查看日记，参考查看日记 API 说明

### 取消分享日记

###### 网址

* `/api/unshare_diary.php`

###### 参数

* `diaryid` 日记 ID

###### 返回

* 无