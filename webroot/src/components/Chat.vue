<template>
    <div>
        <el-row>
            <el-col :span="8" :offset="8" >
                <div class="msg-box">
                    <div class="">

                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
</template>
<script>
  export default {
    data () {
      return {
        test: ''
      }
    }
  }
</script>
<style scoped type="text/css">
    .msg-box{
        border: 1px solid #D8DCE5;
        border-radius: 10px;
        height: 500px;
    }
</style>