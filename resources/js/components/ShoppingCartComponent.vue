<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container mt-2">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Quote
                    </div>
                </div>

                <div class="card-body">
                    <b-form @submit.stop.prevent="is_edit ? updateForm() : onFormSubmit()">
                        <table class="table table-striped table-responsive-md text-center">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="100px">Quantity</th>
                                    <th>Available</th>
                                    <th>Unit Price (<span v-html="nairaSign"></span>)</th>
                                    <th>Total Price (<span v-html="nairaSign"></span>)</th>
                                    <!--<th width="120px">Discount</th>-->
                                    <th>
                                        
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr v-for="(item, index) in form.productItems">
                                    <td>
                                        <vue-typeahead-bootstrap
                                          v-model="item.product_name"
                                          :ieCloseFix="false"
                                          :data="products"
                                          :serializer="data => data.product_name"
                                          @hit="addProductItem($event, index)"
                                          placeholder="Search for product"
                                          @input="lookup(item)"
                                        />
                                    </td>
                                    <td>
                                        
                                        <b-form-input v-model="item.qty" max="item.number" type="number" class="form-control qty-input"  @input="updateText(item)"></b-form-input>
                                      
                                    </td>
                                    <td>
                                        {{ item.number }}
                                    </td>
                                    <td>{{ item.price }}</td>

                                    <td>
                                        
                                            {{ (item.qty * item.price ) - (item.qty * item.discount)}}
                                       
                                    </td>

                                    <!--<b-form-input v-model="item.discount" type="number" class="form-control qty-input" @input="updateDiscount(item)" :max="user.sale_percent"></b-form-input>-->

                                    <td>
                                        <a href="javascript:void(0)" @click="onRemoveProduct(form.productItems.indexOf(item))"><i class="fa fa-times text-red 2x"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                     
                        <table class="table table-striped table-responsive-md text-center">
                            <thead>
                                <tr>
                                    <!--<th>Select Account</th>-->
                                    <th>Select User</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <!--<th>
                                        <vue-typeahead-bootstrap
                                          v-model="typeQuery"
                                          :ieCloseFix="false"
                                          :data="accounts"
                                          :serializer="data => data.account"
                                          @hit="getAccountID($event)"
                                          placeholder="Search for Account"
                                          @input="lookType"
                                        />
                                    </th>-->

                                    <th>
                                        <vue-typeahead-bootstrap
                                          v-model="userQuery"
                                          :ieCloseFix="false"
                                          :data="users"
                                          :serializer="data => data.name"
                                          @hit="getUserID($event)"
                                          placeholder="Search for customer"
                                          @input="lookUser"
                                        />
                                        <b-button variant="info" size="sm" class="float-right mt-1 mb-1" v-on:click="CreateCustomer()">New Customer</b-button>
                                    </th>

                                    <th>
                                        Amount<br>
                                        Discount<br>

                                        VAT (7.5%)<br>
                                        Total Price
                                    </th>
                                    <th>
                                        <span v-html="nairaSign"></span>{{ formatPrice(form.amount) }}<br>
                                        <span v-html="nairaSign"></span>{{ formatPrice(form.discount) }}<br>

                                        <span v-html="nairaSign"></span>{{ formatPrice((7.5/100) * form.totalPrice) }}<br>
                                        <span v-html="nairaSign"></span>{{ formatPrice(Number(form.totalPrice) + Number((7.5/100) * form.totalPrice)) }}
                                    </th>
                                    <th>
                                        <br>
                                        <small>
                                            <a href="javascript:void(0)" @click="ApplyModal" style="color:blue">Apply Discount</a>
                                        </small>
                                    </th>

                                    <th>
                                        <b-button type="button" variant="primary" @click="onAddNewProduct" class="float-right" v-if="sale_id">
                                            <i class="fa fa-plus"></i>
                                        </b-button>

                                        <b-button type="button" variant="primary" @click="onAddProduct" class="float-right" v-else>
                                            <i class="fa fa-plus"></i>
                                        </b-button>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                        <b-button type="submit" variant="primary">Submit</b-button>
                    </b-form>
                </div>
            </div>

            <b-modal id="form-modal" ref="formCustomer" :title="'New Customer'" hide-footer>
                <b-form @submit.stop.prevent="onCustomerSubmit($event)">
                    <fieldset>
                        <b-row>
                            <b-col cols="12">
                                <b-form-group label="Full Name:" label-for="name">
                                    <b-form-input id="name" v-model="formCustomer.name" type="text"></b-form-input>
                                </b-form-group>
                            </b-col>

                            <b-col cols="12">
                                <b-form-group label="Address:" label-for="address">
                                    <b-form-input id="address" v-model="formCustomer.address" type="text"></b-form-input>
                                </b-form-group>
                            </b-col>

                            <b-col cols="12">
                                <b-form-group label="State:" label-for="state">
                                    <b-form-input id="state" v-model="formCustomer.state" type="text"></b-form-input>
                                </b-form-group>
                            </b-col>

                            <b-col cols="12" md="6">
                                <b-form-group label="Phone Number:" label-for="phone">
                                    <b-form-input id="phone" v-model="formCustomer.phone" type="tel"></b-form-input>
                                </b-form-group>
                            </b-col>

                            <b-col cols="12" md="6">
                                <b-form-group label="Email:" label-for="email">
                                    <b-form-input id="email" v-model="formCustomer.email" type="email"></b-form-input>
                                </b-form-group>
                            </b-col>

                            
                        </b-row>
                        <b-button type="submit" variant="primary">Save</b-button>
                    </fieldset>
                </b-form>
            </b-modal>

            <div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="addNewstoreLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Discount</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.stop.prevent="onApply">
                            <div class="modal-body">
                                <div class="form-group">
                                    <input v-model="discount" type="number" class="form-control"  :max="user.sale_percent"/>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">
                                    Apply
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
    import axios from 'axios';
    import {debounce} from 'lodash';

    export default {
        created(){
            this.getUser();
            this.onAddProduct();
        },

        data(){
            return{
                user: '',
                product: null,
                products: [],
                nairaSign: "&#x20A6;",
                typeQuery: '',
                accounts: [],
                sale_id: '',
                form: new Form({
                    user: '',
                    user_id: '',
                    productItems: [],
                    discount: 0,
                    amount: 0,
                    totalPrice: 0,
                    account: '',
                    mainprice: '',
                    vat: '',
                }),

                applied_discount: {
                    percent: '',
                },

                is_edit: false,
                edit_model: null,
                is_busy: false,

                userQuery: '',
                users: [],
                formCustomer: new Form({
                    name: '',
                    address: '',
                    email: '',
                    phone: '',
                }),
                discount: 0,
                options: [
                    { text: 'Cash', value: 'Cash' },
                    { text: 'Transfer', value: 'Transfer' },
                    { text: 'POS', value: 'POS' }
                ],
                sale: '',
                errors: [],
            }
        },

        methods: {
            getUser() {
                axios.get("/api/user")
                .then(({ data }) => {
                    this.user = data.user;
                    //this.discount = data.user.sale_percent;
                    this.store = data.store;
                });
            },

            lookup(item){
                debounce(() => {
                    fetch('/api/store/loadinventory', {params: item.product_name})
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        this.products = data;
                    })
                }, 500)();
            },

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

            lookType(){
                debounce(() => {

                    fetch('/api/account/search', {params: this.typeQuery})
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        this.accounts = data;
                    })
                }, 500)();
            },

            getAccountID(data){
                this.form.account = data.id;
            },

            onFormSubmit($event){
                /*if(this.form.account == ''){
                    Swal.fire(
                        "Oops",
                        "Please select account type, Preferably 'SALES'.",
                        "error"
                    );
                }
                else */

                if(this.form.user_id == ''){
                    Swal.fire(
                        "Oops",
                        "Please select customer.",
                        "error"
                    );
                }
                else{
                    axios.post('/api/cart/checkout', this.form)
                    .then((data)=>{
                        Swal.fire(
                            'Created!',
                            'Quote Created Successfully.',
                            'success'
                        )
                        this.$router.push({ path: '/orderview/' + data.data});
                    })

                    .catch((error) => {
                        console.log(error.errors)
                        console.log(this.errors)
                    }); 
                }
            },

            updateForm($event) {
                if (this.is_busy) return;
                this.is_busy = true;
                console.log(this.form)
                axios.put('/api/cart/checkout/' + this.sale.id, this.form)
                .then((data)=>{
                    Swal.fire(
                        'Created!',
                        'Quote Updated Successfully.',
                        'success'
                    )
                    this.$router.push({ path: '/orderview/' + data.data});
                })

                .catch((error) => {
                });
                this.is_busy = false;
            },

            onAddProduct(){
                this.sale_id = this.$route.params.sale_id;

                if (this.is_busy) return;
                this.is_busy = true;

                if(this.sale_id){
                    axios.get("/api/cart/getorder/" + this.sale_id)

                    .then((response) => {
                        console.log(response.data)
                        if(response.data.error){
                            axios.get('/api/customer/' + this.sale_id)
                            .then((data) => {

                                if(data.data.error){
                                    Swal.fire(
                                        "Failed!",
                                        'Nothing found',
                                        "warning"
                                    );
                                    this.$router.push({ path: "/admin/customers"});
                                }
                                else
                                {
                                    this.form.user_id = data.data.user.id;
                                    this.userQuery = data.data.user.name;
                                    debounce(() => {

                                        fetch('/api/searchcustomer', {params: this.userQuery})
                                        .then(response => {
                                            return response.json();
                                        })
                                        .then(data => {
                                            this.users = data;
                                        })
                                    }, 500)();

                                    this.form.productItems.push(this.setItemModel({}));

                                    console.log(this.userQuery)
                                }
                            })
                        }
                        else{
                            this.is_edit = true;
                            this.form.productItems = response.data.items;
                            this.form.discount = response.data.sale.discount;
                            this.form.totalPrice = response.data.sale.totalPrice;
                            this.form.amount = response.data.sale.initialPrice;
                            this.form.account = response.data.sale.account;
                            this.sale = response.data.sale;
                            this.form.user_id = response.data.customer.id;
                            this.userQuery = response.data.customer.name;
                            this.typeQuery = response.data.account.account;
                        }
                    })

                    .catch((err) => {
                        console.log(err);
                    });
                }
                else{
                    this.form.productItems.push(this.setItemModel({}));
                }  

                this.is_busy = false; 
            },

            onAddNewProduct(){
                this.form.productItems.push(this.setItemModel({}));  
            },

            onRemoveProduct(item_no)
            {
                this.form.productItems.splice(item_no,1);
                axios.post('/api/store/gettotal', this.form)

                .then(({ data }) => {
                    this.form.amount = data;
                    this.form.totalPrice = this.form.amount - this.form.discount;
                    console.log(this.form.amount)
                    console.log(data)
                })
                .catch();
            },

            updateText(item){


                axios.post('/api/store/gettotal', this.form)
                .then(({ data }) => {
                    this.form.amount = data;
                    this.form.totalPrice = this.form.amount - this.form.discount;

                    this.form.vat = (7.5/100) * this.form.totalPrice;
                    this.form.mainprice = Number(this.vat) + Number(this.form.totalPrice);
                })
                .catch();

            },

            updateDiscount(item){
                console.log(item.discount)
                
                    axios.post('/api/store/gettotal', this.form)

                    .then(({ data }) => {
                        this.form.amount = data;

                        console.log(data)
                        this.form.totalPrice = this.form.amount - this.form.discount;
                    })
                    .catch();
            },

            ApplyModal() {
                this.applied_discount.percent = this.user.sale_percent;
                $("#discountModal").modal("show");
            },

            onApply()
            {   
                this.form.discount = (this.discount/100) * this.form.amount;
                this.form.totalPrice = this.form.amount - this.form.discount;
                $("#discountModal").modal("hide");
            },

            getTotal(){
                axios.post('/api/store/gettotal', this.form)

                .then(({ data }) => {
                    this.form.amount = data;
                    this.form.totalPrice = this.form.amount - this.form.discount;
                })
                .catch()
                .finally(() => {
                    this.is_busy = false;
                });
            },

            addProductItem(product, index){
                let selectedItem = this.form.productItems[index];
           
                if((product.number <= 0) || (product.number == null)){
                    Swal.fire(
                        'Failed!',
                        'You do not have enough product!',
                        'error'
                    )                 
                }
                else{
                    this.setItemModel(selectedItem, product);
                    axios.post('/api/store/gettotal', this.form)

                    .then(({ data }) => {
                        this.form.amount = data;
                        this.form.totalPrice = this.form.amount - this.form.discount;
                    })
                    .catch();
                }
            },

            setItemModel(model, newModel){
                model.id = newModel !== undefined ? newModel.id: 0;
                model.price = newModel !== undefined ? newModel.price: 0;
                model.product_name = newModel !== undefined ? newModel.product_name: '';
                model.product_id = newModel !== undefined ? newModel.product_id: '';
                model.number = newModel !== undefined ? newModel.number: 0;
                model.discount = model.discount !== undefined ? model.discount: 0;
                model.qty = model.qty !== undefined ? model.number : 1;
                return model;
            },

            getUserID(data){
                this.form.user_id = data.id;
            },

            CreateCustomer()
            {
                this.formCustomer.name = '';
                this.formCustomer.address = '';
                this.formCustomer.email = '';
                this.formCustomer.phone = '';
                this.formCustomer.state = '';
                this.$refs.formCustomer.show();
            },

            onCustomerSubmit($event)
            {
                this.formCustomer.post("/api/customer")

                .then((data) => {
                    this.$refs.formCustomer.hide();
                    Swal.fire(
                        "Created!",
                        "Customer Created Successfully.",
                        "success"
                    );
                })

                .catch((err) => {
                    this.show_error(err.response.data.errors);
                });
            },

            formatPrice(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },
        },
     }  
</script>


