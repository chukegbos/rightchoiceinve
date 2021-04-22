<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid">
            
            <div class="mb-2 p-2">
                <h2>
                    <strong v-show="!editMode">New Customer</strong>
                    <strong v-show="editMode">Update Customer</strong>
                </h2>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="editMode ? updateUser() : createUser()">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Customer Name</label>
                                <input v-model="form.name" type="text" class="form-control"/>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Contact Person</label>
                                <input v-model="form.c_person" type="text" class="form-control"/>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Email Address</label>
                                <input v-model="form.email" type="email" class="form-control"/>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>Phone Number</label>
                                <input v-model="form.phone" type="tel" class="form-control">
                            </div>

                            <div class="col-md-12 form-group">
                                <label>Address</label>
                                <textarea v-model="form.address" class="form-control"></textarea>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>City</label>
                                <input v-model="form.city" type="text" class="form-control"/>
                            </div>

                            <div class="col-md-6 form-group">
                                <label>State</label>
                                <input v-model="form.state" type="text" class="form-control"/>
                            </div>
                        </div>
                        <button v-show="editMode" type="submit" class="btn btn-success">
                            Update
                        </button>
                        <button v-show="!editMode" type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </form>  
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
            this.loadPage();
        },

        data(){
            return{
                form: new Form({
                    id: "",
                    name: "",
                    email: "",
                    phone: "",
                    address: "",
                    state: "",
                    credit_unit: "",
                    city: "",
                    c_person: "",
                }),
                is_busy: false,
                editMode: false,
                is_busy: false,
            }
        },

        methods: {
            loadPage(){
                let id = this.$route.params.id;
                if(id){
                    this.editMode = true;
                    axios.get('/api/customer/edit/' + id)
                    .then((response) => {
                        if(response.data.error)
                        {
                            Swal.fire(
                                "Deleted!",
                                response.data.error,
                                "error"
                            );
                            this.$router.push({ path: "/admin/customers"});
                        }
                        else{
                            this.form = response.data;
                            console.log(response.data)
                        }
                        
                        console.log(response)
                    })

                    .catch((err) => {
                        console.log(err);
                    });
                }
            },

            createUser() {
                if(this.is_busy) return;
                this.is_busy = true;

                this.form.post("/api/customer")
                .then(() => {
                    this.is_busy = false;
                    Swal.fire(
                        "Created!",
                        "Customer Created Successfully.",
                        "success"
                    );
                    
                    this.$router.push({ path: "/admin/customers" });
                })
                .catch(() => {
                    this.is_busy = false;
                    Swal(
                        "Failed!",
                        "Ops, Something went wrong, try again.",
                        "warning"
                    );

                });
            },

            updateUser() {
                if(this.is_busy) return;
                this.is_busy = true;

                axios.put("/api/customer/" + this.form.id, this.form)
                .then(() => {
                    this.is_busy = false;
                    Swal.fire("Updated!", "Customer Updated Successfully.", "success");
                    this.$router.push({ path: "/admin/customers" });
                })

                .catch();
                this.is_busy = false;
            },
        },
     }  
</script>


