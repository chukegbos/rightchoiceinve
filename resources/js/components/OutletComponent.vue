<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid">
            
            <div class="row mb-2 p-2">
                <div class="col-md-4">
                    <h2><strong>List of Outlets</strong></h2>
                </div>

                <div class="col-md-8">
                    <b-button variant="outline-primary" size="sm" @click="newModal" class="pull-right m-2" v-if="user.store==1 && user.role=='Admin'">
                        Add Outlet
                    </b-button>

                    <!--<b-button size="sm" variant="outline-info"class="pull-right m-2" @click="onPrint"> <i class="fa fa-print"></i> Print</b-button>-->

               
                        <b-form @submit.stop.prevent="onFilterSubmit" class="pull-right m-2" size="sm">
                            <b-input-group>
                                <b-form-input id="name" v-model="filterForm.name" type="text" placeholder="Search store"></b-form-input>

                                <b-input-group-append>
                                    <b-button variant="outline-primary" type="submit"><i class="fa fa-search"></i></b-button>
                                </b-input-group-append>
                            </b-input-group>
                        </b-form>                        
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0" v-if="stores.data.length>0" id="printMe">
                    <div class="text-center" v-if="unprintable==true">
                        <h2>{{ site.sitename }} - List of stores</h2>
                    </div>
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th v-if="unprintable==false">
                                    <input type="checkbox" v-model="selectAll" v-if="user.store==1 && user.role=='Admin'">
                                </th>
                                
                                <th width="200px">Name</th>
                                <th>Location</th>
                                <th>Target</th>
                                <th>Stock Limit</th>
                                <th v-if="unprintable==false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="store in stores.data" :key="store.id">
                                <td v-if="unprintable==false"> <input type="checkbox" v-model="action.selected" :value="store.id" number v-if="user.store==1 && user.role=='Admin'"></td>
                               
                                <td>{{ store.name }} <span class="text-green" v-if="store.id==1"><strong>(Default)</strong></span></td>
                                <td>{{ store.address }}</td>
                                <td>{{ store.target }}</td>
                                <td>{{ store.stock_limit }}</td>
                                <td v-if="unprintable==false">
                                    <b-dropdown id="dropdown-right" text="Action" variant="info">
                                        <b-dropdown-item href="javascript:void(0)" @click="view(store)">View Store</b-dropdown-item>

                                        <b-dropdown-item href="javascript:void(0)" @click="viewroom(store)">View Show Room</b-dropdown-item>

                                      
                                        <hr>
                                        <span v-if="user.store==1 && user.role=='Admin'">
                                        <b-dropdown-item href="javascript:void(0)" @click="editModal(store)">Edit</b-dropdown-item>

                                        <b-dropdown-item href="javascript:void(0)" @click="onDeleteAll(store.id)" v-if="store.id!=1">Delete</b-dropdown-item>
                                        </span>
                                    </b-dropdown>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body" v-else>
                    <div class="alert alert-info text-center"><h3><strong>No store Found.</strong></h3></div>
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
                            <br> Total: <b>{{ count_all }} stores</b>
                        </div>

                        <div class="col-md-8" v-if="this.filterForm.selected!=0">
                            <pagination :data="stores" @pagination-change-page="getResults"></pagination>
                        </div>

                        <div class="col-md-2">
                            <b-button variant="outline-danger" size="sm" v-if="action.selected.length" class="pull-right" @click="onDeleteAll"><i class="fa fa-trash"></i> Delete Selected</b-button>

                            <b-button disabled size="sm" variant="outline-danger" v-else class="pull-right"> <i class="fa fa-trash"></i> Delete Selected</b-button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal fade" id="addNewstore" tabindex="-1" role="dialog" aria-labelledby="addNewstoreLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="addNewstoreLabel"
                                v-show="!editMode"
                            >
                                Add New Outlet
                            </h5>
                            <h5
                                class="modal-title"
                                id="addNewstoreLabel"
                                v-show="editMode"
                            >
                                Update Outlet Information
                            </h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form @submit.prevent="editMode ? updateStore() : createStore()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Name of Outlet <span class="text-danger pulll-right">*</span></label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        name="name"
                                        required
                                        class="form-control"
                                        placeholder="Outlet Name"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'name'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="name"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Address</label>
                                    <input
                                        v-model="form.address"
                                        type="text"
                                        name="address"
                                        class="form-control"
                                        placeholder="Address"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'address'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="address"
                                    ></has-error>
                                </div>


                                <div class="form-group">
                                    <label>Target</label>
                                    <input
                                        v-model="form.target"
                                        type="number"
                                        name="target"
                                        class="form-control"
                                        placeholder="Outlet Target"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'target'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="target"
                                    ></has-error>
                                </div>

                                <div class="form-group">
                                    <label>Stock Limit</label>
                                    <input
                                        v-model="form.stock_limit"
                                        type="number"
                                        name="stock_limit"
                                        class="form-control"
                                        placeholder="Outlet Stock Limit"
                                        :class="{
                                            'is-invalid': form.errors.has(
                                                'stock_limit'
                                            )
                                        }"
                                    />
                                    <has-error
                                        :form="form"
                                        field="stock_limit"
                                    ></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    data-dismiss="modal"
                                >
                                    Close
                                </button>
                                <button
                                    v-show="editMode"
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    Update
                                </button>
                                <button
                                    v-show="!editMode"
                                    type="submit"
                                    class="btn btn-primary"
                                >
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
            this.loadStores();
            this.getUser();
        },

        data() {
            return {
                is_busy: false,
                editMode: false,
                nairaSign: "&#x20A6;",
                model: {},
                stores: {},
                store: "",
                form: new Form({
                    id: "",
                    name: "",
                    address: "",
                    target: "",
                    stock_limit: "",
                }),

                filterForm: {
                    name: '',
                    selected: '10',
                },
                action: {
                    selected: []
                },
                stores: {
                    data: {},
                },
                count_all: '',
                unprintable: false,
                user: ''
            };
        },

        methods: {
            onChange(event) {
                this.filterForm.selected = event.target.value;
                this.loadStores();
                this.getUser();
            },

            loadStores() {
                if (this.is_busy) return;
                this.is_busy = true;

                axios.get("/api/store", { params: this.filterForm })
                .then(({ data }) => {
                    if(this.filterForm.selected==0)
                    {
                        this.stores.data = data.stores;
                    }
                    else{
                        this.stores = data.stores;
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

            newModal() {
                (this.editMode = false), this.form.reset();
                $("#addNewstore").modal("show");
            },

            getUser() {
                axios.get("/api/user").then(({ data }) => {
                    this.user = data.user;
                    this.stores = data.stores;
                });
            },

            onPrint() {
                if (this.is_busy) return;
                this.is_busy = true;
                this.unprintable = true;
                this.$htmlToPaper('printMe');
                //this.unprintable = false;
                this.is_busy = false;
                this.loadStores();
                this.getUser();
                
            },

            getResults(page = 1) {
                axios.get("/api/store?page=" + page, { params: this.filterForm })
                .then(response => {
                    this.stores = response.data.stores;
                });
            },

            editModal(store) {
                (this.editMode = true), this.form.reset();
                $("#addNewstore").modal("show");
                this.form.fill(store);
            },

            onFilterSubmit()
            {
                this.loadStores();
                this.getUser();
            },

            createStore() {
                if (this.is_busy) return;
                this.is_busy = true;
                $("#addNewstore").modal("hide");
                this.form.post("/api/store")
                .then(() => {
                    Swal.fire(
                        "Created!",
                        "Outlet Created Successfully.",
                        "success"
                    );
                    this.loadStores(); 
                    this.getUser();      
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

            updateStore() {
                if (this.is_busy) return;
                this.is_busy = true;
                $("#addNewstore").modal("hide");
                this.form.put("/api/store/" + this.form.id)
                .then(() => {
                   
                    Swal.fire("Updated!", "store Updated Successfully.", "success");
                                
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
                    this.loadStores(); 
                    this.getUser(); 
                });
            },

            formatPrice(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            },

            view(store) {
                this.$router.push({ path: "/outlets/" + store.code });
            },

            viewroom(store) {
                this.$router.push({ path: "/rooms/" + store.code });
            },

            viewsale(store) {
                this.$router.push({ path: "/sale/orders"  });
            },

            onDeleteAll(id) {
                if(id){
                    this.action.selected.push(id);
                }
                
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
                        axios.get('/api/store/delete', { params: this.action})
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Outlet(s) deleted.",
                                "success"
                            );
                            this.is_busy = false;
                            this.loadStores();
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
                    return this.stores.data ? this.action.selected.length == this.stores.data.length : false;
                },
                set: function (value) {
                    var selected = [];

                    if (value) {
                        this.stores.data.forEach(function (store) {
                            selected.push(store.id);
                        });
                    }

                    this.action.selected = selected;
                }
            }
        },
    };
</script>
