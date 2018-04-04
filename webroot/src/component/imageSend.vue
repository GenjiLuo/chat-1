<template>
    <div>
        <input type="file" id="file-upload" name="file" multiple v-show="false">
        <div class="file-list" v-for="file in fileList">
            <p>{{file.name}}</p>
        </div>
    </div>
</template>

<script>
  import {uploadFile} from '../api/api'
  export default {
    name: 'image-send',
    props: ['fileList'],
    data () {
      return {
        sliceSize: 1024 * 1024
      }
    },
    methods: {
      handlePostdata (start, file, size) {
        let stop = start + size
        let chunk = file.slice(start, stop)
        let form = new FormData()
        form.append('data', chunk)
        form.append('name', file.name)
        let config = {
            headers:{
              'Content-Type':'multipart/form-data'
            }
        }
        uploadFile(form, config).then(res => {
          console.log(res)
        })

      },
      handleFileSelect (e) {
        let file = e.target.files[0]
        this.fileList.push(file)
        this.handlePostdata(0, file, this.sliceSize)
      },
      upload () {
        let doc = document.getElementById('file-upload')
        doc.click()
      }
    },
    mounted () {
      let doc = document.getElementById('file-upload')
      doc.addEventListener('change', this.handleFileSelect, false)
    }

  }
</script>

<style scoped lang="sass">
    .file-list
        height: 50px

</style>