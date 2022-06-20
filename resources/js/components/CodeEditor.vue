<template>
  <AceEditor
    v-model="code"
    @init="editorInit"
    lang="php"
    theme="monokai"
    width="100%"
    height="200px"
    :options="options"
  />
</template>

<script>
import AceEditor from 'vuejs-ace-editor';

  export default {
    name: "CodeEditor",
    components: {
      AceEditor
    },
    props: {
      value: {
        type: String,
        required: true,
      },
    },
    data: () => ({
      code: '',
      lineNumbers: true,
      options: {
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: false,
        fontSize: 14,
        highlightActiveLine: true,
        enableSnippets: true,
        showLineNumbers: true,
        tabSize: 2,
        showPrintMargin: false,
        showGutter: true,
      }
    }),
    methods: {
      editorInit() {
        require('brace/ext/language_tools')
        require('brace/ext/beautify')
        require('brace/mode/php')
        require('brace/mode/less')
        require('brace/theme/monokai')
      }
    },
    mounted() {
      this.$nextTick(function() {
        this.code = this.value;
      })
    },
    watch: {
      code(val) {
        this.$emit('input', val);
      },
      value(val) {
        this.code = val;
      }
    },
  }
</script>

<style type="scss">

  .my-editor {
    background: #f5f2f0;
    font-family: Fira code, Fira Mono, Consolas, Menlo, Courier, monospace;
    padding: 5px;


  }

  .height-300 {
    height: 300px;
  }

  .prism-number,
  .prism-tag {
    color: #905;
  }
</style>
