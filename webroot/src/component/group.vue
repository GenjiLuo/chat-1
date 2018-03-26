<template>
    <el-dialog :visible.sync="visible" width="540px" :show-close="false" title="创建群组"
               :close-on-click-modal="false" :close-on-press-escape="false">
        <el-transfer :titles="['好友','群组']"	v-model="groupIdList" :data="friendList" :props="groupProps" style="text-align: left"></el-transfer>
        <span slot="footer" class="dialog-footer">
                 <el-button type="primary" @click="handleCreateGroup">确定</el-button>
                <el-button @click="handleClose">关闭</el-button>
            </span>
    </el-dialog>
</template>
<script>
  import {createGroup} from '../api/api'
  export default {
    name: 'group',
    props: ['visible', 'friendList'],
    data () {
      return {
        groupIdList: [],
        groupProps: {
          key: 'id',
          label: 'username'
        }
      }
    },
    methods: {
      handleClose () {
        this.$emit('update:visible', false)
      },
      // 创建群组
      handleCreateGroup () {
        if (this.groupIdList.length === 0) {
          this.$alert('请选择群组人员', '提示')
          return false
        }
        this.$prompt('请输入群组名称', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消'
        }).then(({ value }) => {
          createGroup({ids: this.groupIdList, name: value}).then(res => {
            if (parseInt(res.status) === 1) {
              this.$emit('update:visible', false)
              this.groupIdList = []
            }
          })
        })
      }
    }
  }
</script>

<style scoped>

</style>