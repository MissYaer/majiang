> majiang-swoole的运行环境要求PHP7.0以上 需安装swoole扩展

初始的目录结构如下：

~~~
www  WEB部署目录（或者子目录）
├─Service               应用服务目录
│  ├─command.php        命令行工具配置文件
│  ├─common.php         公共函数文件
│  ├─config.php         公共配置文件
│  ├─route.php          路由配置文件
│  ├─tags.php           应用行为扩展定义文件
│  └─database.php       数据库配置文件
│
|-Vendor 外部库
|
├─Web                   WEB目录（对外访问目录）
│  ├─index.html         入口文件
│
~~~

## 运行方式
操作简便

运行php start.php start

访问Web目录下的index.html



