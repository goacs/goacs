<template>
  <prism-editor
          class="my-editor height-300"
          v-model="code"
          :highlight="highlighter"
          :line-numbers="lineNumbers"
  ></prism-editor>
</template>

<script>
  import { PrismEditor } from "vue-prism-editor";
  import "vue-prism-editor/dist/prismeditor.min.css";
  import { highlight, languages } from "prismjs/components/prism-core";
  import "prismjs/components/prism-markup";
  import "prismjs/components/prism-markup-templating";
  import "prismjs/components/prism-php";
  import "prismjs/themes/prism.css";
  import "prismjs/plugins/custom-class/prism-custom-class";

  export default {
    name: "CodeEditor",
    components: {
      PrismEditor
    },
    props: {
      value: {
        type: String,
        required: true,
      },
    },
    data: () => ({
      code: '',
      lineNumbers: true
    }),
    methods: {
      highlighter(code) {
        return highlight(code, languages.php);
      }
    },
    mounted() {
      window.Prism.plugins.customClass.map({ number: "prism-number", tag: "prism-tag" });
      this.code = this.value
    },
    watch: {

      code(val) {
        this.$emit('input', val);
      },
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
