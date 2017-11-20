<template>
    <div class="login_start">
        <el-form :model="registerForm" :rules="registerFormRule" ref="registerForm" label-position="left"
                 label-width="0px"
                 class="login-container">
            <h2 class="title">注册账号</h2>
            <el-form-item prop="username">
                <el-input type="text" v-model="registerForm.username" auto-complete="off"
                          placeholder="账号/昵称"></el-input>
            </el-form-item>
            <el-form-item prop="password">
                <el-input type="password" v-model="registerForm.password" auto-complete="off" placeholder="密码"
                          @keyup.enter.native="handleSubmit"></el-input>
            </el-form-item>
            <el-form-item prop="checkPwd">
                <el-input type="password" v-model="registerForm.checkPwd" auto-complete="off" placeholder="确认密码"
                          @keyup.enter.native="handleSubmit"></el-input>
            </el-form-item>

            <el-form-item style="width:100%;">
                <el-button type="primary" style="width:100%;" @click="handleRegister">
                    确定
                </el-button>
            </el-form-item>
            <el-form-item style="width:100%;">
                <router-link to="/register">
                    <el-button type="info" style="width:100%;">
                        取消
                    </el-button>
                </router-link>
            </el-form-item>
        </el-form>
    </div>

</template>
<script>
  import {register, checkUsername} from '../api/api.js'

  export default {
    data () {
      const validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'))
        } else {
          if (this.registerForm.checkPwd !== '') {
            this.$refs.registerForm.validateField('checkPwd')
          }
          callback()
        }
      }
      const validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'))
        } else if (value !== this.registerForm.password) {
          callback(new Error('两次输入密码不一致!'))
        } else {
          callback()
        }
      }
      const validateUsername = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输出用户名'))
        }
        checkUsername({username: value}).then(res => {
          if (parseInt(res.status) !== 1) {
            callback(new Error('该用户名已注册'))
          } else {
            callback()
          }
        })
      }
      return {
        registerForm: {
          username: '',
          password: '',
          checkPwd: ''
        },
        registerFormRule: {
          username: [
            {validator: validateUsername, trigger: 'blur'}
          ],
          password: [
            {validator: validatePass, trigger: 'blur'}
          ],
          checkPwd: [
            {validator: validatePass2, trigger: 'blur'}
          ]
        },
        imageUrl: ''
      }
    },
    methods: {
      handleRegister () {
        this.$refs.registerForm.validate(result => {
          if (result) {
            register(this.registerForm).then(res => {
              if (parseInt(res.status) === 1) {
                this.$alert('注册成功', '恭喜', {
                  confirmButtonText: '跳转登录',
                  callback: () => {
                    this.$router.push('login')
                  }
                })
              }
            })
          }
        })
      }
    }
  }
</script>
<style lang="scss" scoped>
    .login_start {
        background-size: 100% 100%;
        height: 100%;
        width: 100%;
    }

    .login-container {
        .logo {
            vertical-align: -8px;
            margin-right: 4px;
        }
        /*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-border-radius: 5px;
        background-clip: padding-box;
        width: 350px;
        padding: 35px 35px 15px 35px;
        background: #fff;
        border: 1px solid #eaeaea;
        box-shadow: 0 0 25px #cac6c6;
        position: relative;
        top: 180px;
        margin: auto;
        .title {
            margin: 0px auto 40px auto;
            text-align: center;
            color: #505458;
        }
        .remember {
            margin: 0px 0px 35px 0px;
        }
    }

    .avatar-uploader {
        .el-upload {
            border: 1px dashed #d9d9d9;
            border-radius: 6px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
    }

    .avatar-uploader .el-upload:hover {
        border-color: #409EFF;
    }

    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 58px;
        height: 58px;
        line-height: 58px;
        text-align: center;
    }

    .avatar {
        width: 58px;
        height: 58px;
        display: block;
    }
</style>