<template>
  <div>
    <button
      class="btn btn-share btn-twitter"
      :page-url="page_url"
      @click="buttonClick"
    >
      <i class="fab fa-twitter fa-fw"></i>
    </button>
  </div>
</template>

<script>
  export default {
    name:  'v-twitter',
    props: {
      page_url: {
        type: String,
        default: () => document.location.href.replace(document.location.hash, "")
      },
    },
    methods: {
      buttonClick () {
        this.$emit('clicked', this.share_url)
      }
    },
    computed: {
      share_url() {
        return `https://twitter.com/share?url=${encodeURIComponent(this.$props.page_url)}`
      }
    }
  }
</script>
