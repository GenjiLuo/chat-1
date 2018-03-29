<template>
    <el-dialog :visible.sync="visible" width="500px" :show-close="false"
               :close-on-click-modal="false" :close-on-press-escape="false">
        <el-table :data="list" :header-cell-style="{textAlign: 'center'}"  max-height="500">
            <el-table-column  width="50">
                <template slot-scope="scope">
                   <img :src="scope.row.avatar" class="avatar"/>
                </template>
            </el-table-column>
            <el-table-column property="username" label="账号" width="120"> </el-table-column>
            <el-table-column property="sex" label="性别" width="50">
                <template slot-scope="scope">
                    {{ parseInt(scope.row.sex) === 1 ? '男':'女'}}
                </template>
            </el-table-column>
            <el-table-column property="age" label="年龄" width="100"> </el-table-column>
            <el-table-column label="操作">
                <template slot-scope="scope">
                    <el-button  type="primary" size="mini" v-if="!isFriend(scope.row.id)">加好友</el-button>
                </template>
            </el-table-column>
        </el-table>
        <span slot="footer" class="dialog-footer">
                <el-button @click="close">关 闭</el-button>
            </span>
    </el-dialog>
</template>
<script>
  export default {
    name: 'group-user',
    props: ['list', 'visible', 'friendList'],
    methods: {
      close () {
        this.$emit('update:visible', false)
      },
      isFriend (id) {
        if (this.friendList.find(element => parseInt(element.id) === parseInt(id))) {
          return true
        }
        if (parseInt(this.$store.state.info.id) === parseInt(id)) {
          return true
        }
        return false
      }
    }
  }
</script>

<style scoped>
    .avatar{
        width: 40px;
        height: 40px;
    }
</style>