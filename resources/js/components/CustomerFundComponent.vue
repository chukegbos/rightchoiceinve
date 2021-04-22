<template>
    <b-overlay :show="is_busy" rounded="sm">   
        <div class="container-fluid">
            <div class="row mb-2 p-1">
                <div class="col-md-12">
                    <h2><strong>Fund Customer's Balance</strong></h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="updateWallet()">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Customer</label>
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
                                Top Up
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    import {debounce} from 'lodash';
    export default {
        data() {
            return {
                is_busy: false,
                userQuery: '',
                wallet: new Form({
                    payer_id: '',
                    mop: 'Cash',
                    amount: '',
                    tran_type: '',
                    account_one: "",
                account_two: "",
                }),
                users: [],
                options: [
                    { text: 'Wallet', value: 'Wallet' },
                    { text: 'Credit Unit', value: 'Credit Unit' }
                ],
                accounts: [],
                options2: [
                    { text: 'Cash', value: 'Cash' },
                    { text: 'Bank Transfer', value: 'Transfer' },
                    { text: 'Cheque', value: 'Cheque' },
                ],
            };
        },

        created() {
            this.getUser();
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
                this.wallet.payer_id = data.id;
            },

            getUser() {
            axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                    this.accounts = data.accounts;
                });
            },

            setSelected(value) {
                this.wallet.account_one = value.id;
            },

            setSec(value) {
                this.wallet.account_two = value.id;
            },

            updateWallet() {


                if(this.is_busy) return;
                this.is_busy = true;

                axios.post("/api/user/walletuser", this.wallet)

                .then((data) => {
                    if(data.data.error){
                        Swal.fire(
                            "Failed!",
                            data.data.error,
                            "warning"
                        );
                    }
                    else{
                        Swal.fire("Updated!", "Customer Credited Successfully.", "success");
                        this.wallet = '';
                        this.$router.push({ path: "/admin/customers"});
                    }
                })

                .catch();
                this.is_busy = false;
            },
         
        },
    };
</script>
