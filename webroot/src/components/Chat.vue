<template>
    <div class="chat">
        <div>
            <div >
                <div class="friend-box">
                        <div class="info">
                        <div class="avatar" @click="handleShowEdit">
                            <img :src="info.avatar" title="修改个人信息"/>
                        </div>
                    </div>
                        <div class="friend">
                        <div class="head">
                            <el-input size="small" v-model="search" class="search"  prefix-icon="el-icon-search" placeholder="用户名"></el-input>
                        </div>
                        <div class="list">
                            <div v-for="friend in online" :key="friend.id" :title="friend.nickname" @click="changeChat(friend)">
                                <img :src="friend.avatar">
                                <span>{{friend.nickname}}</span>
                            </div>
                            <div v-for="friend in offline" :key="friend.id" :title="friend.nickname" @click="changeChat(friend)">
                                <img :src="friend.avatar" class="offline">
                                <span>{{friend.nickname}}</span>
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
                            <div v-if="msg.owner===false" class="others">
                                <img :src="msg.avatar" />
                                <span>{{msg.msg}}</span>
                            </div>
                            <div style="text-align: right"  v-if="msg.owner===true">
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
        <el-dialog title="修改头像" :visible.sync="editVisible" width="218px" class="dialog">
            <el-upload class="avatar-uploader"
                    :action="action"
                    :show-file-list="false"
                    :on-success="handleAvatarSuccess"
                    :before-upload="beforeAvatarUpload">
                <img v-if="info.avatar" :src="info.avatar" class="avatar">
                <i  class="el-icon-plus avatar-uploader-icon" v-else></i>
            </el-upload>
        </el-dialog>
    </div>
</template>
<script>
  import {avatarUrl, ws} from '../api/api'
  export default {
    data () {
      return {
        editVisible: false,
        info: {},
        msg: '',
        search: '',
        online: [],
        offline: [],
        currentChat: {
          userId: '',
          nickname: '',
          avatar: '',
          msgList: []
        },
        chats: [],
        editForm: {
          nickname: '',
          avatar: ''
        },
        socket: ''
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
      }
    },
    methods: {
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
        let currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate + ' ' + date.getHours() + seperator2 + date.getMinutes() + seperator2 + date.getSeconds()
        return currentdate
      },

      handleSendMsg () {
        if (this.msg === '') {
          this.$message.error('不能发送空消息!')
          return false
        }
        if (this.currentChat.userId === '') {
          this.$message.error('请先选择聊天人员')
          return false
        }
        let msg = {
          to: this.currentChat.userId,
          from: this.info.id,
          msg: this.msg,
          type: 'msg',
          avatar: this.info.avatar,
          time: this.getNowFormatDate(),
          owner: true
        }
        this.currentChat.msgList.push(msg)
        this.socket.send(JSON.stringify(msg))
        this.msg = ''
        document.getElementById('content').scrollIntoView()
      },
      changeChat (friend) {
        let flag = true
        for (let [index, {userId}] of this.chats.entries()) {
          if (parseInt(userId) === parseInt(friend.id)) {
            this.currentChat = this.chats[index]
            flag = false
          }
        }
        if (flag) {
          let newChat = {
            userId: friend.id,
            nickname: friend.nickname,
            avatar: friend.avatar,
            msgList: []
          }
          this.chats.push(newChat)
          this.currentChat = newChat
        }
      },
      handleShowEdit () {
        this.editForm.nickname = this.info.nickname
        this.editVisible = true
      },
      handleAvatarSuccess (res, file) {
        this.info.avatar = res.avatar
      },
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
      onConnect (ws) {
        console.log('connect')
      },
      onMessage (ws) {
        let data = JSON.parse(ws.data)
        switch (data.type) {
          case 'userList':
            this.online = data.online
            this.offline = data.offline
            break
          case 'goOff':
            for (let [index, {id}] of this.online.entries()) {
              if (parseInt(id) === parseInt(data.userId)) {
                let offlineUser = this.online.splice(index, 1)[0]
                this.offline.push(offlineUser)
                this.$message.info(`${offlineUser['nickname']}下线了`)
                return
              }
            }
            break
          case 'goOnline':
            for (let [index, {id}] of this.offline.entries()) {
              if (parseInt(id) === parseInt(data.userId)) {
                let onlineUser = this.offline.splice(index, 1)[0]
                this.online.push(onlineUser)
                this.$message.info(`${onlineUser['nickname']}上线了`)
                return
              }
            }
            break
          case 'msg':
            let from = data.from
            let flag = true
            for (let [index, {userId}] of this.chats.entries()) {
              if (parseInt(userId) === parseInt(from)) {
                this.chats[index].msgList.push(data)
                flag = false
                return
              }
            }
            if (flag) {
              let friend = ''
              for (let [index, {id}] of this.online.entries()) {
                if (parseInt(id) === parseInt(from)) {
                  friend = this.online[index]
                }
              }
              let newChat = {
                userId: friend.id,
                nickname: friend.nickname,
                avatar: friend.avatar,
                msgList: [ data ]
              }
              this.chats.push(newChat)
            }
        }
      }
    },
    mounted () {
      let token = localStorage.getItem('token')
      this.socket = new WebSocket(`${ws}?token=${token}`)
      this.socket.onopen = this.onConnect
      this.socket.onmessage = this.onMessage
      this.info = JSON.parse(localStorage.getItem('user'))
    }
  }
</script>
<style scoped rel="stylesheet/sass" lang="sass">
    $maxHeight : 600px
    $headHeight: 50px
    $imageSize : 40px
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
            border-right: 1px solid #D8DCE5
            display: inline-block
            float: left
            .avatar
                padding: 10px
                img
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
                overflow: auto
                height: 530px
                div
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
</style>