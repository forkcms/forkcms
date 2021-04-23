<template>
  <div>
    <button
      class="btn btn-facebook"
      :page-url="page_url"
      @click.prevent="makeShareWindow"
    >
      <i class="fab fa-facebook-f"></i>
    </button>
  </div>
</template>

<script>
  export default {
    name:  'v-facebook',
    props: {
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
      }
    },
    data () {
      return {
      }
    },
    methods: {
      openPopUpWindow(share_url, width, height) {
        let left = Math.round(screen.width / 2 - width / 2);
        let top = Math.round(screen.height / 2 - height / 2);
        const window_config = `width=${width},height=${height},left=${left},top=${top}`;

        return window.open(share_url, "Share this", `${window_config},toolbar=no,menubar=no,scrollbars=no`)
      },

      makeShareWindow() {
        const width = 640;
        const height = 480;
        const share_url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.$props.page_url)}&description=${encodeURIComponent(this.$props.page_description)}&title=${encodeURIComponent(this.$props.page_title)}`;
        return this.openPopUpWindow(share_url, width, height)
      }
    },
  }
</script>
