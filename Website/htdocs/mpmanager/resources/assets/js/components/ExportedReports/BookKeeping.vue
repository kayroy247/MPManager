<template>
    <div>
        <widget
            :id="'book-keeping'"
            :title="'Payment Requests'"
            :paginator="bookKeepingService.paginator"
            :search="false"
            :subscriber="subscriber"
            color="orange"
        >
          <md-table v-model="list" md-sort="id" md-sort-order="desc">
              <md-table-row>
                  <md-table-head v-for="(item, index) in headers" :key="index">{{item}}</md-table-head>
              </md-table-row>
                <md-table-row  slot="md-table-row" slot-scope="{ item }">
                    <md-table-cell md-sort-by="id" md-label="ID"> {{ item.id}}</md-table-cell>
                    <md-table-cell md-label="Date"> {{ item.date}}</md-table-cell>
                    <md-table-cell md-label="File">
                        <div @click="download(item.id,'/book-keeping')">
                            <md-icon style="cursor: pointer;">save</md-icon>
                            <span > Download</span>
                        </div>

                    </md-table-cell>
                </md-table-row>
            </md-table>

        </widget>
    </div>

</template>

<script>
import Widget from '../../shared/widget'
import {EventBus} from '../../shared/eventbus'
import { BookKeepingService } from '../../services/BookKeepingService'

export default {
    name: 'BookKeeping',
    components: {
        Widget
    },
    mounted() {

        EventBus.$on('pageLoaded', this.reloadList)

    },
    beforeDestroy() {
        EventBus.$off('pageLoaded', this.reloadList)
    },
    data() {
        return {
            bookKeepingService: new BookKeepingService(),
            list: [],
            subscriber: 'bookKeeping',
            headers: ['ID', 'Date', 'File'],
            tableName: 'Outsource Report'
        }
    },
    methods: {
        reloadList(subscriber, data) {
            if (subscriber === this.subscriber) {
                this.list = this.bookKeepingService.updateList(data)
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.list.length)
            }

        },
        endSearching() {
            this.bookKeeping.showAll()
        },
        download(id, reference) {
            window.open(this.bookKeepingService.exportBookKeeping(id,reference))

        }
    }

}
</script>

<style scoped>

</style>
