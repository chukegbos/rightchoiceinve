<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid">
            
            <div class="row mb-2 p-2">
                <div class="col-md-4">
                    <h2><strong>List of Customers</strong></h2>
                </div>

                <div class="col-md-8">
                    <!--<b-dropdown id="dropdown-right" size="sm" right text="Quick Reports" variant="primary" class="pull-right m-2">
                        <b-dropdown-item href="javascript:void(0)">Sales by Customer</b-dropdown-item>
                        <b-dropdown-divider></b-dropdown-divider>
                        <b-dropdown-item href="javascript:void(0)">Customer Balance-Days Outstanding</b-dropdown-item>
                        <b-dropdown-item href="javascript:void(0)">Customer Statement</b-dropdown-item>

                        <b-dropdown-item href="javascript:void(0)">Customer Transactions</b-dropdown-item>
                        <b-dropdown-item href="javascript:void(0)">Customer Unallocated Receipts</b-dropdown-item>
                    </b-dropdown>-->

                    <b-button variant="outline-primary" size="sm" @click="newModal" class="pull-right m-2">
                        Add A Customer
                    </b-button>

                    <b-button size="sm" variant="outline-info"class="pull-right m-2" @click="onPrint"> <i class="fa fa-print"></i> Print</b-button>

               
                        <b-form @submit.stop.prevent="onFilterSubmit" class="pull-right m-2" size="sm">
                            <b-input-group>
                                <b-form-input id="name" v-model="filterForm.name" type="text" placeholder="Search Customer"></b-form-input>

                                <b-input-group-append>
                                    <b-button variant="outline-primary" type="submit"><i class="fa fa-search"></i></b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </b-form>                        
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0" v-if="users.data.length>0" id="printMe">
                    <div class="text-center" v-if="unprintable==true">
                        <h2>{{ site.sitename }} - List of Customers</h2>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th v-if="unprintable==false"><input type="checkbox" v-model="selectAll"></th>
                                <!--<th>Acc ID</th>-->
                                <th width="200px">Name</th>
                                <th width="200px">Contact Person</th>
                                <th>Email</th>
                                <th width="200px">Phone</th>
                                <!--<<th>Location</th>
                                th width="120px">Credit Load</th>
                                <th>Wallet Balance</th>-->
                                <th v-if="unprintable==false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id">
                                <td v-if="unprintable==false"> <input type="checkbox" v-model="action.selected" :value="user.id" number></td>
                                <!--<td>{{ user.unique_id }}</td>-->
                                <td>{{ user.name }}</td>
                                <td>{{ user.c_person }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.phone }}</td>
                                <!--<td>
                                    <span v-if="user.credit_unit">
                                        <span v-html="nairaSign"></span>{{ formatPrice(user.credit_unit) }}
                                    </span>
                                    <span v-else>-</span>
                                </td>

                                <td>
                                    <span v-if="user.wallet_balance">
                                        <span v-html="nairaSign"></span>{{ formatPrice(user.wallet_balance) }}
                                    </span>
                                    <span v-else>
                                        <span v-html="nairaSign"></span> 0.00
                                    </span>
                                </td>
                                <td>{{ user.address }} {{ user.city }} {{ user.state }}</td>-->
                                <td v-if="unprintable==false">
                                    <b-dropdown id="dropdown-right" text="Action" variant="info">
                                        <b-dropdown-item href="javascript:void(0)" @click="view(user)">View</b-dropdown-item>

                                        <b-dropdown-item href="javascript:void(0)" @click="editModal(user)">Edit</b-dropdown-item>

                                        <b-dropdown-item href="javascript:void(0)" @click="deleteUser(user.id)">Delete</b-dropdown-item>
                                        <hr>
                                        <b-dropdown-item href="javascript:void(0)" @click="createInvoice(user)">Create Invoice</b-dropdown-item>
                                    </b-dropdown>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body" v-else>
                    <div class="alert alert-info text-center"><h3><strong>No Customer Found.</strong></h3></div>
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
                            <br> Total: <b>{{ count_all }} Customers</b>
                        </div>

                        <div class="col-md-8" v-if="this.filterForm.selected!=0">
                            <pagination :data="users" @pagination-change-page="getResults"></pagination>
                        </div>

                        <div class="col-md-2">
                            <b-button variant="outline-danger" size="sm" v-if="action.selected.length" class="pull-right" @click="onDeleteAll"><i class="fa fa-trash"></i> Delete Selected</b-button>

                            <b-button disabled size="sm" variant="outline-danger" v-else class="pull-right"> <i class="fa fa-trash"></i> Delete Selected</b-button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="addNewUser" tabindex="-1" role="dialog" aria-labelledby="addNewUserLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addNewUserLabel" v-show="!editMode">
                                Add New
                            </h5>
                            <h5 class="modal-title" id="addNewUserLabel" v-show="editMode">
                                Update User's Info
                            </h5>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editMode ? updateUser() : createUser()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input v-model="form.name" type="text"
                                        name="name" class="form-control" placeholder="Fullname" :class="{'is-invalid': form.errors.has('name')}"/>
                                    <has-error :form="form" field="name"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input v-model="form.email" type="email" name="email" class="form-control" placeholder="Email Address"
                                        :class="{'is-invalid': form.errors.has('email')}"/>
                                    <has-error :form="form" field="email"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="form-control"
                                        placeholder="Phone Number"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'phone'
                                            )
                                        }"
                                    />
                                    <has-error :form="form" field="phone"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <input v-model="form.address" type="text"
                                        class="form-control"
                                        placeholder="Address"
                                        :class="{'is-invalid': form.errors.has('address')}"
                                    />
                                    <has-error :form="form" field="address"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>State</label>
                                    <input v-model="form.state" type="text"
                                        class="form-control"
                                        placeholder="State"
                                        :class="{'is-invalid': form.errors.has('state')}"
                                    />
                                    <has-error :form="form" field="state"></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Credit Unit</label>
                                    <input v-model="form.credit_unit" type="number" step=".01"
                                        class="form-control"
                                        :class="{'is-invalid': form.errors.has('credit_unit')}"
                                    />
                                    <has-error :form="form" field="credit_unit"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">
                                    Close
                                </button>
                                <button v-show="editMode" type="submit" class="btn btn-success">
                                    Update
                                </button>
                                <button v-show="!editMode" type="submit" class="btn btn-primary">
                                    Create
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
export default {
    created() {
        this.loadUsers();
        this.getUser();
    },

    data() {
        return {
            is_busy: false,
            editMode: false,
            nairaSign: "&#x20A6;",
            model: {},
            users: {},
            user: "",
            form: new Form({
                id: "",
                name: "",
                email: "",
                phone: "",
                address: "",
                state: "",
                credit_unit: "",
            }),

            filterForm: {
                name: '',
                selected: '5',
            },
            action: {
                selected: []
            },
            users: {
                data: {},
            },
            count_all: '',
            unprintable: false,
        };
    },

    methods: {
        onChange(event) {
            this.filterForm.selected = event.target.value;
            this.loadUsers();
            this.getUser();
        },

        onPrint() {
            if (this.is_busy) return;
            this.is_busy = true;
            this.unprintable = true;
            this.$htmlToPaper('printMe');
            //this.unprintable = false;
            this.is_busy = false;
            this.loadUsers();
            this.getUser();
        },

        getUser() {
            axios.get("/api/user").then(({ data }) => {
                this.user = data.user;
                this.usersss = data.user;
            });
        },

        getResults(page = 1) {
            axios.get("/api/customer?page=" + page, { params: this.filterForm })
            .then(response => {
                this.users = response.data.customers;
            });
        },

        editModal(user) {
            this.$router.push({ path: "/admin/customers/create/" + user.id});
        },

        newModal() {
            this.$router.push({ path: "/admin/customers/create"});
        },

        loadUsers() {
            if (this.is_busy) return;
            this.is_busy = true;

            axios.get("/api/customer", { params: this.filterForm })
            .then(({ data }) => {
                if(this.filterForm.selected==0)
                {
                    this.users.data = data.customers;
                }
                else{
                    this.users = data.customers;
                }
                this.count_all = data.all;
            })
            .finally(() => {
                this.is_busy = false;
            });
        },

        onFilterSubmit()
        {
            this.loadUsers();
            this.getUser();
        },

        createUser() {
            this.form.post("/api/customer")
            .then(() => {
                $("#addNewUser").modal("hide");

                Swal.fire(
                    "Created!",
                    "Customer Created Successfully.",
                    "success"
                );
                this.loadUsers();
                this.getUser();
            })
            .catch(() => {
                Swal,fire(
                    "Failed!",
                    "Ops, Something went wrong, try again.",
                    "warning"
                );
            });
        },

        updateUser() {
            this.form.put("/api/customer/" + this.form.id)

            .then(() => {
                $("#addNewUser").modal("hide");

                Swal.fire("Updated!", "Customer Updated Successfully.", "success");
                this.$Progress.finish();
                this.loadUsers();
                this.getUser();
            })

            .catch();
        },

        deleteUser(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            })
            .then(result => {
                if (result.value) {
                    this.$Progress.start();
                    this.form.delete("/api/customer/" + id)
                    .then(() => {
                        Swal.fire(
                            "Deleted!",
                            "Customer has been deleted.",
                            "success"
                        );
                        this.$Progress.finish();
                        this.loadUsers();
                        this.getUser();
                    })

                    .catch(() => {
                        Swal(
                            "Failed!",
                            "Ops, Something went wrong, try again.",
                            "warning"
                        );
                    });
                }
            });
        },

        createInvoice(user){
            this.$router.push({ path: "/sale/shopping-cart/" + user.unique_id});
        },

        formatPrice(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },

        view(user) {
            this.$router.push({ path: "/admin/customers/" + user.unique_id });
        },

        onDeleteAll(id) {
                
                if(id){
                    this.action.selected.push(id);
                }

                console.log(this.action)
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                })
                .then(result => {
                    if (result.value) {
                        if (this.is_busy) return;
                        this.is_busy = true;
                        axios.get('/api/customer/delete', { params: this.action})
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Customer(s) deleted.",
                                "success"
                            );
                            this.is_busy = false;
                            this.loadUsers();
                            this.getUser();
                        })

                        .catch(() => {
                            Swal.fire(
                                "Failed!",
                                "Ops, Something went wrong, try again.",
                                "warning"
                            );
                            this.is_busy = false;
                        });
                    }
                });
        },

    },

    computed: {
        selectAll: {
            get: function () {
                return this.users.data ? this.action.selected.length == this.users.data.length : false;
            },
            set: function (value) {
                var selected = [];

                if (value) {
                    this.users.data.forEach(function (user) {
                        selected.push(user.id);
                    });
                }

                this.action.selected = selected;
            }
        }
    },
};
</script>
