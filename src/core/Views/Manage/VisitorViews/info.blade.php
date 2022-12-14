<template>
  <app-dialog title="traffic statistics">
    <div class="p-4">
      <a-radio-group type="button" v-model:model-value="day" @change="changeZoom">
        <a-radio value="7">7 days</a-radio>
        <a-radio value="30">30 days</a-radio>
        <a-radio value="90">90 days</a-radio>
        <a-radio value="365">1 year</a-radio>
      </a-radio-group>
      <div class="mt-6">
        {!!$appChart!!}
      </div>
    </div>
  </app-dialog>
</template>

<script>
  export default {
    data() {
      return {
        day: "7"
      }
    },
    mounted() {
      this.$nextTick(() => {
        this.changeZoom()

        setTimeout(() => {
          window.dispatchEvent(new Event('resize'))
        }, 300)
      })

    },
    methods: {
      changeZoom() {
        let now = new Date()
        this.onZoom(new Date(now.getTime() - this.day * 24 * 3600 * 1000).getTime(), now.getTime())
      },
      onZoom(start, stop) {
        this.$refs.chart.zoomX(
          start,
          stop
        )

      }

    }
  }
</script>
