<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container mb-2">
            <div class="row mt-2">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <button v-if="sale.approved==1 && sale.status=='pending' && user.role!='Marketing'" class="btn btn-info btn-sm mb-2"  @click="newModal">Complete Transaction</button>

                    <span v-if="this.user.invoice==1 && sale.approved!=1 && sale.status=='pending'">
                        <button class="btn btn-info btn-sm mb-2"  @click="approve(sale)">Approve as Invoice</button>
                    </span>
                    <button v-if="sale.status=='pending' && user.role!='Accounting'" class="btn btn-primary btn-sm mb-2"  @click="editInvoice">Edit</button>

                    <button @click=onPrint class="btn btn-success btn-sm mb-2">Print</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10" id="printMe">
                    <div class="card">
                        <div class="card-header text-center">
                            <h4>{{ site.sitename }}</h4>
                            <p>{{ site.address }}</p>
                        </div>

                       
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4 v-if="sale.status=='pending' && sale.approved==1"><strong>Invoice</strong></h4>
                                    <h4 v-else-if="sale.status=='concluded'"><strong>List of Items</strong><br>
                                    <small> <a href="javascript:void(0)" @click="viewRec()">View Receipt</a></small>
                                    </h4>
                                    <h4 v-else><strong>Quote</strong></h4>
                                </div>
                                <div class="col-md-7">
                                    <div class="p-1 mb-3">
                                        <span v-if="sale.status=='pending' && sale.approved==1">Invoice No</span>
                                        <span v-else>Quote No</span>: <b>{{ sale.sale_id }}</b><br>
                                        Marketer: <b>{{ sale.marketer }}</b><br>
                                        Date: <b>{{ sale.created_at | myDate }}</b><br>

                                        Status
                                        <span v-if="sale.status=='concluded'" class="badge badge-success">Paid</span>

                                        <span v-if="sale.status=='pending'" class="badge badge-danger">Unpaid</span>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="p-1 mb-3 pull-right">
                                        Customer: <b>{{ customer.name }}</b><br>
                                        Contact Person: <b>{{ customer.c_person }}</b><br>
                                        Phone: <b>{{ customer.phone}}</b><br>
                                        Address: <b>{{ customer.address }} {{ customer.state }}</b><br>
                                    </div>
                                </div>
                            
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price (<span v-html="nairaSign"></span>)</th>
                                                    <!--<th>Discount (<span v-html="nairaSign"></span>)</th>-->
                                                    <th class="pull-right">Amount (<span v-html="nairaSign"></span>)</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                <tr v-for="(item, index) in items">
                                                    <td>{{ index + 1}}</td>
                                                    <td>{{ item.product_name}}</td>
                                                    <td>{{ item.qty }}</td>
                                                    <td>{{ formatPrice(item.price) }}</td>
                                                    <!--<td>{{ formatPrice(item.discount) }}</td>-->
                                                    <td class="pull-right">{{ formatPrice(item.qty*item.price) }}</td>
                                                </tr>                                
                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>Discount</th>
                                                    <!--<th></th>-->
                                                    <th class="pull-right">{{ formatPrice(sale.discount) }}</th>
                                                </tr>

                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>Amount</th>
                                                    <!--<th></th>-->
                                                    <th class="pull-right">{{ formatPrice(this.sale.totalPrice) }}</th>
                                                </tr>

                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>VAT (7.5%)</th>
                                                    <!--<th></th>-->
                                                    <th class="pull-right">{{ formatPrice(vat) }}</th>
                                                </tr>

                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>Total Price</th>
                                                    <!--<th></th>-->
                                                    <th class="pull-right">{{ formatPrice(mainprice) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--<div class="card-footer">
                            <div class="pull-left">{{ site.phone }}</div>
                            <div class="pull-right">{{ site.email }}</div>
                        </div>-->
                    </div>
                </div>
            </div> 
        </div>

        <div class="modal fade" id="addNewUser" tabindex="-1" role="dialog" aria-labelledby="addNewUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewUserLabel">
                            Conclude Transaction
                        </h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="updateDeal()">
                        <div class="modal-body">
             
                            <div class="form-group">
                                <label>Amount</label>
                                <input v-model="form.totalPrice" type="text" readonly="true" class="form-control"/>
                                <input v-model="form.sale_id" type="hidden" readonly="true" class="form-control"/>
                            </div>

                            <div class="form-group">
                                <label>Customer Name</label>
                                <input v-model="customer.name" type="text" readonly="true" class="form-control"/>
                            </div>

                            

                            <b-form-group label="Payment Method:" label-for="payment_method">
                                <select v-model="form.payment_method" class="form-control" @change="onChange($event)">
                                    <option v-for="option in options" v-bind:value="option.value">
                                        {{ option.text }}
                                    </option>
                                </select>
                            </b-form-group>
                           
                            <div class="form-group" v-if="sec_id">
                                <label>Date of Payment</label>
                                <input v-model="form.date" type="date" class="form-control" />
                            </div>

                            <div class="form-group" v-else>
                                <label>Customer Account Balance</label>
                                <input v-model="customer.wallet_balance" type="text" readonly="true" class="form-control"/>
                                <small><a href="javascript:void(0)" @click="WalletModal(customer)" class="pull-right">Fund Account</a></small>
                            </div>

                            <div class="form-group">
                                <label>Debit</label>
                                <v-select label="account" :options="accounts" @input="setSelected" ></v-select>
                            </div>

                            <div class="form-group">
                                <label>Credit</label>
                                <v-select label="account" :options="accounts" @input="setSec" ></v-select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-success">
                                Conclude
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="markUser" tabindex="-1" role="dialog" aria-labelledby="markUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addNewUserLabel">
                            Mark as Invoice
                        </h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="updateCreditSale()">
                        <div class="modal-body">
             
                            <div class="form-group">
                               
                                <b-form-checkbox v-model="account.credit" value="1" unchecked-value="0"></b-form-checkbox> On Credit?
                            </div>

                            <div class="form-group" v-if="account.credit==1">
                                <label>Date of Payment</label>
                                <input v-model="account.date" type="date" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label>Debit</label>
                                <v-select label="account" :options="accounts" @input="setSelected" ></v-select>
                            </div>

                            <div class="form-group">
                                <label>Credit</label>
                                <v-select label="account" :options="accounts" @input="setSec" ></v-select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-success">
                                Set
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addNewCredit" tabindex="-1" role="dialog" aria-labelledby="addNewUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Adjust Credit Level
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
                               Adjust
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

                            <div class="form-group">
                                <label v-if="wallet.mop=='Cash'">Receipt Number</label>
                                <label v-else-if="wallet.mop=='Transfer'">Transfer ID</label>
                                <label v-else>Cheque Number</label>
                                <input v-model="wallet.tran_type" type="text" class="form-control" required/>
                            </div>

                            <div class="form-group">
                                <label>Debit</label>
                                <v-select label="account" :options="accounts" @input="fundSelected" ></v-select>
                            </div>

                            <div class="form-group">
                                <label>Credit</label>
                                <v-select label="account" :options="accounts" @input="fundSec" ></v-select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                Close
                            </button>

                            <button type="submit" class="btn btn-success">
                                Fund
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </b-overlay>
</template>

<script>
    import VueBootstrap4Table from 'vue-bootstrap4-table';
    export default {
    created() {
        this.viewOrder();
        this.loadSite();
        this.getUser();
    },

    data() {
        return {
            is_busy: false,
            sale_id: '',
            sale: '',
            sec_id: '',
            ready: '',
            items: {},
            totalPrice: '',
            site: "",
            user: "",
            vat: "",
            mainprice: "",
            customer: '',
            form: new Form({
                id: "",
                user: '',
                wallet_balance: '',
                credit_unit: '',
                sale_id: '',
                totalPrice: '',
                payment_method: '',
                date: '',
                account_one: "",
                account_two: "",
            }),
            accounts: [],
            nairaSign: "&#x20A6;",

            account: new Form({
                credit: '',
                date: '',
                account_one: "",
                account_two: "",
            }),

            credit: new Form({
                name: '',
                payer_id: '',
                credit_unit: '',
            }),

            wallet: new Form({
                name: '',
                payer_id: '',
                mop: 'Cash',
                amount: '',
                account_one: "",
                account_two: "",
            }),

            options: [
                { text: 'Account Balance', value: 'Wallet' },
                { text: 'Credit', value: 'Credit Unit' }
            ],

            options2: [
                { text: 'Cash', value: 'Cash' },
                { text: 'Bank Transfer', value: 'Transfer' },
                { text: 'Cheque', value: 'Cheque' },
            ],
        };
    },

    methods: {
        getUser() {
            axios.get("/api/user")
            .then(({ data }) => {
                this.user = data.user;
                this.accounts = data.accounts;
            });
        },

        setSelected(value) {
            this.form.account_one = value.id;
        },

        setSec(value) {
            this.form.account_two = value.id;
        },

        fundSelected(value) {
            this.wallet.account_one = value.id;
        },

        fundSec(value) {
            this.wallet.account_two = value.id;
        },

        viewRec() {
            this.$router.push({ path: "/receipt/" + this.sale_id });
        },

        viewOrder() {
            if(this.is_busy) return;
            this.is_busy = true;

            this.sale_id = this.$route.params.sale_id;

            axios.get("/api/cart/getorder/" + this.sale_id)

            .then((response) => {
                this.sale = response.data.sale
                this.form = response.data.sale;
                this.customer = response.data.customer;
                this.items = response.data.items;
                this.vat = (7.5/100) * this.sale.totalPrice;
                this.mainprice = Number(this.vat) + Number(this.sale.totalPrice);
                //this.form.totalPrice = this.mainprice;
                console.log(this.items)
            })

            .catch((err) => {
                console.log(err);
            })

            .finally(() => {
                this.is_busy = false;
            });
        },

        loadSite() {
            axios.get("/api/setting")
            .then(({ data }) => {
                this.site = data;
            });
        },

        onPrint() {
            this.$htmlToPaper('printMe');
        },

        newModal() {
            $("#addNewUser").modal("show");
        },

        markModal() {
            $("#markUser").modal("show");
        },

        CreditModal(customer) {
            this.credit.payer_id = customer.id;
            this.credit.name = customer.name;
            this.credit.credit_unit = customer.credit_unit;
            $("#addNewUser").modal("hide");
            $("#addNewCredit").modal("show");
        },

        WalletModal(customer) {
            this.wallet.payer_id = customer.id;
            this.wallet.name = customer.name;
            $("#addNewUser").modal("hide");
            $("#addNewWallet").modal("show");
        },

        onChange($event){
            let value = event.target.value;
            console.log(value)
            if (value == "Credit Unit")
            {
                this.sec_id = 1;
                if(this.customer.credit_unit >= this.form.totalPrice){
                    this.ready = 1;
                }
                else{
                    this.ready = '';
                }
            }
            else
            {
                this.sec_id = '';
                if(this.customer.wallet_balance >= this.form.totalPrice){
                    this.ready = 1;
                }
                else{
                    this.ready = '';
                }
            }
        },

        updateDeal() {
            console.log(this.form)
            if(!this.form.payment_method){
                Swal.fire(
                    "Failed!",
                    'Please select payment method',
                    "warning"
                );
            }
            else{
                axios.post("/api/cart/closedeal", this.form)
                .then((data) => {
                    $("#addNewUser").modal("hide");
                    if(data.data.error){
                        Swal.fire(
                            "Failed!",
                            data.data.error,
                            "warning"
                        );
                    }
                    else{
                        Swal.fire("Updated!", "Deal Closed Successfully.", "success");
                    }

                    this.viewOrder();
                    this.loadSite();
                })
                .catch();
            }
        },

        updateCredit() {
            console.log(this.credit);
            axios.post("/api/user/credituser", this.credit)

            .then((data) => {
                $("#addNewCredit").modal("hide");

                Swal.fire("Updated!", "Credit Topped Up Successfully.", "success");
                this.viewOrder();
                this.loadSite();
                this.getUser();
                this.form.payment_method = 'Credit Unit';
                $("#addNewUser").modal("show");
            })

            .catch();
        },

        updateCreditSale() {
            console.log(this.credit);
            axios.post("/api/user/credituser", this.account)

            .then((data) => {
                $("#addNewCredit").modal("hide");

                Swal.fire("Updated!", "Approve.", "success");
                this.viewOrder();
                this.loadSite();
                this.getUser();
               
            })

            .catch();
        },

        approve(sale){
            axios.post("/api/cart/approvequote", this.form)

            .then((data) => {
                Swal.fire("Updated!", "Quote approved as invoice.", "success");
                this.viewOrder();
                this.loadSite();
            })

            .catch();
        },

        updateWallet() {
            console.log(this.wallet);
            axios.post("/api/user/walletuser", this.wallet)

            .then((data) => {
                $("#addNewWallet").modal("hide");

                Swal.fire("Updated!", "Customer Funded Successfully.", "success");
                this.viewOrder();
                this.loadSite();
                this.getUser();
                this.form.payment_method = 'Wallet';
                $("#addNewUser").modal("show");
            })

            .catch();
        },

        editInvoice() {
            this.$router.push({ path: "/sale/shopping-cart/" + this.sale.sale_id });
        },

        formatPrice(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }
    }
};
</script>
