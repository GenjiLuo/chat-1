<template>
    <el-dialog title="修改头像" :visible.sync="visible" width="218px" class="dialog"
               :show-close="false"	:close-on-click-modal="false" :close-on-press-escape="false">
        <el-upload class="avatar-uploader"
                   :action="action"
                   :show-file-list="false"
                   :on-success="handleAvatarSuccess"
                   :before-upload="beforeAvatarUpload"
                   :headers="$store.getters.headers"
        >
            <img v-if="avatar" :src="avatar" class="avatar">
            <i class="el-icon-plus avatar-uploader-icon" v-else></i>
        </el-upload>
        <span slot="footer"  style="text-align: center">
               <el-button type="primary" @click="handleClose">关闭</el-button>
        </span>
    </el-dialog>
</template>

<script>
  import {avatarUrl} from '../api/api'
  export default {
    name: 'edit-info',
    props: ['visible'],
    data () {
      return {
        info: {}
      }
    },
    computed: {
      action () {
        return avatarUrl
      },
      avatar () {
        return this.$store.state.info.avatar
      }
    },
    methods: {
      handleClose () {
        this.$emit('update:visible', false)
      },
      // 头像上传成功事件
      handleAvatarSuccess (res) {
        this.$store.commit('setAvatar', res.url)
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
      }
    }
  }
</script>

<style scoped rel="stylesheet/sass" lang="sass">
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
</style>