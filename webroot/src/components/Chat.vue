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
                    <div class="friend-list"></div>
                </div>
            </el-col>
            <el-col :span="8">
                <div class="msg-box">
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
                </div>
            </el-col>
        </el-row>
        <el-dialog title="修改信息" :visible.sync="editVisible" width="30%">
            <el-form :model="editForm" label-width="80px">
                <el-form-item label="昵称" >
                    <el-input v-model="editForm.nickname" ></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="editVisible = false">取 消</el-button>
                <el-button type="primary" @click="editVisible = false">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<script>
  export default {
    data () {
      return {
        editVisible: false,
        info: {},
        msg: '',
        currentFriend: {},
        friend: [
          {
            fd: '',
            msgList: [],
          }
        ],
        editForm: {
          nickname: '',
          avatar: ''
        }
      }
    },
    methods: {
      handleShowEdit(){
        this.editForm.nickname = this.info.nickname
        this.editVisible = true
      }
    },
    mounted () {
      this.info = JSON.parse(localStorage.getItem('user'))
      console.log(this.info)
    }
  }
</script>
<style scoped rel="stylesheet/sass" lang="sass">
    .msg-box
        border: 1px solid #D8DCE5
        height: 600px
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
        height: 600px
        border-right: none
        .info
            width: 60px
            height: 100%
            border-right: 1px solid #D8DCE5
            display: inline-block
            .avatar
                padding: 10px
                img
                    width: 40px
                    height: 40px
                    cursor: pointer
        .friend-list
            width: 100%
            height: 100%
            display: inline-block
</style>