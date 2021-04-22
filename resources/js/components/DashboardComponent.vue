<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container mb-5">
            <span class="">
                <div class="row pt-5" v-if="user.role=='Admin'">
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h5>{{ stat.inventories }}</h5>

                                <p>Total Items</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-pie-chart"></i>
                            </div>
                            <a href="javascript:void(0)" @click="$router.push({name: 'Inventory'})" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    

                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h5>{{ stat.customers }}</h5>

                                <p>Customer Registrations</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-plus"></i>
                            </div>
                            <a href="javascript:void(0)" @click="$router.push({name: 'Customers'})" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h5>{{ stat.transactions }}</h5>

                                <p>Successful Transactions</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-shopping-bag"></i>
                            </div>
                            <a href="javascript:void(0)" @click="$router.push({name: 'Transactions'})" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->

                    <!-- ./col -->
                    <div class="col-lg-3 col-xs-6">
                        <!-- small box -->
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h5>
                                    <span v-html="nairaSign"></span>
                                    {{ formatPrice(stat.mainPrice) }}
                                </h5>

                                <p>Total Sales Value</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                            <a href="javascript:void(0)" @click="$router.push({name: 'Transactions'})" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <!-- ./col -->
                </div>

                <!--<div class="row">
                    <div class="col-md-6">
                        <div class="box box-danger">
                            <div class="box-header">
                                <h3 class="box-title">All sales</h3>
                            </div>
                            <va-chart v-if="site_visit_chart_config" :chart-config="site_visit_chart_config"></va-chart>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-danger">
                            <div class="box-header">
                                <h3 class="box-title">Daily Item Sales</h3>
                            </div>
                            <va-chart v-if="daily_order_chart_config" :chart-config="daily_order_chart_config"></va-chart>
                        </div>
                    </div>
                </div>-->

                <div class="row pt-5">
                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Quotes</h3>
                            </div>

                            <div class="card-body">
                                <div v-if="quotes.data.length">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Date</th>
                                            <th>Ref ID</th>
                                            <th>Customer</th>

                                            <!--<th>Marketer</th>
                                            <th>Cashier</th>
                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                            <th>Discount (<span v-html="nairaSign"></span>)</th>
                                            <th>Payment Type</th>-->

                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                       
                                        </tr>

                                        <tr v-for="order in quotes.data" :key="order.id">
                                            <td>{{ order.created_at | myDate }}</td>
                                            <td>{{ order.sale_id }}</td>
                                            <td>{{ order.user_name }}</td>
                                            <!--<td>{{ formatPrice(order.initialPrice)  }}</td>
                                            <td>{{ formatPrice(order.discount)  }}</td>
                                            <td>By {{ order.mop }}</td> -->

                                            <td>{{ formatPrice(order.totalPrice)  }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-else class="alert alert-info text-center">No Pending Quotes.</div>
                            </div>

                            <div class="card-footer" v-if="invoices.data.length">
                                <button class="btn btn-primary" type="button" @click="$router.push({name: 'Quotes'})">View All</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Invoices</h3>
                            </div>

                            <div class="card-body">
                                <div v-if="invoices.data.length">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Date</th>
                                            <th>Ref ID</th>
                                            <th>Customer</th>

                                            <!--<th>Marketer</th>
                                            <th>Cashier</th>
                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                            <th>Discount (<span v-html="nairaSign"></span>)</th>
                                            <th>Payment Type</th>-->

                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                        </tr>

                                        <tr v-for="order in invoices.data" :key="order.id">
                                            <td>{{ order.created_at | myDate }}</td>
                                            <td>{{ order.sale_id }}</td>
                                            <td>{{ order.user_name }}</td>
                                            <!--<td>{{ formatPrice(order.initialPrice)  }}</td>
                                            <td>{{ formatPrice(order.discount)  }}</td>
                                            <td>By {{ order.mop }}</td> -->

                                            <td>{{ formatPrice(order.totalPrice)  }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-else class="alert alert-info text-center">No Pending Invoice.</div>
                            </div>

                            <div class="card-footer" v-if="invoices.data.length">
                                <button class="btn btn-primary" type="button" @click="$router.push({name: 'Invoice'})">View All</button>
                            </div>
                        </div>
                    </div>
                </div>
            </span>
        </div>
    </b-overlay>
</template>

<script>
    import moment from "moment";

    export default {
        created() {
            this.getUser();
            this.getStat();
            this.loadQuote();
            this.loadSales();
        },

        data() {
            return {
                is_busy: false,
                user: "",
                editMode: false,
                stat: '',
                nairaSign: "&#x20A6;",
                filterForm: {
                    start_date: '',
                    end_date: '',
                    customer: '',
                    selected: '5',
                },

                invoices: {},
                invoices: {
                    data: '',
                },

                quotes: {},
                quotes: {
                    data: '',
                },
            };
        },

        methods: {
            getUser() {
                if(this.is_busy) return;
                this.is_busy = true;

                axios.get("api/user")
                .then(({ data }) => {
                    this.user = data.user;
                    console.log(this.user);
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            getStat() {
                axios.get("/api/stat")
                .then(({ data }) => {
                    this.stat = data;
                });
            },

            formatPrice(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },

            loadSales() {
                axios.get('/api/store/invoice', { params: this.filterForm })
                .then((data) => {

                    console.log(data.data)
                    if(this.filterForm.selected==0)
                    {
                        this.invoices.data = data.data.report_data;
                    }
                    else{
                        this.invoices = data.data.report_data;
                    }             
                })

                .catch((err) => {
                    console.log(err);
                })

                .finally(() => {
                    this.is_busy = false;
                });
            },

            loadQuote() {
                axios.get('/api/store/quotes', { params: this.filterForm })
                .then((data) => {

                    console.log(data.data)
                    if(this.filterForm.selected==0)
                    {
                        this.quotes.data = data.data.report_data;
                    }
                    else{
                        this.quotes = data.data.report_data;
                    }
                    
                })

                .catch((err) => {
                    console.log(err);
                });
            },
        }      
    };
</script>
<style>
    .help-img{
        height:40px;
        width:40px;
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .hover:hover {
        background-color:#ECF0F5;
    }

    .routerText{
        font-size:15px
    }

    .margin-bottom {
        margin-bottom: 50px
    }
    .text-center{
        padding: 10px
    }
</style>