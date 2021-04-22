<template>
    <b-overlay :show="is_busy" rounded="sm">
        <div class="container-fluid">    
            <div class="row mb-2 p-2">
                <div class="col-md-8">
                    <h2><strong>Purchase Details ({{purchase_id}})</strong></h2>
                </div>
            </div>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <tr>
                            <th>Purchase ID</th>
                            <th>Products</th>
                            <th>Quantity</th>
                            <th>Price (<span v-html="nairaSign"></span>)</th>
                            <th>Supplied By</th>
                            <th>Moved to</th>
                            <th>Date</th>
                        </tr>

                        <tr v-for="purchase in groupPurchases" :key="purchase.id">
                            <td>{{ purchase.purchase_id }}</td>
                            <td>{{ purchase.product_name }}</td>
                            <td>{{ purchase.quantity }}</td>
                            <td>{{ formatPrice(purchase.total_price) }}</td>
                            <td>{{ purchase.supplier }}</td>
                            <td>{{ purchase.store_id }}</td>
                            <td>{{ purchase.purchase_date | myDate }}</td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <pagination
                        :data="purchases"
                        @pagination-change-page="getResults"
                    ></pagination>
                </div>
            </div>
        </div>
    </b-overlay>
</template>

<script>
export default {
    created() {
        this.viewPurchase();
        this.getProduct();
        this.getUser();
    },

    data() {
        return {
            is_busy: false,
            nairaSign: "&#x20A6;",
            editMode: false,
            model: {},
            purchases: {},
            groupPurchases: "",
          
            singleSupplier: "",
            singleProduct: "",
            user: "",
            purchase_id: "",

            products: {},
            errors: []
        };
    },

    methods: {
        getUser() {
            axios.get("/api/user")
            .then(({ data }) => {
                this.user = data.user;
            });
        },

        getResults(page = 1) {
            axios.get("/api/purchases?page=" + page)
            .then(response => {
                this.purchases = response.data;
            });
        },

        viewPurchase() {
            this.purchase_id = this.$route.params.id;
            axios.get("/api/purchases/" + this.$route.params.id)
            .then(({ data }) => {
                if(data.length==0){
                    Swal.fire(
                        "Failed!",
                        'This purchase transaction does not exist',
                        "warning"
                    );
                    this.$router.push({ path: '/admin/purchases'});
                }
               
                this.groupPurchases = data;
            });
        },

        getProduct() {
            axios.get("/api/inventory")
            .then(({ data }) => {
                this.products = data.data;
            });
        },

        formatPrice(value) {
            let val = (value/1).toFixed(2).replace(',', '.')
            return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
    }
};
</script>
