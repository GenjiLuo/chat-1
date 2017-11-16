<template>
    <div class="login_start">
        <el-form :model="loginForm" :rules="loginFormRules" ref="ruleForm2" label-position="left" label-width="0px"
                 class="login-container">
            <h2 class="title"><img src="../assets/logo.jpg" alt="" class="logo" width="32" height="32">svp在线IM</h2>
            <el-form-item prop="account">
                <el-input type="text" v-model="loginForm.account" auto-complete="off" placeholder="账号"></el-input>
            </el-form-item>
            <el-form-item prop="checkPass">
                <el-input type="password" v-model="loginForm.password" auto-complete="off" placeholder="密码"
                          @keyup.enter.native="handleSubmit"></el-input>
            </el-form-item>
            <el-form-item style="width:100%;">
                <el-button type="primary" style="width:100%;">
                    登录
                </el-button>
            </el-form-item>
            <el-form-item style="width:100%;">
                <router-link to="/register">
                    <el-button type="info" style="width:100%;">
                        注册
                    </el-button>
                </router-link>
            </el-form-item>
        </el-form>
        <!--<footer>-->
        <!--<p class="">版权所有：深圳市联云谷科技有限公司</p>-->
        <!--<p class="">电话：400-888-5840 备案号：粤ICP备16109127号-1</p>-->
        <!--</footer>-->
    </div>
</template>
<script>
  export default {
    data () {
      return {
        checked: '',
        username: '',
        password: '',
        loginForm: {
          password: '',
          account: ''
        },
        loginFormRules: {}
      }
    },
    methods: {
      handleSubmit () {
        return ''
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

</style>