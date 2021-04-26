<template>
  <div class="d-flex">
    <Facebook
      v-if="facebook"
      :page_url="page_url"
      :page_description="page_description"
      :page_title="page_title"
      @clicked="openPopUpWindow"
    ></Facebook>
    <Twitter
      v-if="twitter"
      :page_url="page_url"
      :page_title="page_title"
      @clicked="openPopUpWindow"
    ></Twitter>
  </div>
</template>

<script>
  import Facebook from './ShareButtons/Facebook'
  import Twitter from './ShareButtons/Twitter'

  export default {
    name:  'v-share-buttons',
    components: {
      Facebook,
      Twitter
    },
    props: {
      facebook: {
        type: Boolean,
        default: false
      },
      twitter: {
        type: Boolean,
        default: false
      },
      page_url: {
        type: String,
        default: () => document.location.href.replace(document.location.hash, "")
      },
      page_description: {
        type: String,
        default: () => ""
      },
      page_title: {
        type: String,
        default: () => ""
      },
    },
    methods: {
      openPopUpWindow(share_url, width = 640, height = 480) {
        let left = Math.round(screen.width / 2 - width / 2);
        let top = Math.round(screen.height / 2 - height / 2);
        const window_config = `width=${width},height=${height},left=${left},top=${top}`;

        return window.open(share_url, "Share this", `${window_config},toolbar=no,menubar=no,scrollbars=no`)
      },
    },
  }
</script>
