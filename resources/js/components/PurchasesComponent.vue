<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid">
            <div class="row mb-2 p-2">
                <div class="col-md-4">
                    <h2><strong>List of Purchase</strong></h2>
                </div>

                <div class="col-md-8">
                    <router-link to="/admin/purchase/create" class="pull-right m-2">
                        <b-button variant="outline-primary" size="sm">
                            Add <i class="fa fa-plus"></i>
                        </b-button>
                    </router-link>

                    <b-button variant="outline-primary" size="sm" class="pull-right m-2" v-b-modal.filter-modal><i class="fa fa-filter"></i> Filter</b-button>

                    <b-modal id="filter-modal" ref="filter" title="Filter" hide-footer>
                        <b-form @submit.stop.prevent="onFilterSubmit">

                            <div class="row">
                                <div class="col-lg-6">
                                    <b-form-group label="Start Date:" label-for="Start Date">
                                        <b-form-datepicker v-model="filterForm.start_date" placeholder="Start date"
                                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                                        </b-form-datepicker>
                                    </b-form-group>
                                </div>

                                <div class="col-lg-6">
                                    <b-form-group label="End Date:" label-for="End Date">
                                        <b-form-datepicker v-model="filterForm.end_date" placeholder="End date"
                                            :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                                        </b-form-datepicker>
                                    </b-form-group>
                                </div>
                            </div>

                            <b-form-group label="Purchase Code:">
                                <b-form-input id="name" v-model="filterForm.name" type="text"></b-form-input>
                            </b-form-group>

                            <b-button type="submit" variant="primary">Filter</b-button>
                        </b-form>
                    </b-modal>                      
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0" v-if="purchases.data.length>0">
                    <table class="table table-hover">
                        <tr>
                           
                            <th>Purchase ID</th>
                            <th>Date</th>
                            <th>Modify</th>
                        </tr>

                        <tr v-for="purchase in purchases.data" :key="purchase.purchase_id">
                          
                            <td>{{ purchase.purchase_id }}</td>
                            <td>{{ purchase.purchase_date | myDate }}</td>
                            <td>
                                <router-link :to="'/admin/purchases/' + purchase.purchase_id">
                                    <i class="fa fa-eye text-blue"></i> View
                                </router-link>
                              
                                <!--<a href="javascript:void(0)" @click="deletePurchase(purchase.id)">
                                    <i class="fa fa-trash text-red"></i>
                                </a>-->
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="card-body" v-else>
                    <div class="alert alert-info text-center"><h3><strong>No Purchase Found.</strong></h3></div>
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
                           
                        </div>

                        <div class="col-md-8" v-if="this.filterForm.selected!=0">
                            <pagination :data="purchases" @pagination-change-page="getResults"></pagination>
                        </div>

                        <!--<div class="col-md-2">
                            <b-button variant="outline-danger" size="sm" v-if="action.selected.length" class="pull-right" @click="onDeleteAll"><i class="fa fa-trash"></i> Delete Selected</b-button>

                            <b-button disabled size="sm" variant="outline-danger" v-else class="pull-right"> <i class="fa fa-trash"></i> Delete Selected</b-button>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
    import moment from 'moment'
    export default {
        created() {
            this.loadPurchase();
        },

        data() {
            return {
                filterForm: {
                    start_date: '',
                    end_date: '',
                    name: '',
                    selected: '10',
                },

                is_busy: false,
                nairaSign: "&#x20A6;",
                editMode: false,
                model: {},
                purchases: {},
                action: {
                    selected: []
                },
                purchases: {
                    data: {},
                },
                count_all: '',
                unprintable: false,
            };
        },

        methods: {     
            getResults(page = 1) {
                axios.get("/api/purchases?page=" + page)
                .then(response => {
                    this.purchases = response.data.purchases;
                });
            },

            onChange(event) {
                this.filterForm.selected = event.target.value;
                this.loadUsers();
            },

            loadPurchase() {
                if (this.is_busy) return;
                this.is_busy = true;

                axios.get("/api/purchases", { params: this.filterForm })
                .then(({ data }) => {
                    if(this.filterForm.selected==0)
                    {
                        this.purchases.data = data.purchases;
                    }
                    else{
                        this.purchases = data.purchases;
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

            onFilterSubmit()
            {
                this.loadPurchase();
                this.$refs.filter.hide();
            },
        }
    };
</script>
