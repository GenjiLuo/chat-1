基于swoole,redis,vue的在线im练习项目  
=== 
包括一个swoole实现的http小框架，一个swoole实现的websocket小框架  
欢迎学习相互交流  
先执行sql.sql导入数据库  
在根目录下
```
composer install
cd server\http 
php run.php //启动http服务
cd server\ws
php run.php //启动ws服务

cd webroot
npm install webpack -g
npm 
npm run dev  //启用客户端
```