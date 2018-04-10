# Chat
使用swoole + vue + restful api实现的在线IM，包括一个http服务，一个websocket服务  
**[demo](http://119.29.63.161)**  
测试账号: zhou 密码:123456
持续更新中
## 功能
- [x] 注册，登陆，登陆状态维持
- [x] 重复登陆处理，重复连接处理（同一浏览器打开多个）
- [x] 头像修改
- [x] 好友聊天，添加，删除好友
- [x] 群组聊天，创建，退出群组
- [x] 聊天记录保存，可上拉加载查看，删除本方聊天记录（不影响对方）
- [x] 上下线通知，未读消息通知，消息推送
- [x] 链接消息处理
- [x] 发送图片
- [ ] 发送文件
## 技术栈
- websocket 负责推送转发消息
- http 负责需要回执的相关操作
- restful api
- 依赖注入，控制反转
- redis,mysql长连接
- redis 发布订阅
- Vue.js + vuex + webpack + vue-router + es6 + element-ui
## Requirement
1. PHP >= 7.0
2. **[Composer](https://getcomposer.org/)**
3. **[swoole](https://www.swoole.com/)** 扩展
4. **[redis](http://pecl.php.net/package/redis)** 拓展
5. **[node.js](https://nodejs.org/en/)**(前端开发环境下需要)  
6. **[webpack](http://webpack.github.io/)**(前端开发环境下需要)  
## Start
导入sql.sql
修改配置redis,db配置参数
```shell
// 启动后端服务
composer install
cd server\http 
php run.php 
cd server\ws
php run.php
// 启动前端
cd webroot
npm install webpack -g
npm i
npm run dev
```