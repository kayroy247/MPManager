<template>
    <md-dialog :md-active.sync="addNewReceipt" :md-clicked-outside="true">
        <div v-if="agent.balance<0">
            <form novalidate class="md-layout" @submit.prevent="saveReceipt"
                  data-vv-scope="Receipt-Form">
                <md-card class="md-layout-item">
                    <md-card-header>
                        <div style="float:right; cursor:pointer" @click="hide()">
                            <md-icon>close</md-icon>&nbsp;Close
                        </div>
                    </md-card-header>
                    <md-card-content>
                        <div class="exclamation">
                        <span class="success-span">
                            <md-icon style="color: green">done</md-icon>
                        </span>
                            <div class="md-layout-item md-size-100 exclamation-div">
                                <span>Suggested receipt amount is  {{ agent.dueToEnergySupplier }} </span>
                            </div>
                        </div>
                        <md-field :class="{'md-invalid': errors.has('Receipt-Form.amount')}">
                            <label for="amount">Amount </label>
                            <md-input name="amount" id="amount" v-model="agentReceiptService.newReceipt.amount"
                                      :max="agent.dueToEnergySupplier"
                                      v-validate="'required|min_value:0'" type="number"
                            />
                            <span class="md-error">{{ errors.first('Receipt-Form.amount') }}</span>
                        </md-field>
                        <md-progress-bar md-mode="indeterminate" v-if="loading"/>
                    </md-card-content>
                    <md-card-actions>
                        <md-button role="button" type="submit" class="md-raised md-primary" :disabled="loading">
                            Receive
                        </md-button>

                    </md-card-actions>
                </md-card>
            </form>
        </div>
        <div v-else>
            <md-card class="md-layout-item">
                <md-card-header>
                    <div style="float:right; cursor:pointer" @click="hide()">
                        <md-icon>close</md-icon>&nbsp;Close
                    </div>
                </md-card-header>
                <md-card-content>
                    <div class="exclamation">
                        <span class="success-span">
                            <md-icon style="color: green">notifications</md-icon>
                        </span>
                        <div class="md-layout-item md-size-100 exclamation-div">
                            <span>This agent does not owe the energy provider. </span>
                        </div>
                    </div>

                </md-card-content>
                <md-card-actions>

                </md-card-actions>
            </md-card>
        </div>

    </md-dialog>

</template>
<script>

import { EventBus } from '../../../shared/eventbus'
import { AgentReceiptService } from '../../../services/AgentReceiptService'
import { AgentService } from '../../../services/AgentService'

export default {
    name: 'NewReceipt',
    data () {
        return {
            agentReceiptService: new AgentReceiptService(),
            loading: false,
            agentService: new AgentService(),
        }
    },
    components: {},
    props: {
        agent: {},
        addNewReceipt: {
            type: Boolean,
            default: false,

        }
    },
    methods: {

        async saveReceipt () {
            if (this.agentReceiptService.newReceipt.amount > this.agent.dueToEnergySupplier) {
                this.alertNotify('warn', 'Max receipt amount must be equal to ' + this.agent.dueToEnergySupplier)
                this.agentReceiptService.newReceipt.amount = this.agent.dueToEnergySupplier
            } else {
                let validator = await this.$validator.validateAll('Receipt-Form')
                if (validator) {
                    try {
                        this.loading = true
                        try {

                            this.agentReceiptService.newReceipt.agentId = this.agent.id
                            await this.agentReceiptService.addNewReceipt()
                            this.loading = false
                            this.receiptAdded()
                            this.alertNotify('success', 'Agent added successfully')
                        } catch (e) {
                            this.loading = false
                            this.alertNotify('error', e.message)
                        }
                    } catch (e) {
                        this.alertNotify('error', e.message)
                    }
                }
            }

        },
        hide () {
            EventBus.$emit('newReceiptClosed')
        },
        receiptAdded () {
            EventBus.$emit('receiptAdded')
        },
        alertNotify (type, message) {
            this.$notify({
                group: 'notify',
                type: type,
                title: type + ' !',
                text: message
            })
        },
    }
}

</script>
<style scoped>

</style>
<style scoped>
.success-span {
    font-size: large;
    font-weight: 700;
    color: green;
}

.exclamation-div {
    margin-top: 2% !important;
}

.exclamation {
    width: 100% !important;
    margin: auto;
    align-items: center;
    display: inline-grid;
    text-align: center;

}

.exclamation-div span {
    font-size: medium !important
}
</style>
