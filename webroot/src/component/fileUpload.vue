<template>
    <div>
        <input type="file" id="file-upload" name="file" multiple v-show="false">
        <div class="file-list" v-for="file in fileList">
            <p>{{file.name}}</p>
        </div>
    </div>
</template>

<script>
  export default {
    name: 'file-upload',
    props: ['fileList'],
    data () {
      return {
        sliceSize: 256
      }
    },
    methods: {
      handlePostdata (start, file, size, fileRead = null) {
        if (fileRead === null){
          fileRead = new FileReader()
          fileRead.onloadend = e => {
            if (e.target.readyState === FileReader.DONE) { // DONE == 2
              if (stop < file.size) {
                this.handlePostdata(stop, file, size, fileRead)
              }
            }
          }
          let stop = start + size
          let splice = file.slice(start, stop)
          let data = fileRead.readAsBinaryString(splice)
        }
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