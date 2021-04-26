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
        return `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(this.$props.page_url)}&text=${encodeURIComponent(this.$props.page_title)}&summary=${encodeURIComponent(this.$props.page_description)}&mini=true`;
      }
    },
  }
</script>
