<template>
    <b-overlay :show="is_busy" rounded="sm">
         <div class="container-fluid">
            
            <div class="row mb-2 p-2">
                <div class="col-md-7">
                    <h2><strong>List of Debtors</strong></h2>
                </div>

                <div class="col-md-5">

                  <b-button size="sm" variant="outline-info"class="pull-right m-2" @click="onPrint"> <i class="fa fa-print"></i> Print</b-button>                      
                </div>
            </div>

            <div class="card" id="printMe">
                <div class="card-body table-responsive p-0" v-if="debtors.data.length">
                        <table class="table table-hover">
                            <tr>
                                <th>Invoice ID</th>
                                <th>Customer</th>
                                <th>Amount (<span v-html="nairaSign"></span>)</th>
                                <th>Date of Repayment</th>
                                <th>Action</th>
                            </tr>

                            <tr v-for="order in debtors.data" :key="order.id">
                                <td>{{ order.trans_id }}</td>
                                <td>{{ order.user_id }}</td>
                                <td>{{ formatPrice(order.amount) }}</td>
                                <td>{{ order.repayment_date | myDate}}</td>
                                <td><span @click=viewItems(order.trans_id) style="color:blue" class="btn">View</span></td>
                            </tr>
                        </table>
                </div>

                <div class="card-body" v-else>
                    <div class="alert alert-info text-center"><h3><strong>No Debtor Found.</strong></h3></div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-2">
                            <b>Show <select v-model="filterForm.selected" @change="onChange($event)">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="0">All</option>
                                </select>
                            Entries</b>
                            <br> Total: <b>{{ count_all }} debtors</b>
                        </div>

                        <div class="col-md-8" v-if="this.filterForm.selected!=0">
                            <pagination :data="debtors" @pagination-change-page="getResults"></pagination>
                        </div>

                        <div class="col-md-2">
                            <b-button variant="outline-danger" size="sm" v-if="action.selected.length" class="pull-right" @click="onDeleteAll"><i class="fa fa-trash"></i> Delete Selected</b-button>

                            <b-button disabled size="sm" variant="outline-danger" v-else class="pull-right"> <i class="fa fa-trash"></i> Delete Selected</b-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    import moment from 'moment';

    export default {
        created() {
            this.loadSales();
        },

        data() {
            return {
                filterForm: {
                    start_date: moment().subtract(30, 'days').format("YYYY-MM-DD"),
                    end_date: moment().format("YYYY-MM-DD"),
                    store: 0,
                    mode_of_payment: 0,
                    selected: '10',
                },

                payments: [
                  { value: 'Cash', text: 'Cash' },
                  { value: 'POS', text: 'POS' },
                  { value: 'Transfer', text: 'Transfer' },
                ],
                stores: {},
                is_busy: false,
                debtors: null,
                summary: null,
                debtors: {
                    data: '',
                },

                action: {
                    selected: []
                },
                count_all: '',
                unprintable: false,
                nairaSign: "&#x20A6;",
            };
        },

        methods: {
            onPrint() {
                this.$htmlToPaper('printMe');
            },

            onChange(event) {
                this.filterForm.selected = event.target.value;
                this.loadStores();
                this.getUser();
            },

            formatPrice(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },

            getResults(page = 1) {

                axios.get('/api/store/debtors?page=' + page, { params: this.filterForm })
                .then(response => {
                    this.debtors = response.data.report_data;
                });
            },

            loadSales() {
                if(this.is_busy) return;
                this.is_busy = true;

                axios.get('/api/store/debtors', { params: this.filterForm })
                .then(({ data }) => {
                    if(this.filterForm.selected==0)
                    {
                        this.debtors.data = data.report_data;
                    }
                    else{
                        this.debtors = data.report_data;
                    }
                    this.count_all = data.all;
                })
                .catch(() => {
                    Swal,fire(
                        "Failed!",
                        "Ops, Something went wrong, try again.",
                        "warning"
                    );
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            onFilterSubmit()
            {
                this.loadSales();
                this.$refs.filter.hide();
            },

            viewItems(sale_id) {
                this.$router.push({
                    path: "/store/debtors/" + sale_id
                });
            },
        },
    };
</script>

<style>
    .list_product{
        list-style: none;
    }
</style>
