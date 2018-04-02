<template>
    <div class="main" >
        <div>
            <div class="left-content">
                <div class="friend-box">
                    <div class="info">
                        <el-dropdown @command="handleCommand">
                            <span class="el-dropdown-link">
                                <div class="avatar">
                                <img :src="avatar">
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
                            <span class="el-icon-plus" title="新建聊天群组" @click="visible.createGroup = true"></span>
                        </div>
                        <div class="list">
                            <div v-for="chat in filterChatList" :key="chat.id"
                                 @click="changeChat(chat)" :class="{current: currentChat.chat_id===chat.chat_id }">
                                <el-dropdown @command="handleChatComment" v-if="parseInt(chat.type) === 0">
                                    <el-badge :value="chat.notReadNum">
                                        <img :src="chat.avatar" :class="{offline:!chat.online}">
                                    </el-badge>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item :command="{type: 'delete', data: chat.chat_id}">删除聊天</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                                <el-dropdown @command="handleChatComment" v-if="parseInt(chat.type) === 1">
                                    <el-badge :value="chat.notReadNum">
                                        <img src="@/assets/many.png" >
                                    </el-badge>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item :command="{type: 'userList', data: chat.userList}">群组人员</el-dropdown-item>
                                        <el-dropdown-item :command="{type: 'delete', data: chat.chat_id}">退出群组</el-dropdown-item>
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
                            <span class="el-icon-plus" title="新增好友" @click="visible.newFriend = true"></span>
                        </div>
                        <div class="list">
                            <div @click="visible.applyList = true">
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
            <div class="msg-box right-content" v-show="visible.chatList">
                <div class="head">
                    <p v-if="parseInt(currentChat.type) === 0">{{currentChat.username}}</p>
                    <p v-if="parseInt(currentChat.type) === 1" title="">{{currentChat.group_name}}
                        (<span v-for="user,index in currentChat.userList" v-if="user.username !== info.username">{{user.username}}<span v-if="index < currentChat.userList.length - 2">,</span>
                        </span>)
                    </p>
                </div>
                <div class="chat-tool">
                    <div class="content" id="content" @scroll="handleScroll">
                        <p class="load-msg-notice" v-show="visible.loadingMessage">-----------加载聊天记录中-----------</p>
                        <p class="load-msg-notice"  v-show="currentChat.noMsg">-----------暂无更多消息-----------</p>
                        <div v-for="msg in currentChat.msgList ">
                            <div v-if="msg.from_id !== info.id" class="others">
                                <img :src="msg.avatar">
                                <span v-html="linkMsg(msg.msg)" v-if="isLink(msg.msg)"></span>
                                <span  v-if="!isLink(msg.msg)">{{msg.msg}}</span>
                            </div>
                            <div style="text-align: right" v-if="msg.from_id === info.id">
                                <span style="background-color: #9dea6a" v-html="linkMsg(msg.msg)" v-if="isLink(msg.msg)"></span>
                                <span style="background-color: #9dea6a"  v-if="!isLink(msg.msg)">{{msg.msg}}</span>
                                <img :src="msg.avatar"/>
                            </div>
                        </div>
                    </div>
                    <div class="tool-list">
                        <i class="el-icon-picture" title="发送图片" @click="handleUploadFile"></i>
                    </div>
                    <div class="input" @keyup.alt.83="handleSendMsg">
                        <el-input type="textarea" :rows="4" placeholder="请输入内容" v-model="msg"
                                  class="message" resize="none" >
                        </el-input>
                        <el-button class="send" type="primary" @click="handleSendMsg">发送(alt + s)</el-button>
                    </div>
                </div>
            </div>
            <div class="friend-info right-content" v-show="visible.friendList">
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
                        <el-button type="success" @click="handleChat(currentFriend.id)" >发送消息</el-button>
                        <el-button type="danger" @click="handleDeleteFriend(currentFriend.id)">删除好友</el-button>
                    </div>
                </div>
            </div>
            <div class="file-upload">
                <file-upload ref="uploadFile" :file-list="fileList" />
            </div>
        </div>
        <el-dialog title="错误" :visible.sync="visible.repeatConnect" width="400px" center
                   :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
            <span><i class="el-icon-error"></i>只允许打开一个聊天窗口</span>
        </el-dialog>

        <user-list  :visible.sync="visible.newFriend" />
        <apply-list :visible.sync="visible.applyList" :not-read-apply.sync="haveNotReadApply" @handleFriendList="handleFriendList"/>
        <edit-info :visible.sync="visible.edit" />
        <group :visible.sync="visible.createGroup" :friend-list="friendList" />
        <group-user :visible.sync="visible.groupUser" :list="groupUserList" :friend-list="friendList" />
    </div>
