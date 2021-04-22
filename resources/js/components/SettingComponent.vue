<template>
    <b-overlay :show="is_busy" rounded="sm">  
        <div class="container-fluid">
            <div class="row mb-2 p-2">
                <div class="col-md-4">
                    <h2><strong>Site Settings</strong></h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form @submit.prevent="updateSite()">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sitename</label>
                                    <input v-model="form.sitename" type="text" name="sitename" class="form-control" required/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input v-model="form.email" type="email" name="email" class="form-control"/>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input v-model="form.phone" type="tel" class="form-control"/>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input v-model="form.address" type="text" class="form-control"/>
                                </div>
                            </div>
                        </div>  

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    export default {
        data() {
            return {
                is_busy: false,
                form: new Form({
                    sitename: '',
                    email: '',
                    phone: '',
                    address: '',
                    admin_percent: '',
                    manager_percent: '',
                    cashier_percent: '',
                    naira_value: '',
                    expense_ratio: '',
                    percent_gain: '',
                }),
                view_error: '',
            };
        },

        created() {
            this.loadSite();
        },

        methods: {
            loadSite() {
                if(this.is_busy) return;
                this.is_busy = true;

                axios.get("/api/setting")
                .then(({ data }) => {
                    this.form = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            updateSite() {
                if(this.is_busy) return;
                this.is_busy = true;

                axios.put("/api/setting/" + this.form.id, this.form)
                .then(() => {
                    Swal.fire("Updated!", "Site Updated Successfully.", "success");
                    this.loadSite();
                })
                .catch((data) => {
                    this.view_error = data;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },
        },

        
    };
</script>
