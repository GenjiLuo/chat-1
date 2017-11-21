<template>
    <div class="chat">
        <el-row>
            <el-col :span="3" :offset="6">
                <div class="friend-box">
                    <div class="info">
                        <div class="avatar" @click="handleShowEdit">
                            <img :src="info.avatar" title="修改个人信息"/>
                        </div>
                    </div>
                    <div class="friend">
                        <div class="head">
                        </div>
                        <div class="list">
                            <div v-for="friend in friendList" :key="friend.id">
                                <img :src="friend.avatar" :class="{ offline:!friend.online}">
                                <span>{{friend.nickname}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </el-col>
            <el-col :span="8">
                <div class="msg-box">
                    <div class="head">
                    </div>
                    <div class="content"></div>
                    <div class="input">
                        <el-input type="textarea" :rows="4" placeholder="请输入内容" v-model="msg"
                                      class="message">
                        </el-input>
                        <el-button class="send" type="primary">发送(enter)</el-button>
                    </div>
                </div>
            </el-col>
        </el-row>
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
  import {avatarUrl} from '../api/api'
  export default {
    data () {
      return {
        editVisible: false,
        info: {},
        msg: '',
        currentFriend: {},
        friendList: [],
        friend: [],
        editForm: {
          nickname: '',
          avatar: ''
        },
        socket: ''
      }
    },
    computed: {
      action () {
        let token = localStorage.getItem('token')
        return avatarUrl + '?token=' + token
      }
    },
    methods: {
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
            this.friendList = data.users
            console.log( data.users)
            break
          case 'goOff':
            console.log(this.friendList[data.userId]);
            break
          case "goOnline":
            console.log(data)
            break
        }
      }
    },
    mounted () {
      let token = localStorage.getItem('token')
      this.socket = new WebSocket('ws://192.168.1.196:9501?token=' + token)
      this.socket.onopen = this.onConnect
      this.socket.onmessage = this.onMessage
      this.info = JSON.parse(localStorage.getItem('user'))
    }
  }
</script>
<style scoped rel="stylesheet/sass" lang="sass">
    $height : 600px
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
        height: $height
        .content
            overflow: auto
            height: 350px
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
            height: 70px
            border-bottom: 1px solid #D8DCE5
    .chat
        margin-top: 100px
    .friend-box
        border-left: 1px solid #D8DCE5
        border-top: 1px solid #D8DCE5
        border-bottom: 1px solid #D8DCE5
        height: $height
        border-right: none
        .info
            width: 30%
            height: 100%
            border-right: 1px solid #D8DCE5
            display: inline-block
            float: left
            .avatar
                padding: 10px
                img
                    width: 40px
                    height: 40px
                    cursor: pointer
        .friend
            width: 69.5%
            float: right
            height: 100%
            display: inline-block
            .head
                height: 70px
                border-bottom: 1px solid #D8DCE5
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
                        width: 40px
                        height: 40px
                    span
                        display: inline-block
                        vertical-align: top
                        width: 60%
                        overflow: hidden
                    &:hover
                        background-color: #0e90d2
                    .offline
                        filter: grayscale(100%)


</style>