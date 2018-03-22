<template>
    <el-dialog :visible.sync="visible" width="400px" :show-close="false" custom-class="new-friend">
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
                <el-button @click="visible.applyList = false">关闭</el-button>
            </span>
    </el-dialog>
</template>

<script>
  export default {
    name: "friendApplied",
    prop: [visible],
    data: function () {
      return {
        search: '',
        applyList: []
      }
    },
    method: {

    },
    computed: {
      // 好友申请搜索
      filterApplyList () {
        if (this.search !== '') {
          return this.applyList.filter((element) => {
            return element.username.indexOf(this.search.applyList) !== -1
          })
        }
        return this.applyList
      }
    }
  }
</script>

<style scoped>

</style>