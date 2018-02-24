<template>
    <div class="chat">
        <div>
            <div >
                <div class="friend-box">
                    <div class="info">
                        <div class="avatar" @click="handleShowEdit">
                            <img :src="info.avatar" title="修改个人信息"/>
                        </div>
                        <div>
                            <a @click.prevent="showAddFriend" href="#" title="新增好友"><i class="el-icon-plus" title="新增好友"></i></a>
                        </div>
                    </div>
                    <div class="friend">
                        <div class="head">
                            <el-input size="small" v-model="search" class="search"  prefix-icon="el-icon-search" placeholder="昵称"></el-input>
                        </div>
                        <div class="list">
                            <div v-for="friend in filterFriends" :key="friend.id" :title="friend.nickname" @click="changeChat(friend)" :class="{current: currentChat.id===friend.id }">
                                <img :src="friend.avatar" :class="{offline:!friend.online}">
                                <span>{{friend.nickname}}</span>
                                <sup class="dot" v-if="friend.getNew===true"></sup>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div >
                <div class="msg-box">
                    <div class="head">
                        <p>{{currentChat.nickname}}</p>
                    </div>
                    <div class="content" id="content">
                        <div  v-for="msg in currentChat.msgList ">
                            <div v-if="msg.from===currentChat.id" class="others">
                                <img :src="msg.avatar" />
                                <span>{{msg.msg}}</span>
                            </div>
                            <div style="text-align: right"  v-if="msg.from !== currentChat.id">
                                <span style="background-color: #9dea6a">{{msg.msg}}</span>
                                <img :src="msg.avatar" />
                            </div>
                        </div>
                    </div>
                    <div class="input">
                        <el-input type="textarea" :rows="4" placeholder="请输入内容" v-model="msg"
                                      class="message" resize="none"	>
                        </el-input>
                        <el-button class="send" type="primary" @click="handleSendMsg"  >发送</el-button>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog title="修改头像" :visible.sync="visible.edit" width="218px" class="dialog">
            <el-upload class="avatar-uploader"
                    :action="action"
                    :show-file-list="false"
                    :on-success="handleAvatarSuccess"
                    :before-upload="beforeAvatarUpload">
                <img v-if="info.avatar" :src="info.avatar" class="avatar">
                <i  class="el-icon-plus avatar-uploader-icon" v-else></i>
            </el-upload>
        </el-dialog>
        <el-dialog  :visible.sync="visible.addFriend" width="300px" :show-close="false" class="add-friend">
            <span slot="title">
                 <el-input size="small" @blur="handleGetUserList" v-model="userSearch"  prefix-icon="el-icon-search" placeholder="昵称"></el-input>
            </span>
            <div class="user-content">
                <div v-for="user in userList" >
                    <img :src="user.avatar">
                    <span>{{user.nickname}}</span>
                    <el-button size="small" icon="el-icon-plus" ></el-button>
                </div>
            </div>
        </el-dialog >
    </div>
