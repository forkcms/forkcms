<template>
  <div>
    <button
      class="btn btn-twitter"
      :page-url="page_url"
      @click.prevent="makeShareWindow"
    >
      <i class="fab fa-twitter"></i>
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
        const share_url = `https://twitter.com/share?url=${encodeURIComponent(this.$props.page_url)}&text=${encodeURIComponent(this.$props.page_title)}`;
        return this.openPopUpWindow(share_url, width, height)
      }
    },
  }
</script>
