<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid mt-2">
            <div class="row mb-2 p-2">
                <div class="col-md-4">
                    <h2><strong>{{ user.name }} </strong></h2>
                </div>

                <div class="col-md-8">
                    <vue-typeahead-bootstrap
                        v-model="userQuery"
                        :ieCloseFix="false"
                        :data="users"
                        :serializer="data => data.name"
                        @hit="getUserID($event)"
                        placeholder="Search for customer"
                        @input="lookUser"
                    />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <b-row>
                        <b-col cols="12" md="6">
                            Contact Person: <b>{{ user.c_person }}</b><br>
                            Phone: <b>{{ user.phone }}</b><br>
                            Email: <b>{{ user.email }}</b><br>
                            Address: <b>{{ user.address }} {{ user.city }} {{ user.state}}</b></p>
                        </b-col>
                        <b-col cols="12" md="6">
                            <p>Account Outstanding: <b><span v-html="nairaSign"></span>{{ formatPrice(user.wallet_balance) }}</b><br>
                            Credit Unit: <b><span v-html="nairaSign"></span>{{ formatPrice(user.credit_unit) }}</b><br>
                            Total Sales: <b>{{ order_count }}</b><br>
                            Value of total sales: <b><span v-html="nairaSign"></span>{{ formatPrice( value_order_count) }}</b></p>
                        </b-col>
                    </b-row>
                </div>
            </div>

            <b-card no-body>
                <b-tabs card>
                    <b-tab title="Overview" active>
                        <b-card-text>
                            <b-row>
                                <b-col cols="12" md="6">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <tr>
                                                <th>Quotes</th>
                                                <th>Invoices</th>
                                                <th>Transactions</th>
                                            </tr>

                                            <tr>
                                                <td>{{ quotes.length }}</td>
                                                <td>{{ invoices.length }}</td> 
                                                <td>{{ all_orders.length }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </b-col>
                                <b-col cols="12" md="6">
                                    <div>
                                        <h3>Quick Reports</h3>
                                        <ul>
                                            <li><p><a href="javascript:void(0)" @click="statement(user)" style="color:blue; padding:2px">Customers Statement</a></p></li>
                                            <li><p><a href="javascript:void(0)" @click="sales(user)" style="color:blue; padding:2px">Customers Sales</a></p></li>
                                        </ul>
                                    </div>
                                </b-col>
                            </b-row>
                        </b-card-text>
                    </b-tab>

                    <b-tab title="Quotes">
                        <b-card-text>
                            <div style="border; height: 20em; overflow-y: auto; white-space: nowrap; padding:5px">
                                <div  v-if="quotes.length">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>S/N</th>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                            <th>Sales Rep</th>
                                            <th>Action</th>
                                        </tr>

                                        <tr  v-for="(order, index) in quotes">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ order.sale_id }}</td>
                                            <td>{{ order.main_date | myDate }}</td> 
                                            <td>{{ formatPrice(order.totalPrice)  }}</td>
                                            <td>{{ order.market_id }}</td>
                                            <td>
                                                <span v-if="order.status!='cancel'">
                                                    <a href="javascript:void(0)" @click=viewItems(order) style="color:blue">View</a> <span v-if="user.role==3">| <a href="javascript:void(0)" @click=cancel(order) style="color:blue">Cancel</a></span>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-else class="alert alert-danger text-center">
                                    No record of any quote found
                                </div>
                            </div>
                        </b-card-text>
                    </b-tab>

                    <b-tab title="Invoices">
                        <b-card-text>
                            <div style="border; height: 20em; overflow-y: auto; white-space: nowrap; padding:5px">
                                <div  v-if="invoices.length">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>S/N</th>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                            <th>Sales Rep</th>
                                            <th>Action</th>
                                        </tr>

                                        <tr  v-for="(order, index) in invoices">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ order.sale_id }}</td>
                                            <td>{{ order.main_date | myDate }}</td> 
                                            <td>{{ formatPrice(order.totalPrice)  }}</td>
                                            <td>{{ order.market_id }}</td>
                                            <td>
                                                <span v-if="order.status!='cancel'">
                                                    <a href="javascript:void(0)" @click=viewItems(order) style="color:blue">View</a> <span v-if="user.role==3">| <a href="javascript:void(0)" @click=cancel(order) style="color:blue">Cancel</a></span>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-else class="alert alert-danger text-center">
                                    No record of any invoice found
                                </div>
                            </div>
                        </b-card-text>
                    </b-tab>

                    <b-tab title="All Transactions">
                        <b-card-text>
                            <div style="border; height: 20em; overflow-y: auto; white-space: nowrap; padding:5px">
                                <div  v-if="all_orders.length>0">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>S/N</th>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Amount (<span v-html="nairaSign"></span>)</th>
                                            <th>Sales Rep</th>
                                            <th>Action</th>
                                        </tr>

                                        <tr  v-for="(order, index) in all_orders">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ order.sale_id }}</td>
                                            <td>{{ order.main_date | myDate }}</td> 
                                            <td>{{ formatPrice(order.totalPrice)  }}</td>
                                            <td>{{ order.market_id }}</td>
                                            <td>
                                                <span v-if="order.status!='cancel'">
                                                    <a href="javascript:void(0)" @click=viewItems(order) style="color:blue">View</a> <span v-if="user.role==3">| <a href="javascript:void(0)" @click=cancel(order) style="color:blue">Cancel</a></span>
                                                </span>
                                            </td>
                                        </tr>
                                        
                                    </table>
                                </div>
                                <div v-else class="alert alert-danger text-center">
                                    No record of any transaction found
                                </div>
                            </div>
                        </b-card-text>
                    </b-tab>
                </b-tabs>
            </b-card>

            <div class="modal fade" id="addNewCredit" tabindex="-1" role="dialog" aria-labelledby="addNewUserLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Increase Credit Level
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="updateCredit()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input v-model="credit.name" type="text" readonly="true" class="form-control"/>
                                </div>

                               
                                <div class="form-group">
                                    <label>Customer Credit Unit</label>
                                    <input v-model="credit.credit_unit" type="number" class="form-control"/>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>

                                <button type="submit" class="btn btn-success">
                                    Increase
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addNewWallet" tabindex="-1" role="dialog" aria-labelledby="addNewUserLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                Fund Customer
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="updateWallet()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Customer Name</label>
                                    <input v-model="wallet.name" type="text" readonly="true" class="form-control"/>
                                </div>

                               
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input v-model="wallet.amount" type="number" class="form-control"/>
                                </div>

                                <b-form-group label="Method of Fund:" label-for="payment_method">
                                    <select v-model="wallet.mop" class="form-control">
                                        <option v-for="option in options2" v-bind:value="option.value">
                                            {{ option.text }}
                                        </option>
                                    </select>
                                </b-form-group>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>

                                <button type="submit" class="btn btn-success">
                                    Top Up
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    import {debounce} from 'lodash';
    
    export default {
        data(){
            return {
                is_busy: false,
                user: '',
                orders: [],
                all_orders: [],
                invoices: [],
                quotes: [],
                nairaSign: "&#x20A6;",
                unique_id: '',
                order_count: '',
                value_order_count: '',
                funds: {},
                users: [],
                userQuery: '',
                credit: new Form({
                    name: '',
                    payer_id: '',
                    credit_unit: '',
                }),

                form: new Form({
                    user_id: '',
                }),

                wallet: new Form({
                    name: '',
                    payer_id: '',
                    mop: 'Cash',
                    amount: '',
                }),

                options: [
                    { text: 'Wallet', value: 'Wallet' },
                    { text: 'Credit Unit', value: 'Credit Unit' }
                ],

                options2: [
                    { text: 'Cash', value: 'Cash' },
                    { text: 'Transfer', value: 'Transfer' },
                ],
            }
        },

        created(){
            this.loadPage();
        },

        methods: {
            lookUser(){
                debounce(() => {

                    fetch('/api/searchcustomer', {params: this.userQuery})
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        this.users = data;

                    })
                }, 500)();
            },

            getUserID(data){

                if(this.is_busy) return;
                this.is_busy = true;

                this.unique_id = data.unique_id;
               
                axios.get('/api/customer/' + this.unique_id)
                .then((response) => {
                    if(response.data.error){
                        Swal.fire(
                            "Failed!",
                            response.data.error,
                            "warning"
                        );
                        this.$router.push({ path: "/admin/customers"});
                    }
                    else{
                        console.log(response.data)
                        this.user = response.data.user;
                        this.orders = response.data.orders;
                        this.all_orders = response.data.all_orders;
                        this.invoices = response.data.invoices;
                        this.quotes = response.data.quotes;
                        this.funds = response.data.funds;
                        this.order_count = response.data.order_count;
                        this.value_order_count = response.data.value_order_count;
                    }
                })

                .catch((err) => {
                    console.log(err);
                })

                .finally(() => {
                    this.is_busy = false;
                });
            },

            getResultsFund(page = 1) {

                axios.get('/api/customer/' + this.unique_id +'?page=' + page)
                .then(response => {
                    this.funds = response.data.funds;
                })
                .catch((err) => {
                        console.log(err);
                });
            },

            getResults(page = 1) {

                axios.get('/api/customer/' + this.unique_id +'?page=' + page)
                .then(response => {
                    this.orders = response.data.orders;
                })
                .catch((err) => {
                        console.log(err);
                });
            },

            loadPage(){
                if(this.is_busy) return;
                this.is_busy = true;

                this.unique_id = this.$route.params.unique_id;
                
                axios.get('/api/customer/' + this.unique_id)
                .then((response) => {
                    if(response.data.error){
                        Swal.fire(
                            "Failed!",
                            response.data.error,
                            "warning"
                        );
                        this.$router.push({ path: "/admin/customers"});
                    }
                    else{
                        console.log(response.data)
                        this.user = response.data.user;
                        this.orders = response.data.orders;
                        this.all_orders = response.data.all_orders;
                        this.invoices = response.data.invoices;
                        this.quotes = response.data.quotes;
                        this.funds = response.data.funds;
                        this.order_count = response.data.order_count;
                        this.value_order_count = response.data.value_order_count;
                    }
                })

                .catch((err) => {
                    console.log(err);
                })

                .finally(() => {
                    this.is_busy = false;
                });
            },

            formatPrice(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },

            viewItems(order) {
                this.$router.push({ path: "/orderview/" + order.sale_id });
            },

            statement(user) {
                this.$router.push({ name: 'statement', params: { unique_id: user.unique_id} });
                //this.$router.push({ path: "/customer/statement", params: { unique_id: user.unique_id } });
            },

            sales(user) {
                this.$router.push({ name: 'sales', params: { unique_id: user.unique_id} });
                //this.$router.push({ path: "/customer/sales/" + user.unique_id });
            },

            viewAll() {
                this.$router.push({ path: "/sale/orders/" + this.user.id });
            },

            CreditModal(customer) {
                this.credit.payer_id = customer.id;
                this.credit.name = customer.name;
                this.credit.credit_unit = customer.credit_unit;
                $("#addNewCredit").modal("show");
            },

            WalletModal(customer) {
                this.wallet.payer_id = customer.id;
                this.wallet.name = customer.name;
                $("#addNewWallet").modal("show");
            },

            updateCredit() {
                axios.post("/api/user/credituser", this.credit)

                .then((data) => {
                    $("#addNewCredit").modal("hide");
                    Swal.fire("Updated!", "Credit Topped Up Successfully.", "success");
                    this.loadPage();
                })

                .catch();
            },

            updateWallet() {
                axios.post("/api/user/walletuser", this.wallet)

                .then((data) => {
                    $("#addNewWallet").modal("hide");
                    Swal.fire("Updated!", "Customer Credited Successfully.", "success");
                    this.loadPage();
                })

                .catch();
            },
        },  
    }
</script>
<style>
    .active{
        color: #000 !important;
    }

    a{
        color: #000 !important;
    }
<style>