</template>
<script>
  import {avatarUrl, ws} from '../api/api'
  export default {
    data () {
      return {
        info: {},
        msg: '',
        search: '',
        currentChat: {
          nickname: '',
          msgList: [],
          id: '',
          avatar: ''
        },
        visible: {
          edit: false,
          addFriend: false
        },
        friendList: [],
        chats: [],
        editForm: {
          nickname: '',
          avatar: ''
        },
        socket: '',
        userSearch: '',
        userList: '',
        isConnect: false
      }
    },
    updated () {
      let content = document.getElementById('content')
      content.scrollTop = content.scrollHeight
    },
    computed: {
      action () {
        let token = localStorage.getItem('token')
        return avatarUrl + '?token=' + token
      },
      // 朋友搜索
      filterFriends () {
        if (this.search !== '') {
          return this.friendList.filter((element) => {
            return element.nickname.indexOf(this.search) !== -1
          })
        }
        return this.friendList
      }
    },
    methods: {
      // 显示新增朋友页面
      showAddFriend () {
        this.visible.addFriend = true
        this.handleGetUserList()
      },
      // 日期格式化
      getNowFormatDate () {
        let date = new Date()
        let seperator1 = '-'
        let seperator2 = ':'
        let month = date.getMonth() + 1
        let strDate = date.getDate()
        if (month >= 1 && month <= 9) {
          month = '0' + month
        }
        if (strDate >= 0 && strDate <= 9) {
          strDate = '0' + strDate
        }
        return date.getFullYear() + seperator1 + month + seperator1 + strDate + ' ' + date.getHours() + seperator2 + date.getMinutes() + seperator2 + date.getSeconds()
      },
      // 获取用户列表
      handleGetUserList () {
        let msg = {
          type: 'userList',
          search: this.userSearch
        }
        this.socket.send(JSON.stringify(msg))
      },
      // 发送消息
      handleSendMsg () {
        if (this.msg === '') {
          this.$message.error('不能发送空消息!')
          return false
        }
        if (this.currentChat.id === '') {
          this.$message.error('请先选择聊天人员!')
          return false
        }
        let msg = {
          to: this.currentChat.id,
          from: this.info.id,
          msg: this.msg,
          type: 'msg',
          avatar: this.info.avatar,
          time: this.getNowFormatDate()
        }
        this.currentChat.msgList.push(msg)
        this.socket.send(JSON.stringify(msg))
        // 将当前用户移到列表最上面
        for (let [index, {id}] of this.friendList.entries()) {
          if (parseInt(id) === parseInt(this.currentChat.id)) {
            let friend = this.friendList.splice(index, 1)[0]
            this.friendList.unshift(friend)
            this.$set(this.friendList[index], 'getNew', false)
            break
          }
        }
        this.msg = ''
      },
      // 切换聊天对象
      changeChat (friend) {
        this.currentChat = friend
        this.$set(friend, 'getNew', false)
      },
      // 显示编辑页面
      handleShowEdit () {
        this.editForm.nickname = this.info.nickname
        this.visible.edit = true
      },
      // 头像上传成功事件
      handleAvatarSuccess (res, file) {
        this.info.avatar = res.avatar
      },

      // 头像上传验证
      beforeAvatarUpload (file) {
        const isJPG = file.type === 'image/jpeg'
        const isLt2M = file.size / 1024 / 1024 < 2
        if (!isJPG) {
          this.$message.error('上传头像图片只能是 JPG 格式!')
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!')
        }
        return isJPG && isLt2M
      },
      // 开启ws链接
      openConnect () {
        let token = localStorage.getItem('token')
        this.socket = new WebSocket(`${ws}?token=${token}`)
        this.socket.onopen = this.onConnect
        this.socket.onmessage = this.onMessage
      },
      // 链接成功事件
      onConnect (ws) {
        this.isConnect = true
      },
      //
      onClose () {
        this.isConnect = false
      },
      // 下线处理函数
      handleGoOff (data) {
        let offlineUser
        // 取出下线的用户
        for (let [index, { id }] of this.friendList.entries()) {
          if (parseInt(id) === parseInt(data.userId)) {
            this.friendList[index].online = false
            offlineUser = this.friendList.splice(index, 1)[0]
            break
          }
        }
        // 插入到下线用户队列的最前面
        for (let [index, { online }] of this.friendList.entries()) {
          if (online === false) {
            this.friendList.splice(index, 0, offlineUser)
            this.$message.info(`${offlineUser.nickname}下线了`)
            break
          }
        }
      },
      // 上线处理函数
      handleGoOnline (data) {
        let onlineUser
        // 取出上线用户
        let flag = false
        for (let [index, { id }] of this.friendList.entries()) {
          if (parseInt(id) === parseInt(data.user.id)) {
            this.friendList[index].online = true
            onlineUser = this.friendList.splice(index, 1)[0]
            flag = true
            break
          }
        }
        // 插入到上线用户队列的最后面
        for (let [index, { online }] of this.friendList.entries()) {
          if (online === false) {
            if (flag) {
              this.friendList.splice(index, 0, onlineUser)
              this.$message.info(`${onlineUser.nickname}上线了`)
            } else {
              this.friendList.splice(index, 0, data)
            }
            break
          }
        }
      },
      // 聊天消息接受处理函数
      handleMsg (data) {
        let from = parseInt(data.from)
        for (let [index, {id}] of this.friendList.entries()) {
          if (parseInt(id) === from) {
            this.friendList[index].msgList.push(data)
            this.$set(this.friendList[index], 'getNew', true)
            break
          }
        }
      },
      // 接受消息事件
      onMessage (ws) {
        let data = JSON.parse(ws.data)
        switch (data.type) {
          case 'friendList':
            this.friendList = data.friend
            // 根据上下线排序
            this.friendList.sort(function (a, b) {
              if (a.online === true && b.online === false) return -1
              if (a.online === false && b.online === true) return 1
              return 0
            })
            break
          case 'goOff':
            this.handleGoOff(data)
            break
          case 'goOnline':
            this.handleGoOnline(data)
            break
          case 'msg':
            this.handleMsg(data)
            break
          case 'userList':
            this.userList = data.users
        }
      }
    },
    mounted () {
      this.openConnect()
      this.info = JSON.parse(localStorage.getItem('user'))
    }
  }
</script>
<style scoped rel="stylesheet/sass" lang="sass">
    $maxHeight : 600px
    $headHeight: 50px
    $imageSize : 40px
    .item
        margin: 0px
    .avatar-uploader
        .el-upload
            border: 1px dashed #d9d9d9
            border-radius: 6px
            cursor: pointer
            position: relative
            overflow: hidden
        .el-upload:hover
            border-color: #409EFF
    .dialog
        .avatar-uploader-icon
            font-size: 28px
            color: #8c939d
            width: 178px
            height: 178px
            line-height: 178px
            text-align: center
        .avatar
            width: 178px
            height: 178px
            display: block

    .msg-box
        border: 1px solid #D8DCE5
        height: $maxHeight
        .content
            background-color: #f5f5f5
            overflow: auto
            height: 350px
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
            border-top: 1px solid #D8DCE5
            height: 180px
            padding: 20px
            .send
                float: right
                margin-top: 10px
                width: 100px
            .message
                overflow: auto
        .head
            background-color: #f5f5f5
            height: $headHeight
            border-bottom: 1px solid #D8DCE5
            p
                height: $headHeight
                line-height: $headHeight
                margin: 0
                padding: 0 20px
    .chat
        margin-top: 100px
        text-align: center
        min-width: 1366px
        &>div
            margin: 0 auto
            display: inline-block
            &>div:first-child
                width: 250px
                display: inline-block
                float: left
            &>div:nth-child(2)
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
            width: 58px
            height: 100%
            display: inline-block
            float: left
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

        .friend
            background-color: #e7e6e6
            width: 190px
            float: right
            height: 100%
            display: inline-block
            .head
                height: $headHeight
                border-bottom: 1px solid #D8DCE5
                .search
                    margin: 10px 7px
                    width: 80%
            .list
                .dot
                    position: absolute
                    top: 0px
                    left: 55px
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
                .current
                    background-color: #c6c5c5
                overflow: auto
                height: 530px
                div
                    position: relative
                    padding: 5px 8px
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

</style>