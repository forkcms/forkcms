<template>
  <button
    class="btn btn-share btn-facebook"
    :page-url="page_url"
    @click="buttonClick"
  >
    <i class="fab fa-facebook-f fa-fw"></i>
  </button>
</template>

<script>
  export default {
    name:  'v-facebook',
    props: {
      page_url: {
        type: String,
        default: () => document.location.href.replace(document.location.hash, "")
      },
      page_title: {
        type: String,
        default: ''
      },
      page_description: {
        type: String,
        default: ''
      }
    },
    data () {
      return {
      }
    },
    methods: {
      buttonClick () {
        this.$emit('clicked', this.share_url)
      }
    },
    computed: {
      share_url() {
        return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.$props.page_url)}&description=${encodeURIComponent(this.$props.page_description)}&title=${encodeURIComponent(this.$props.page_title)}`
      },
    }
  }
</script>