</template>
<script>
  import {
    ws, deleteChat, createChat, updateChat, friendList, deleteFriend, messageList
  } from '../api/api'
  import {getNowFormatDate, validateURL} from '../utils/tool'
  import group from '../component/group'
  import editInfo from '../component/editInfo'
  import applyList from '../component/applyList'
  import userList from '../component/userList'
  import groupUser from '../component/groupUser'
  import fileUpload from '../component/fileUpload'
  export default {
    components: {group, editInfo, applyList, userList, groupUser, fileUpload},
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
          repeatConnect: false,
          edit: false,
          addFriend: false,
          chatList: true,
          friendList: false,
          newFriend: false,
          applyList: false,
          createGroup: false,
          reConnect: true,
          groupUser: false,
          loadingMessage: false
        },
        friendList: [], // 好友列表
        chatList: [],  // 聊天列表
        userList: [],  // 用户列表
        groupUserList: [],
        currentFriend: { // 当前好友
          id: '',
          username: '',
          avatar: '',
          age: '',
          sex: ''
        },
        socket: '',
        isConnect: false,
        haveNotReadApply: false,
        getMsgFlag: true,
        fileList: []
      }
    },
    updated () {
      // 消息框滚动条处理
      let toBottom
      if (!this.currentChat.scrollBottom) {
        toBottom = 0
      } else {
        toBottom = this.currentChat.scrollBottom
      }
      let content = document.getElementById('content')
      content.scrollTop = content.scrollHeight - toBottom
    },
    computed: {

      // 获取头像
      avatar () {
        return this.$store.state.info.avatar
      },
      // 聊天搜索,排序
      filterChatList () {
        this.chatList = this.chatList.sort((a, b) => {
          if (a.online > b.online) {
            return -1
          } else if (a.online === b.online) {
            if (a.last_chat_time > b.last_chat_time) {
              return -1
            } else {
              return 1
            }
          } else {
            return 1
          }
        })
        if (this.search.chat !== '') {
          return this.chatList.filter((element) => {
            if (element.username) {
              return element.username.indexOf(this.search.chat) !== -1
            } else {
              return element.group_name.indexOf(this.search.chat) !== -1
            }
          })
        }
        return this.chatList
      }
    },
    methods: {
      handleUploadFile () {
        this.$refs.uploadFile.upload()
      },
      // scroll触发函数，记录每个聊天框滚动条的滚动位置
      handleScroll () {
        let doc = document.getElementById('content')
        this.currentChat.scrollBottom = doc.scrollHeight - doc.scrollTop
        if (doc.scrollTop < 10) {
          this.handleMessageList(this.currentChat)
        }
      },
      //  获取聊天记录
      handleMessageList (chat) {
        if (chat.noMsg !== true && this.getMsgFlag === true) {
          this.visible.loadingMessage = true
          this.getMsgFlag = false
          chat.page += 1
          let params = {
            page: chat.page,
            id: chat.chat_id,
            time: chat.getMsgTime
          }
          messageList(params).then(res => {
            this.visible.loadingMessage = false
            if (parseInt(res.status) === 1) {
              if (res.msgList.length === 0) {
                this.$set(chat, 'noMsg', true)
              } else {
                chat.msgList = res.msgList.concat(chat.msgList)
              }
            }
            this.getMsgFlag = true
          })
        }
      },

      // 判断消息是否为url
      isLink (msg) {
        return validateURL(msg)
      },
      // 消息内容url化
      linkMsg (msg) {
        return `<a href="${msg}" target="_blank">${msg}</a>`
      },
      // http删除朋友
      handleDeleteFriend (id) {
        this.$confirm('删除好友也会删除与该好友的聊天记录, 是否继续?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          deleteFriend(id).then(res => {
            let deleteId = parseInt(id)
            if (parseInt(res.status) === 1) {
              this.handleFriendList()
              for (let [index, {id}] of this.chatList.entries()) {
                if (deleteId === parseInt(id)) {
                  this.chatList.splice(index, 1)
                  break
                }
              }
            }
          })
        }).catch(() => {})
      },
      // http删除聊天
      handleChatComment (object) {
        if (object.type === 'delete') {
          deleteChat(object.data).then(data => {
            if (parseInt(data.status) === 1) {
              const chatId = data.chatId
              let index = this.chatList.findIndex(element => parseInt(element.chat_id) === parseInt(chatId))
              const deleteChat = this.chatList.splice(index, 1)[0]
              if (deleteChat === this.currentChat) {
                this.currentChat = {}
              }
            }
          })
        }
        if (object.type === 'userList') {
          this.groupUserList = object.data
          this.visible.groupUser = true
        }
      },
      // 朋友详情栏点击`发送消息`触发事件
      handleChat (friendId) {
        let exist = false
        let chat = this.chatList.find(e => parseInt(e.id) === parseInt(friendId))
        if (chat) {
          this.switchInterface('chat')  // 切换到聊天界面
          this.changeChat(chat)    // 将当前聊天对象切换成该聊天
          exist = true
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
          this.visible.edit = true
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
          is_read: 1,
          avatar: this.avatar
        }
        this.send(msg).then(() => {
          this.currentChat.msgList.push(msg)
          this.currentChat.last_chat_time = getNowFormatDate()
          this.msg = ''
          if (this.currentChat.notReadNum > 0) {
            updateChat({id: this.currentChat.chat_id}).then(res => {
              if (parseInt(res.status) === 1) {
                this.currentChat.notReadNum = 0
              }
            })
          }
        })
      },
      // 切换当前聊天对象
      changeChat (chat) {
        this.currentChat = chat
        // 消息设置为已读
        if (chat.notReadNum > 0) {
          updateChat({id: chat.chat_id}).then(res => {
            if (parseInt(res.status) === 1) {
              chat.notReadNum = 0
            }
          })
        }
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
        this.$confirm('聊天服务器连接失败', '提示', {
          confirmButtonText: '重新连接',
          cancelButtonText: '取消',
          type: 'error'
        }).then(() => {
          this.openConnect()
        }).catch(() => {
          localStorage.setItem('token', '')
          this.$router.push('/')
        })
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
      // 聊天消息接受处理函数
      handleMsg (data) {
        if ('chat' in data) {
          const chat = data.chat
          chat.notReadNum += 1
          chat.msgList.push(data.msg)
          this.chatList.unshift(chat)
        } else {
          let chat = this.chatList.find(element => parseInt(element.chat_id) === parseInt(data.msg.chat_id))
          chat.msgList.push(data.msg)
          chat.last_chat_time = data.msg.time
          chat.notReadNum += 1
        }
      },
      // 接受消息事件
      onMessage (ws) {
        let data = JSON.parse(ws.data)
        switch (data.type) {
          case 'chatList': // 聊天列表
            this.chatList = data.chatList
            break
          case 'msg':
            this.handleMsg(data)
            break
          case 'forbidden':  // token不正确
            this.handleLoginOut()
            break
          case 'repeat':  // 被踢
            this.visible.reConnect = false
            this.socket.close()
            localStorage.setItem('token', '')
            this.$alert('你的账号已在别处登陆', '提示', {
              confirmButtonText: '确定',
              callback: () => {
                this.$router.push('/')
              }
            })
            break
          case 'newApply':   // 有新的好友申请
            this.haveNotReadApply = true
            break
          case 'applySucc':  // 好友申请被同意
            this.handleFriendList()
            this.$message.success('"' + data.friend.username + '"已经同意你的好友申请')
            break
          case 'newGroup': // 接受新群组
            this.chatList.unshift(data.group)
            break
          case 'repeatConnect': // 重复连接
            this.socket.close()
            this.visible.repeatConnect = true
            this.visible.reConnect = false
            break
          case 'goOnline':  // 上线消息
            let chat = this.chatList.find(element => parseInt(element.chat_id) === parseInt(data.chatId))
            chat.online = true
            break
          case 'goOffLine':  // 下线消息
            chat = this.chatList.find(element => parseInt(element.chat_id) === parseInt(data.chatId))
            chat.online = false
            break
          case 'quitGroup':   // 退出群组消息
            let chatId = data.chatId
            let userId = data.userId
            chat = this.chatList.find((element) => parseInt(element.chat_id) === parseInt(chatId))
            let index = chat.userList.findIndex(element => parseInt(element.id) === parseInt(userId))
            let user = chat.userList.splice(index, 1)
            this.$message.success(`<${user.username}>退出了群组<${chat.group_name}>`)
            break
        }
      }
    },
    mounted () {
      this.openConnect()
      this.info = this.$store.state.info
      this.handleFriendList()
    },
    watch: {
      isConnect: function (newResult, oldResult) {
        if (newResult === false && this.$route.path === '/chat' && this.visible.reConnect === true) {
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
            .left-content
                width: 260px
                display: inline-block
                float: left
            .right-content
                width: 600px
                display: inline-block
                vertical-align: top
            .file-upload
                vertical-align: top
                width: 200px
                display: inline-block
                float: right
                border-top: 1px solid #D8DCE5
                border-right: 1px solid #D8DCE5
                border-bottom: 1px solid #D8DCE5
                height: 600px
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
                overflow: hidden
                white-space: nowrap
                text-overflow: ellipsis
                span

                    overflow: hidden
        .chat-tool
            .content
                background-color: #f5f5f5
                overflow: auto
                height: 340px
                text-align: left
                .load-msg-notice
                    text-align: center
                    color: #909399
                    margin: 10px 50px
                    font-size: 16px
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
                    /*width: 100px*/
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

</style>