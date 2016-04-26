# Emotion-Diary-Web

Emotion Diary is a lightweight personal diary APP focused on privacy and convenience. Based on the technology of face identification, users can use their face as the key to open the APP. At the same time the smile on your face can also be detected and used as the realtime emotional information, which will be part of their diary notes. After days of recording, you can review your statistics of your emotions, as well as your meaningful life.

## 环境要求

* LAMP 或者 WAMP
* Apache 版本：2.4+
* MySQL 版本：5.6+
* PHP 版本：5.5+

## 配置
**下文中请将单引号及其中的内容替换为自己定义的名称**

* 在 MySQL 中新建数据库和用户，并分配权限
 * `CREATE DATABASE 'databasename' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
 * `GRANT ALL PRIVILEGES ON 'databasename'.* TO 'username'@localhost IDENTIFIED BY ''password'';`
* 刷新权限
 * `FLUSH PRIVILEGES;` 
* 在数据库中运行`initialize.sql`，创建数据库
 * `USE 'databasename';`
 * `SOURCE initialize.sql;`
* 网站根目录下复制文件 `config.sample.php` 为 `config.php`
* 编辑 `config.php` 文件，输入 MySQL 用户名、密码和数据库名，完成数据库配置
* 运行 `db_connect_test.php` 以测试数据库配置是否成功
