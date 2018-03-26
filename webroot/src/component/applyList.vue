<template>
    <el-dialog :visible.sync="show" width="400px" :show-close="false" custom-class="new-friend"
               :close-on-click-modal="false" :close-on-press-escape="false">
            <span slot="title">
                  <el-input v-model.trim="search" auto-complete="off" size="small"
                            prefix-icon="el-icon-search" placeholder="用户名"/>
            </span>
        <div class="apply-box">
            <div v-for="apply in filterApplyList">
                <img :src="apply.avatar">
                <span>{{apply.username}}</span>
                <span v-if="apply.reason">({{apply.reason}})</span>
                <el-button @click='handleReject(apply)' size="mini" class="el-icon-error" type="danger" plain
                           v-show="parseInt(apply.status)===0"/>
                <el-button @click='handleAgree(apply)' size="mini" class="el-icon-success" type="primary" plain
                           v-show="parseInt(apply.status)===0"/>
                <el-button :disabled="parseInt(apply.status) === 1 " size="mini" type="primary" plain
                           v-show="parseInt(apply.status)===1">已同意
                </el-button>
                <el-button :disabled="parseInt(apply.status) === 2 " size="mini" type="danger" plain
                           v-show="parseInt(apply.status)===2">已拒绝
                </el-button>
            </div>
        </div>
        <span slot="footer" class="dialog-footer">
                <el-button @click="handleClose">关闭</el-button>
            </span>
    </el-dialog>
</template>

<script>
  import {applyList, updateApply, updateUser} from '../api/api'
  export default {
    name: 'apply-list',
    prop: ['visible', 'notReadApply'],
    data: function () {
      return {
        search: '',
        applyList: []
      }
    },
    method: {
      // 关闭界面
      handleClose () {
        this.$emit('update:visible', false)
      },
      // 同意好友申请
      handleAgree (apply) {
        updateApply({status: 1, id: apply.id}).then(res => {
          if (parseInt(res.status) === 1) {
            this.handleFriendList()
            apply.status = 1
          }
        })
      },
      // 拒绝好友申请
      handleReject (apply) {
        updateApply({status: 2, id: apply.id}).then(res => {
          if (parseInt(res.status) === 1) {
            apply.status = 2
          }
        })
      },
      handleApplyList () {
        applyList(res => {
          if (parseInt(res.status) === 1) {
            this.applyList = res.applyList
          }
        })
      }
    },
    computed: {
      // 好友申请搜索
      filterApplyList () {
        if (this.search !== '') {
          return this.applyList.filter((element) => {
            return element.username.indexOf(this.search) !== -1
          })
        }
        return this.applyList
      },
      show () {
        this.handleApplyList()
        updateUser({id: this.info.id, type: 'applyRead'}).then(res => {
          if (parseInt(res.status) === 1) {
            this.haveNotReadApply = false
          }
        })
        return this.visible
      }
    }
  }
</script>

<style scoped lang="sass">
    $imageSize : 40px
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