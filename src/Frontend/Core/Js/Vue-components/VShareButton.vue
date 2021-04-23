<template>
  <div>
    <Facebook
      v-if="platform === 'facebook'"
      :page_url="page_url"
      :page_description="page_description"
      :page_title="page_title"
    ></Facebook>
  </div>
</template>

<script>
  import Facebook from './ShareButtons/Facebook'

  export default {
    name:  'v-share-button',
    components: {
      Facebook
    },
    props: {
      platform: {
        type: String,
        required: true
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
  }
</script>
