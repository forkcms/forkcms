<template>
  <div>
    <button
      class="btn btn-share btn-linked-in"
      :page-url="page_url"
      @click="buttonClick"
    >
      <i class="fab fa-linkedin-in fa-fw"></i>
    </button>
  </div>
</template>

<script>
  export default {
    name:  'v-linked-in',
    props: {
      page_url: {
        type: String,
        default: () => document.location.href.replace(document.location.hash, "")
      }
    },
    methods: {
      buttonClick () {
        this.$emit('clicked', this.share_url)
      }
    },
    computed: {
      share_url() {
        return `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(this.$props.page_url)}&mini=true`
      }
    }
  }
</script>
