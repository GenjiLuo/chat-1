<template>
    <div class="main">
        <div>
            <div>
                <div class="friend-box">
                    <div class="info">
                        <el-dropdown @command="handleCommand">
                            <span class="el-dropdown-link">
                                <div class="avatar">
                                <img :src="info.avatar">
                                </div>
                            </span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item command="avatar">修改头像</el-dropdown-item>
                                <el-dropdown-item command="loginOut">注销登陆</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <div class="action" @click="switchInterface('chat')">
                            <i class="fa fa-wechat" title="聊天" :class="{ active: visible.chatList }"></i>
                        </div>
                        <div class="action" @click="switchInterface('friend')">
                            <i class="fa fa-user-circle" title="好友" :class="{ active: visible.friendList }"></i>
                            <sup class="dot" v-show="haveNotReadApply"></sup>
                        </div>
                    </div>
                    <div class="chat-list" v-show="visible.chatList">
                        <div class="head">
                            <el-input size="small" v-model="search.chat" class="search" prefix-icon="el-icon-search"
                                      placeholder="用户名"/>
                            <span class="el-icon-plus" title="新建聊天群组" @click="showCreateGroup"></span>
                        </div>
                        <div class="list">
                            <div v-for="chat in filterChatList" :key="chat.id"
                                 @click="changeChat(chat)" :class="{current: currentChat.chat_id===chat.chat_id }">
                                <el-dropdown @command="handleDeleteChat(chat.chat_id)" v-if="parseInt(chat.type) === 0">
                                    <el-badge :value="chat.notReadNum">
                                        <img :src="chat.avatar" :class="{offline:!chat.online}">
                                    </el-badge>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item>删除聊天</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                                <el-dropdown @command="handleDeleteChat(chat.chat_id)" v-if="parseInt(chat.type) === 1">
                                    <el-badge :value="chat.notReadNum">
                                        <img src="@/assets/many.png" >
                                    </el-badge>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item>退出群组</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                                <span v-if="parseInt(chat.type) === 0">{{chat.username}}</span>
                                <span v-if="parseInt(chat.type) === 1">{{chat.group_name}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="friend-list" v-show="visible.friendList">
                        <div class="head">
                            <el-input size="small" v-model="search.friend" class="search" prefix-icon="el-icon-search"
                                      placeholder="用户名"/>
                            <span class="el-icon-plus" title="新增好友" @click="showAddFriend"></span>
                        </div>
                        <div class="list">
                            <div @click="showApplyList">
                                <img src="../assets/add.png">
                                <span>好友申请</span>
                                <sup class="dot" v-show="haveNotReadApply"></sup>
                            </div>
                            <div v-for="friend in friendList" :key="friend.id" :title="friend.username"
                                 @click="selectFriend(friend)" :class="{current: currentFriend.id===friend.id }">
                                <img :src="friend.avatar">
                                <span>{{friend.username}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="msg-box" v-show="visible.chatList">
                <div class="head">
                    <p v-if="parseInt(currentChat.type) === 0">{{currentChat.username}}</p>
                    <p v-if="parseInt(currentChat.type) === 1">{{currentChat.group_name}}</p>
                </div>
                <div class="chat-tool">
                    <div class="content" id="content">
                        <div v-for="msg in currentChat.msgList ">
                            <div v-if="msg.from_id !== info.id" class="others">
                                <img :src="currentChatAvatar(msg.from_id)" v-if="parseInt(currentChat.type) === 1"/>
                                <img :src="currentChat.avatar" v-if="parseInt(currentChat.type) === 0"/>
                                <span>{{msg.msg}}</span>
                            </div>
                            <div style="text-align: right" v-if="msg.from_id === info.id">
                                <span style="background-color: #9dea6a">{{msg.msg}}</span>
                                <img :src="info.avatar"/>
                            </div>
                        </div>
                    </div>
                    <div class="tool-list">
                        <i class="el-icon-picture" title="发送图片"></i>
                    </div>
                    <div class="input">
                        <el-input type="textarea" :rows="4" placeholder="请输入内容" v-model="msg"
                                  class="message" resize="none">
                        </el-input>
                        <el-button class="send" type="primary" @click="handleSendMsg">发送</el-button>
                    </div>
                </div>
            </div>
            <div class="friend-info" v-show="visible.friendList">
                <div v-show="friendList.length > 0">
                    <div class="title">
                        <div>
                            <p>{{currentFriend.username}}
                                <i class="fa fa-venus" style="color: pink" v-show="currentFriend.sex == 0"></i>
                                <i class="fa fa-mars" style="color: blue" v-show="currentFriend.sex == 1"></i>
                            </p>
                            <p>{{currentFriend.age}}</p>
                        </div>
                        <div>
                            <img :src="currentFriend.avatar"/>
                        </div>
                    </div>
                    <div class="action">
                        <el-button type="success" @click="handleChat(currentFriend.id)">发送消息</el-button>
                        <el-button type="danger" @click="handleDeleteFriend(currentFriend.id)">删除好友</el-button>
                    </div>
                </div>
            </div>

        </div>

        <!--用户搜索申请 start-->
        <el-dialog :visible.sync="visible.newFriend" width="300px" :show-close="false" custom-class="new-friend">
            <span slot="title">
               <el-input placeholder="请输入内容" v-model.trim="search.newFriend" class="input-with-select" size="small">
                    <el-button slot="append" icon="el-icon-search" size="mini" @click="handleSearchUser"/>
               </el-input>
            </span>
            <div class="user-box">
                <div v-for="user in userList">
                    <img :src="user.avatar">
                    <span>{{user.username}}</span>
                    <el-button :disabled="user.can_apply === false " size="mini" class="el-icon-plus" type="primary"
                               plain @click="handleAddFriend(user)"/>
                </div>
            </div>
            <span slot="footer" class="dialog-footer">
                <el-button @click="visible.newFriend = false">关 闭</el-button>
            </span>
        </el-dialog>
        <!--用户搜索申请 end-->

        <!--好友申请列表 start-->
        <!--<el-dialog :visible.sync="visible.applyList" width="400px" :show-close="false" custom-class="new-friend">-->
            <!--<span slot="title">-->
                  <!--<el-input v-model.trim="search.applyList" auto-complete="off" size="small"-->
                            <!--prefix-icon="el-icon-search" placeholder="用户名"/>-->
            <!--</span>-->
            <!--<div class="apply-box">-->
                <!--<div v-for="apply in filterApplyList">-->
                    <!--<img :src="apply.avatar">-->
                    <!--<span>{{apply.username}}</span>-->
                    <!--<span v-if="apply.reason">({{apply.reason}})</span>-->
                    <!--<el-button @click='handleReject(apply)' size="mini" class="el-icon-error" type="danger" plain-->
                               <!--v-show="parseInt(apply.status)===0"/>-->
                    <!--<el-button @click='handleAgree(apply)' size="mini" class="el-icon-success" type="primary" plain-->
                               <!--v-show="parseInt(apply.status)===0"/>-->
                    <!--<el-button :disabled="parseInt(apply.status) === 1 " size="mini" type="primary" plain-->
                               <!--v-show="parseInt(apply.status)===1">已同意-->
                    <!--</el-button>-->
                    <!--<el-button :disabled="parseInt(apply.status) === 2 " size="mini" type="danger" plain-->
                               <!--v-show="parseInt(apply.status)===2">已拒绝-->
                    <!--</el-button>-->
                <!--</div>-->
            <!--</div>-->
            <!--<span slot="footer" class="dialog-footer">-->
                <!--<el-button @click="visible.applyList = false">关闭</el-button>-->
            <!--</span>-->
        <!--</el-dialog>-->

        <edit-info :visible.sync="visible.edit" :info="info" />
        <group :visible.sync="visible.createGroup" :friendList="friendList" />
    </div>
</template>
<script>
  import {
    avatarUrl, ws, deleteChat, createChat, updateChat, friendList, createApply, deleteFriend
  } from '../api/api'
  import group from '../component/group'
  import editInfo from '../component/editInfo'
  import ApplyList from '../component/applyList'
  export default {
    components: {group, editInfo, ApplyList},
    data () {
      return {
        info: {}, // 个人信息
        msg: '',  // 聊天输入框
        search: { // 相关search
          chat: '',
          friend: '',
          newFriend: ''
        },
        currentChat: {  // 当前聊天对象
        },
        visible: {
          edit: false,
          addFriend: false,
          chatList: true,
          friendList: false,
          newFriend: false,
          applyList: false,
          createGroup: false
        },
        friendList: [], // 好友列表
        chatList: [],  // 聊天列表
        userList: [],  // 用户列表
        currentFriend: { // 当前好友
          id: '',
          username: '',
          avatar: '',
          age: '',
          sex: ''
        },
        socket: '',
        isConnect: false,
        haveNotReadApply: false
      }
    },
    updated () {
      // 消息框移到最底部
      let content = document.getElementById('content')
      content.scrollTop = content.scrollHeight
    },
    computed: {
      action () {
        let token = localStorage.getItem('token')
        return avatarUrl + '?token=' + token
      },
      // 聊天搜索
      filterChatList () {
        if (this.search.chat !== '') {
          return this.chatList.filter((element) => {
            return element.username.indexOf(this.search.chat) !== -1
          })
        }
        return this.chatList
      }
    },
    methods: {
      // 群组聊天对象img
      currentChatAvatar (friendId) {
        for (let {id, avatar} of this.currentChat.userList) {
          if (parseInt(friendId) === parseInt(id)) {
            return avatar
          }
        }
      },
      // 显示创建群组界面
      showCreateGroup () {
        console.log(this.friendList)
        this.visible.createGroup = true
      },
      // http删除朋友
      handleDeleteFriend (id) {
        deleteFriend(id).then(res => {
          let deleteId = parseInt(id)
          if (parseInt(res.status) === 1) {
            this.handleFriendList()
            for (let [index, {id}] of this.chatList.entries()) {
              if (deleteId === parseInt(id)) {
                this.chatList.slice(index, 1)
                break
              }
            }
          }
        })
      },
      // http删除聊天
      handleDeleteChat (chatId) {
        deleteChat(chatId).then(data => {
          if (parseInt(data.status) === 1) {
            const chatId = data.chatId
            for (let [index, {chat_id}] of this.chatList.entries()) {
              if (parseInt(chat_id) === parseInt(chatId)) {
                const deleteChat = this.chatList.splice(index, 1)[0]
                if (deleteChat === this.currentChat) {
                  this.currentChat = {}
                }
                break
              }
            }
          }
        })
      },
      // 显示好友申请列表
      showApplyList () {
        this.visible.applyList = true
        // 设置好友申请已读
      },
      // 朋友详情栏点击`发送消息`触发事件
      handleChat (friendId) {
        let exist = false
        let chat
        for (let [index, {id}] of this.chatList.entries()) {
          if (parseInt(id) === parseInt(friendId)) {
            chat = this.chatList[index] // 移动到最上层
            this.switchInterface('chat')  // 切换到聊天界面
            this.changeChat(chat)    // 将当前聊天对象切换成该聊天
            exist = true
            break
          }
        }
        // 如果聊天不存在,则创建聊天请求
        if (!exist) {
          createChat({targetId: friendId}).then(res => {
            if (parseInt(res.status) === 1) {
              chat = res.chat
              this.chatList.unshift(chat)
              this.switchInterface('chat')  // 切换到聊天界面
              this.changeChat(chat)    // 将当前聊天对象切换成该聊天
            }
          })
        }
      },
      // 选择朋友
      selectFriend (friend) {
        this.currentFriend = friend
      },
      // 搜索user函数
      handleSearchUser () {
        this.send({type: 'userList', search: this.search.newFriend})
      },
      // 新增好友请求
      handleAddFriend (user) {
        this.$prompt(' ', '申请理由', {
          confirmButtonText: '确定',
          cancelButtonText: '取消'
        }).then(({value}) => {
          createApply({targetId: user.id, reason: value}).then(res => {
            if (parseInt(res.status) === 1) {
              this.$set(user, 'can_apply', false)
            }
          })
        })
      },
      // 获取朋友列表
      handleFriendList () {
        friendList({}).then(res => {
          this.friendList = res.friendList
          if (this.currentFriend.id === '' && this.friendList.length > 0) {
            this.selectFriend(this.friendList[0])
          }
        })
      },
      // 切换界面
      switchInterface (type) {
        if (type === 'friend') {
          this.visible.friendList = true
          this.visible.chatList = false
        }
        if (type === 'chat') {
          this.visible.friendList = false
          this.visible.chatList = true
        }
      },
      // 头像下拉菜单事件
      handleCommand (type) {
        if (type === 'avatar') {
          this.handleShowEdit()
        }
        if (type === 'loginOut') {
          this.handleLoginOut()
        }
      },
      // 注销登陆
      handleLoginOut () {
        this.socket.close()
        localStorage.setItem('token', '')
        this.$router.push('/')
      },
      // 显示新增好友列表
      showAddFriend () {
        this.visible.newFriend = true
        this.send({type: 'userList', search: this.search.newFriend})
      },
      // webSocket发送消息
      send (msg) {
        return new Promise((resolve, reject) => {
          if (this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify(msg))
            resolve()
          } else {
            this.isConnect = false
            reject(new Error())
          }
        })
      },
      // 发送聊天消息
      handleSendMsg () {
        if (this.msg === '') {
          this.$message.error('不能发送空消息!')
          return false
        }
        if (Object.keys(this.currentChat).length === 0) {
          this.$message.error('请先选择聊天人员!')
          return false
        }
        let msg = {
          chat_id: this.currentChat.chat_id,
          msg: this.msg,
          type: 'msg',
          from_id: this.info.id,
          to_id: this.currentChat.target_id,
          is_read: 1
        }
        this.send(msg).then(() => {
          this.currentChat.msgList.push(msg)
          // 将当前用户移到列表最上面
          for (let [index, {id}] of this.chatList.entries()) {
            if (parseInt(id) === parseInt(this.currentChat.id)) {
              let friend = this.chatList.splice(index, 1)[0]
              this.chatList.unshift(friend)
              this.chatList[index].notReadNum = 0
              break
            }
          }
          this.msg = ''
        })
      },
      // 切换当前聊天对象
      changeChat (chat) {
        this.currentChat = chat
        // 消息设置为已读
        updateChat({id: chat.chat_id}).then(res => {
          if (parseInt(res.status) === 1) {
            chat.notReadNum = 0
          }
        })
      },
      // 显示编辑页面
      handleShowEdit () {
        this.editForm.username = this.info.username
        this.visible.edit = true
      },
      // 开启ws链接
      openConnect () {
        let token = localStorage.getItem('token')
        this.socket = new WebSocket(`${ws}?token=${token}`)
        this.socket.onopen = this.onConnect
        this.socket.onmessage = this.onMessage
        this.socket.onclose = this.onClose
        this.socket.onerror = this.onError
      },
      onError () {

      },
      // 链接成功事件
      onConnect (ws) {
        this.$message.success(`已成功连接到聊天服务器`)
        this.isConnect = true
      },
      // 断开连接触发函数
      onClose () {
        this.isConnect = false
      },
      // 下线处理函数
      handleGoOff (data) {
        let offlineUser
        // 取出下线的用户
        for (let [index, {id}] of this.friendList.entries()) {
          if (parseInt(id) === parseInt(data.userId)) {
            this.friendList[index].online = false
            offlineUser = this.friendList.splice(index, 1)[0]
            break
          }
        }
        // 插入到下线用户队列的最前面
        for (let [index, {online}] of this.friendList.entries()) {
          if (online === false) {
            this.friendList.splice(index, 0, offlineUser)
            this.$message.info(`${offlineUser.username}下线了`)
            break
          }
        }
      },
      // 上线处理函数
      handleGoOnline (data) {
        let onlineUser
        // 取出上线用户
        let flag = false
        for (let [index, {id}] of this.chatList.entries()) {
          if (parseInt(id) === parseInt(data.user.id)) {
            this.chatList[index].online = true
            onlineUser = this.chatList.splice(index, 1)[0]
            flag = true
            break
          }
        }
        // 插入到上线用户队列的最后面
        for (let [index, {online}] of this.friendList.entries()) {
          if (online === false) {
            if (flag) {
              this.friendList.splice(index, 0, onlineUser)
              this.$message.info(`${onlineUser.username}上线了`)
            } else {
              this.friendList.splice(index, 0, data)
            }
            break
          }
        }
      },
      // 聊天消息接受处理函数
      handleMsg (data) {
        if ('chat' in data) {
          const chat = data.chat
          chat.notReadNum += 1
          chat.msgList.push(data.msg)
          this.chatList.push(chat)
        } else {
          for (let [index, {chat_id}] of this.chatList.entries()) {
            if (parseInt(chat_id) === parseInt(data.msg.chat_id)) {
              this.chatList[index].msgList.push(data.msg)
              this.chatList[index].notReadNum += 1
              break
            }
          }
        }
      },
      // 接受消息事件
      onMessage (ws) {
        let data = JSON.parse(ws.data)
        switch (data.type) {
          case 'chatList': // 聊天列表
            this.chatList = data.chatList
            break
          case 'userList': // 用户列表
            this.userList = data.userList
            break
          case 'msg':
            this.handleMsg(data)
            break
          case 'forbidden':  // token不正确
            this.handleLoginOut()
            break
          case 'repeat':  // 被踢
            this.socket.close()
            this.$alert('你的账号已在别处登陆', '提示', {
              confirmButtonText: '确定',
              callback: () => {
                this.socket.close()
                localStorage.setItem('token', '')
//                this.$router.push('/')
              }
            })
            break
          case 'applyList':   // 好友申请列表
            this.applyList = data.applyList
            // 如果有未读的申请
            for (let index in this.applyList) {
              if (parseInt(this.applyList[index].is_read) === 0) {
                this.haveNotReadApply = true
                break
              }
            }
            break
          case 'applySucc':  // 好友申请被同意
            this.handleFriendList()
            this.$message.success('"' + data.friend.username + '"已经同意你的好友申请')
            break
          case 'newGroup': // 接受新群组
            this.chatList.push(data.group)
            break
        }
      }
    },
    mounted () {
      this.openConnect()
      this.info = JSON.parse(localStorage.getItem('user'))
      this.handleFriendList()
    },
    watch: {
      // 断开连接后的提示
      isConnect: function (newResult, oldResult) {
        if (newResult === false && this.$router.path === '/chat') {
          this.$confirm('聊天服务器已断开', '提示', {
            confirmButtonText: '重新连接',
            cancelButtonText: '取消',
            type: 'error'
          }).then(() => {
            this.openConnect()
          }).catch(() => {
            localStorage.setItem('token', '')
            this.$router.push('/')
          })
        }
      }
    }
  }
</script>
<style scoped rel="stylesheet/sass" lang="sass">
    $maxHeight: 600px
    $headHeight: 50px
    $imageSize: 40px
    .main
        margin-top: 100px
        text-align: center
        min-width: 1366px
        & > div
            margin: 0 auto
            display: inline-block
            & > div:first-child
                width: 260px
                display: inline-block
                float: left
            & > div:nth-child(n+2)
                width: 600px
                display: inline-block
                vertical-align: top
    .friend-box
        border-left: 1px solid #D8DCE5
        border-top: 1px solid #D8DCE5
        border-bottom: 1px solid #D8DCE5
        height: $maxHeight
        border-right: none
        .info
            width: 59px
            height: 100%
            display: inline-block
            float: left
            a
                color: #909399
            div
                height: 58px
                line-height: 58px
                font-size: 20px
            .avatar
                height: 58px
                img
                    margin: 10px
                    width: $imageSize
                    height: $imageSize
                    cursor: pointer
                    border-radius: 5px
            .action
                color: #909399
                position: relative
                .dot
                    position: absolute
                    top: 15px
                    left: 15px
                    background-color: #fa5555
                    border-radius: 10px
                    color: #fff
                    display: inline-block
                    font-size: 12px
                    height: 8px
                    width: 8px
                    text-align: center
                    white-space: nowrap
                    border: 1px solid #fff
                &:hover
                    cursor: pointer
                    background-color: #f5f5f5

            .active
                color: #67C23A !important
        .chat-list
            background-color: #e7e6e6
            width: 200px
            float: right
            height: 100%
            display: inline-block
            .head
                font-size: 18px
                text-align: left
                height: $headHeight
                border-bottom: 1px solid #D8DCE5
                .search
                    margin: 10px 10px
                    width: 70%
                span
                    &:hover
                        cursor: pointer
            .list
                .current
                    background-color: #c6c5c5
                overflow: auto
                height: 530px
                text-align: left
                > div
                    position: relative
                    padding: 5px 0px 5px 15px
                    height: 40px
                    line-height: 40px
                    font-size: 15px
                    margin: 0
                    cursor: pointer
                    img
                        width: $imageSize
                        height: $imageSize
                        border-radius: 5px
                        vertical-align: top
                    span
                        display: inline-block
                        vertical-align: top
                        width: 60%
                        overflow: hidden
                        text-align: left
                    &:hover
                        background-color: #c6c6c6
                    .offline
                        filter: grayscale(100%)
        .friend-list
            background-color: #e7e6e6
            width: 200px
            float: right
            height: 100%
            display: inline-block
            .head
                font-size: 18px
                text-align: left
                height: $headHeight
                border-bottom: 1px solid #D8DCE5
                .search
                    margin: 10px 10px
                    width: 70%
                span
                    &:hover
                        cursor: pointer
            .list
                .dot
                    position: absolute
                    top: 2px
                    left: 12px
                    background-color: #fa5555
                    border-radius: 10px
                    color: #fff
                    display: inline-block
                    font-size: 12px
                    height: 8px
                    width: 8px
                    text-align: center
                    white-space: nowrap
                    border: 1px solid #fff
                > div:nth-child(1)
                    border-bottom: 2px solid #DCDFE6
                .current
                    background-color: #c6c5c5
                > div
                    text-align: left
                    position: relative
                    padding: 5px 0px 5px 15px
                    height: 40px
                    line-height: 40px
                    font-size: 15px
                    margin: 0
                    cursor: pointer
                    img
                        width: $imageSize
                        height: $imageSize
                        border-radius: 5px
                        vertical-align: top
                    span
                        display: inline-block
                        vertical-align: top
                        width: 60%
                        overflow: hidden
                        text-align: left
                    &:hover
                        background-color: #c6c6c6

    .user-content
        margin-top: -30px
        height: 500px
        overflow: auto
        div
            padding: 5px
            height: 40px
            vertical-align: top
            text-align: left
            img
                width: $imageSize
                height: $imageSize
            span
                display: inline-block
                line-height: 40px
                font-size: 20px
                vertical-align: top
                padding-left: 10px
            button
                vertical-align: top
                float: right
                margin-top: 3px

        .item
            margin: 0px
    .msg-box
        border: 1px solid #D8DCE5
        height: $maxHeight
        .head
            background-color: #f5f5f5
            height: $headHeight
            border-bottom: 1px solid #D8DCE5
            p
                height: $headHeight
                line-height: $headHeight
                margin: 0
                padding: 0 20px
        .chat-tool
            .content
                background-color: #f5f5f5
                overflow: auto
                height: 340px
                text-align: left
                div
                    margin: 10px 10px
                    min-height: 50px
                img
                    width: $imageSize
                    height: $imageSize
                    margin-top: 5px
                    border-radius: 5px
                span
                    background-color: #ffffff
                    display: inline-block
                    vertical-align: top
                    line-height: 25px
                    max-width: 200px
                    min-height: 30px
                    margin-top: 5px
                    padding: 5px
                    word-break: break-all
                    word-wrap: break-word
                    text-align: left
                    border-radius: 5px

            .input
                height: 150px
                padding: 0px 20px 20px 20px
                .send
                    float: right
                    margin-top: 10px
                    width: 100px
                .message
                    overflow: auto

            .tool-list
                border-top: 1px solid #D8DCE5
                height: 25px
                text-align: right
                padding: 5px 30px 5px 0
                i
                    &:hover
                        cursor: pointer

    .friend-info
        border: 1px solid #D8DCE5
        height: $maxHeight
        background-color: #f5f5f5
        > div
            width: 80%
            margin: 0 10%
        .title
            margin-top: 100px
            padding-bottom: 30px
            border-bottom: 1px solid #DCDFE6
            div:nth-child(1)
                padding-top: 10px
                display: inline-block
                width: 40%
                vertical-align: top
                i
                    margin-left: 5px
                p
                    margin-top: 0px
                    margin-bottom: 5px
            div:nth-child(2)
                display: inline-block
                width: 40%
                img
                    width: 70px
                    height: 70px
                    border-radius: 10px
        .action
            margin-top: 50px
            button:nth-child(2)
                margin-left: 100px

    .new-friend
        input
            width: 200px
        .user-box
            overflow: auto
            div
                height: 40px
                line-height: 40px
                text-align: left
                margin: 8px 0 5px
                img
                    border-radius: 5px
                    height: $imageSize
                    width: $imageSize
                span
                    height: 30px
                    display: inline-block
                    vertical-align: top
                    margin-left: 10px
                button
                    vertical-align: top
                    float: right
                    margin-top: 6px
                    margin-left: 10px
        .apply-box
            overflow: auto
            div
                height: 40px
                line-height: 40px
                text-align: left
                margin: 8px 0 5px
                img
                    border-radius: 5px
                    height: $imageSize
                    width: $imageSize
                span:first-child
                    font-size: 25px
                span
                    height: 40px
                    line-height: 40px
                    display: inline-block
                    vertical-align: top
                    margin-left: 10px

                button
                    vertical-align: top
                    float: right
                    margin-top: 6px
                    margin-left: 10px


</style>