<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid">
            <div class="row mb-2 p-2">
                <div class="col-md-4">
                    <h2><strong>List of Staff</strong></h2>
                </div>

                <div class="col-md-8">
                    <b-button variant="outline-primary" size="sm" @click="newModal" class="pull-right m-2" v-if="userss.role=='Admin'">
                        Add A Staff
                    </b-button>

                    <!--<b-button size="sm" variant="outline-info"class="pull-right m-2"> <i class="fa fa-print"></i> Print</b-button>-->

               
                    <b-form @submit.stop.prevent="onFilterSubmit" class="pull-right m-2" size="sm">
                        <b-input-group>
                            <b-form-input id="name" v-model="filterForm.name" type="text" placeholder="Search Staff"></b-form-input>

                            <b-input-group-append>
                                <b-button variant="outline-primary" type="submit"><i class="fa fa-search"></i></b-button>
                            </b-input-group-append>
                        </b-input-group>
                    </b-form>                        
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0" v-if="users.data.length>0">
                    <table class="table table-hover">
                        <tr>
                            <th><input type="checkbox" v-model="selectAll"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <!--<th>Salary (<span v-html="nairaSign"></span>)</th>-->
                            <th width="400px">Address</th>
                            <th>Action</th>
                        </tr>

                        <tr v-for="user in users.data" :key="user.id">
                            <td> <input type="checkbox" v-model="action.selected" :value="user.id" number></td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.phone }}</td>
                            <td>{{ user.role }}</td>
                            <!--<td>{{ formatPrice(user.salary) }}</td>-->
                            <td>{{ user.address }}</td>
                            <td>
                                <b-dropdown id="dropdown-right" text="Action" variant="info">
                                    <b-dropdown-item href="javascript:void(0)" @click="view(user)">View</b-dropdown-item>

                                    <b-dropdown-item href="javascript:void(0)" @click="editModal(user)">Edit</b-dropdown-item>

                                    <b-dropdown-item href="javascript:void(0)" @click="deleteUser(user.id)" v-if="user.id!=userss.id && user.id!=1">Delete</b-dropdown-item>
                                </b-dropdown>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="card-body" v-else>
                    <div class="alert alert-info text-center"><h3><strong>No Staff Found.</strong></h3></div>
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
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
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
                            <div class="modal-body row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input v-model="form.name" type="text"
                                            name="name" class="form-control" placeholder="Fullname" :class="{'is-invalid': form.errors.has('name')}"/>
                                        <has-error :form="form" field="name"></has-error>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input v-model="form.email" type="email" name="email" class="form-control" placeholder="Email Address"
                                            :class="{'is-invalid': form.errors.has('email')}"/>
                                        <has-error :form="form" field="email"></has-error>
                                    </div>
                                </div>

                                <div class="col-md-4">
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
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <input v-model="form.address" type="text"
                                            class="form-control"
                                            placeholder="Home Address"
                                            :class="{'is-invalid': form.errors.has('address')}"
                                        />
                                        <has-error :form="form" field="address"></has-error>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <b-form-group label="Store:">
                                        <v-select label="name" :options="stores" @input="setSelected" ></v-select>
                                    </b-form-group>
                                </div>

                                <!--<div class="col-md-4">
                                    <div class="form-group">
                                        <label>Salary</label>
                                        <input v-model="form.salary" type="number"
                                            class="form-control"
                                            :class="{'is-invalid': form.errors.has('salary')}"
                                        />
                                        <has-error :form="form" field="salary"></has-error>
                                    </div>
                                </div>-->

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Role</label>
                                        <b-form-select
                                            v-model="form.role"
                                            :options="role"
                                            value-field="value"
                                            text-field="text"
                                        >
                                        <template v-slot:first>
                                            <b-form-select-option :value="null" disabled>
                                                -- Please select a role--
                                            </b-form-select-option>
                                        </template>
                                        </b-form-select>
                                        <b-form-checkbox
                                          id="checkbox-1"
                                          v-model="form.invoice"
                                          name="checkbox-1"
                                          value="1"
                                          unchecked-value="0"
                                        >
                                          Can Approve Invoice
                                        </b-form-checkbox>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" v-if="show_password==1">
                                        <label>Password</label>
                                        <input
                                            v-model="form.password"
                                            placeholder="Password"
                                            type="password"
                                            name="password"
                                            class="form-control"
                                            :class="{'is-invalid': form.errors.has('password')}"
                                        />
                                        <has-error :form="form" field="password"></has-error>
                                        <a href="#" class="pull-right" @click="setPassword"  v-show="editMode">Hide Password</a>
                                    </div>

                                    <a href="#" class="pull-right" @click="unsetPassword" v-else>Change Password</a>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Next of Kin Name</label>
                                        <input v-model="form.next_of_kin" type="text"
                                            name="next_of_kin" class="form-control" placeholder="Next of Kin Name" :class="{'is-invalid': form.errors.has('next_of_kin')}"/>
                                        <has-error :form="form" field="next_of_kin"></has-error>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Next of Kin Address</label>
                                        <input v-model="form.next_of_kin_address" type="text" name="next_of_kin_address" class="form-control" placeholder="Next of Kin Address"
                                            :class="{'is-invalid': form.errors.has('next_of_kin_address')}"/>
                                        <has-error :form="form" field="next_of_kin_address"></has-error>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Next of Kin Phone</label>
                                        <input
                                            v-model="form.next_of_kin_phone"
                                            type="tel"
                                            class="form-control"
                                            placeholder="Next of Kin Phone Number"
                                            :class="{
                                                'is-invalid': form.errors.has(
                                                    'next_of_kin_phone'
                                                )
                                            }"
                                        />
                                        <has-error :form="form" field="next_of_kin_phone"></has-error>
                                    </div>
                                </div>


                                <!--<div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Photo Image</label>
                                        <input type="file" @change="uploadImage" accept="image/*" name="image" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6" v-if="form.image">
                                    <div class="my-2">
                                        <img :src="form.image" class="img-fluid" style="height:100px; width:150px">
                                    </div>
                                </div>-->
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
                stores: [],
                show_password: 1,
                userss: "",
                form: new Form({
                    id: "",
                    name: "",
                    email: "",
                    phone: "",
                    role: 1,
                    address: "",
                    password: "",
                    next_of_kin: '',
                    next_of_kin_address: '',
                    next_of_kin_phone: '',
                    image: '',
                    salary: '0.00',
                    invoice: '',
                    store: '',
                }),

                role: [
                    { text: 'Marketing', value: '2' },
                    { text: 'Admin', value: '3' },
                    { text: 'Accounts', value: '4' },
                ],

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
                };
        },

        methods: {
            print () {
                // Pass the element id here
                this.$htmlToPaper('printMe');
            },

            onChange(event) {
                this.filterForm.selected = event.target.value;
                this.loadUsers();
                this.getUser();
            },

            getUser() {
                axios.get("/api/user").then(({ data }) => {
                    this.userss = data.user;
                    this.stores = data.stores;
                });
            },

            setSelected(value) {
                this.form.store = value.id;
            },

            uploadImage(e){
                let file = e.target.files[0];
                let reader = new FileReader();
                if(file['size'] < 8388608){
                  reader.onloadend = (file) => {
                    this.form.image = reader.result;
                    console.log(this.form.image);
                  }
                  reader.readAsDataURL(file);
                }else{
                   Swal("Failed!", "Oops, You are uploading a large image, try again. Upload file less that 8MB", "Warning")
                }
            },

            getResults(page = 1) {
                axios.get("/api/admin?page=" + page, { params: this.filterForm })
                .then(response => {
                    this.users = response.data.staff;
                });
            },

            loadUsers() {
                if (this.is_busy) return;
                this.is_busy = true;

                axios.get("/api/admin", { params: this.filterForm })
                .then(({ data }) => {
                    if(this.filterForm.selected==0)
                    {
                        this.users.data = data.staff;
                    }
                    else{
                        this.users = data.staff;
                    }
                    this.count_all = data.all;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            editModal(user) {
                (this.editMode = true), this.form.reset();
                $("#addNewUser").modal("show");
                this.form.fill(user);
                this.show_password = 2;
                this.form.store = user.store;
                if(user.image){
                    this.form.image = '/img/photos/'+ user.image;
                }      
            },

            newModal() {
                (this.editMode = false), this.form.reset();
                $("#addNewUser").modal("show");
            },

            setPassword() {
                this.show_password = 2;
            },

            unsetPassword() {
                this.show_password = 1;
            },

            
            view(user) {
                console.log(user)
                this.$router.push({ path: "/admin/user/" + user.unique_id });
            },

            onFilterSubmit()
            {
                this.loadUsers();
                this.getUser();
            },

            createUser() {
                this.$Progress.start();
                this.form.post("/api/admin")
                .then(() => {
                    $("#addNewUser").modal("hide");

                    Swal.fire(
                        "Created!",
                        "Staff Created Successfully.",
                        "success"
                    );
                    
                    this.loadUsers();
                    this.getUser();
                })

                .catch();
            },

            updateUser() {
                this.form.put("/api/admin/" + this.form.id)

                .then(() => {
                    $("#addNewUser").modal("hide");
                    Swal.fire("Updated!", "Staff Updated Successfully.", "success");
                    this.show_password = 1;
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
                }).then(result => {
                    if (result.value) {
                        this.form.delete("/api/admin/" + id)
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Staff deleted.",
                                "success"
                            );
                            this.loadUsers();
                            this.getUser();
                        })

                        .catch(() => {
                            Swal.fire(
                                "Failed!",
                                "Ops, Something went wrong, try again.",
                                "warning"
                            );
                        });
                    }
                });
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
                        axios.get('/api/admin/delete', { params: this.action})
                        .then(() => {
                            Swal.fire(
                                "Deleted!",
                                "Staff deleted.",
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

            formatPrice(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            }
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
