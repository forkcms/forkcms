<template>
  <div class="d-flex">
    <Facebook
      v-if="facebook"
      :page_url="page_url"
      @clicked="openPopUpWindow"
    ></Facebook>
    <Twitter
      v-if="twitter"
      :page_url="page_url"
      @clicked="openPopUpWindow"
    ></Twitter>
    <LinkedIn
      v-if="linkedin"
      :page_url="page_url"
      @clicked="openPopUpWindow"
    ></LinkedIn>
  </div>
</template>

<script>
  import Facebook from './ShareButtons/Facebook'
  import Twitter from './ShareButtons/Twitter'
  import LinkedIn from './ShareButtons/LinkedIn'

  export default {
    name:  'v-share-buttons',
    components: {
      Facebook,
      Twitter,
      LinkedIn
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
      linkedin: {
        type: Boolean,
        default: false
      },
      page_url: {
        type: String,
        default: () => document.location.href.replace(document.location.hash, "")
      }
    },
    methods: {
      openPopUpWindow(share_url, width = 640, height = 480) {
        let left = Math.round(screen.width / 2 - width / 2);
        let top = Math.round(screen.height / 2 - height / 2);
        const window_config = `width=${width},height=${height},left=${left},top=${top}`;

        return window.open(share_url, "Share this", `${window_config},toolbar=no,menubar=no,scrollbars=no`)
      }
    }
  }
</script>
