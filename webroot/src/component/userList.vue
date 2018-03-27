<template>
    <el-dialog :visible.sync="visible" width="300px" :show-close="false" custom-class="new-friend"
               :close-on-click-modal="false" :close-on-press-escape="false">
            <span slot="title">
               <el-input placeholder="请输入内容" v-model.trim="search" class="input-with-select" size="small">
                    <el-button slot="append" icon="el-icon-search" size="mini" @click="handleUserList"/>
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
                <el-button @click="close">关 闭</el-button>
            </span>
    </el-dialog>
</template>

<script>
  import {uerList, createApply} from '../api/api'
  export default {
    name: 'user-list',
    props: ['visible'],
    data () {
      return {
        search: '',
        userList: []
      }
    },
    methods: {
      close () {
        this.$emit('update:visible', false)
      },
      handleUserList () {
        uerList({search: this.search}).then(res => {
          if (parseInt(res.status) === 1) {
            this.userList = res.userList
          }
        })
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
      }
    },
    mounted () {
      this.handleUserList()
    }
  }
</script>

<style scoped lang="sass">
    $imageSize: 40px
    .new-friend
        input
            width: 200px
        .user-box
            max-height: 400px
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
</style>