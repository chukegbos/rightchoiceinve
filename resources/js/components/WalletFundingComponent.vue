<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <b-button variant="outline-primary" size="sm" v-b-modal.filter-modal><i class="fa fa-filter"></i> Filter</b-button>
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

                            <b-form-group label="Customer:">
                                <vue-typeahead-bootstrap
                                  v-model="userQuery"
                                  :ieCloseFix="false"
                                  :data="users"
                                  :serializer="data => data.name"
                                  @hit="getUserID($event)"
                                  placeholder="Search for customer"
                                  @input="lookUser"
                                />
                            </b-form-group>
                            <b-button type="submit" variant="primary">Filter</b-button>
                        </b-form>
                    </b-modal>
                </div>
            </div>
        
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Funding History</span>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" v-if="fundings.data.length>0">
                    <table class="table table-hover">
                        <tr>
                            <th>Customer</th>
                            <th>Account</th>
                            <th>Payment Type</th>
                            <th>Date</th>
                        </tr>

                        <tr v-for="fund in fundings.data" :key="fund.id">
                            <td>{{ fund.customer_name }}</td>
                            <td>{{ fund.amount }}</td>
                            <td>{{ fund.mop }}</td>       
                            <td>{{ fund.created_at | myDate }}</td>
                        </tr>
                    </table>
                </div>
                <div v-else class="text-center alert alert-danger p-3">
                    Nothing found
                </div>
                <div class="card-footer" v-if="fundings.data.length>0">
                    <pagination
                        :data="fundings"
                        @pagination-change-page="getResults"
                    ></pagination>
                </div>
            </div>
        </div>

    </b-overlay>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    import {debounce} from 'lodash';

    export default {
        created() {
            this.loadAccount();
        },

        data() {
            return {
                filterForm: {
                    end_date: '',
                    start_date: '',
                    customer_id: '',
                },

                fundings: [],
                fundings: {
                    data: '',
                },
                is_busy: false,
                userQuery: '',
                users: [],
            };
        },

        methods: {
            getResults(page = 1) {
                axios.get("/api/funding?page=" + page)
                .then(response => {
                    this.fundings = response.data.fundings;
                });
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

            getUserID(data){
                this.filterForm.customer_id = data.id;
            },

            loadAccount() {
                if (this.is_busy) return;
                this.is_busy = true;

                axios.get("/api/funding", { params: this.filterForm })
                .then(({ data }) => {
                    this.fundings = data.fundings;
                })
                .finally(() => {
                    this.is_busy = false;
                });
            },

            onFilterSubmit()
            {
                this.loadAccount();
                this.$refs.filter.hide();
            },
        }
    };
</script>
