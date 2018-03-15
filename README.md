#chat  

使用swoole实现的在线IM，包括一个http服务，一个websocket服务  
功能上包括 注册，登陆，好友申请（同意拒绝），发送聊天消息，等等（参考微信PC端持续更新中）
后端使用php + swoole + mysql + redis   
前端使用 vue 全家桶 + elementUI，界面上参考微信PC端 

## Requirement

1. PHP >= 7.0
2. **[Composer](https://getcomposer.org/)**
3. **[swoole](https://www.swoole.com/)** 扩展
4. **[redis](http://pecl.php.net/package/redis)** 拓展
5. **[node.js](https://nodejs.org/en/)**(前端开发环境下需要)  
6. **[weapack](http://webpack.github.io/)**(前端开发环境下需要)  
## Start
```shell
// 启动后端服务
composer install
cd server\http 
php run.php //启动http服务
cd server\ws
php run.php //启动ws服务
// 启动前端
cd webroot
npm install webpack -g
npm -i
npm run dev  //启用客户端
```