<template>
  <div class="container box">
    <b-row>
      <b-col class="col-5">
        <h1>Product Management</h1>
      </b-col>
      <b-col class="col-1 mt-2">
        <div v-if="listLoader" class="loading ml-2">
          <div class="spinner-border" role="status"></div>
        </div>
      </b-col>
      <b-col class="col-3 mt-2 pl-0">
        <div class="input-group">
          <input
            type="search"
            class="form-control rounded"
            placeholder="Search"
            aria-label="Search"
            aria-describedby="search-addon"
          />
          <button type="button" class="btn btn-search ms-3">Search</button>
        </div>
      </b-col>
      <b-col class="col-3 mt-2 pr-0 float-end">
        <b-button variant="success" @click="openModal('create-modal')">
          Create New
        </b-button>
      </b-col>
    </b-row>

    <table class="table table-bordered table-width">
      <thead>
        <tr>
          <th width="5%">No</th>
          <th width="15%">Name</th>
          <th width="60%">Details</th>
          <th width="20%">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(product, index) in productsList" v-bind:key="index">
          <td>{{ product.id }}</td>
          <td>{{ product.name }}</td>
          <td>{{ product.details }}</td>
          <td>
            <b-button
              class="me-1"
              variant="primary"
              @click.prevent.stop="fillProduct(product)"
              >Edit
            </b-button>
            <b-button
              class="me-1"
              variant="danger"
              @click.prevent.stop="deleteProduct(product)"
              >Delete
            </b-button>
          </td>
        </tr>
      </tbody>
    </table>
    <b-row>
      <b-col class="col-7"></b-col>
      <b-col class="col-5">
        <div class="pagination-position mt-3">
          <b-pagination
            v-model="this.currentPage"
            :per-page="this.pageSize"
            first-number
            last-number
            @change="pagination"
          ></b-pagination>
        </div>
      </b-col>
    </b-row>
    <!-- creatModal -->

    <b-modal
      id="delete-modal"
      ref="delete-modal"
      class="mo mo-overlay"
      title="Delete Product"
      :hide-footer="true"
    >
      <form @submit.prevent="removeProduct" class="form-modal-custom-style">
        <p>Are you sure?</p>
        <div class="d-flex justify-content-center mt-2">
          <b-button type="submit">
            <span>
              Delete
              <div v-if="loader" class="loading ml-2">
                <div class="spinner-border" role="status"></div>
              </div>
            </span>
          </b-button>
        </div>
      </form>
    </b-modal>
    <b-modal
      id="create-modal"
      ref="create-modal"
      class="mo mo-overlay"
      title="New product"
      :hide-footer="true"
    >
      <form @submit.prevent="createProduct" class="form-modal-custom-style">
        <b-form-group label="Name" label-for="name" class="input-modal-champ">
          <b-form-input
            id="Name"
            v-model="product.name"
            required
          ></b-form-input>
        </b-form-group>
        <b-form-group label="Details" label-for="details">
          <b-form-textarea
            id="details"
            v-model="product.details"
            required
          ></b-form-textarea>
        </b-form-group>
        <div class="d-flex justify-content-center mt-2">
          <b-button type="submit">
            <span>
              Save
              <div v-if="loader" class="loading ml-2">
                <div class="spinner-border" role="status"></div>
              </div>
            </span>
          </b-button>
        </div>
      </form>
    </b-modal>
    <b-modal
      id="Edit-modal"
      ref="Edit-modal"
      class="mo mo-overlay"
      title="Edit Product"
      :hide-footer="true"
    >
      <form @submit.prevent="updateProduct" class="form-modal-custom-style">
        <b-form-group label="Name" label-for="name" class="input-modal-champ">
          <b-form-input
            id="Name"
            v-model="product.name"
            required
          ></b-form-input>
        </b-form-group>
        <b-form-group label="Details" label-for="details">
          <b-form-textarea
            id="details"
            v-model="product.details"
            required
          ></b-form-textarea>
        </b-form-group>
        <div class="d-flex justify-content-center mt-2">
          <b-button type="submit">
            <span>
              Submit
              <div v-if="loader" class="loading ml-2">
                <div class="spinner-border" role="status"></div>
              </div>
            </span>
          </b-button>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import ProductApi from "./ProductApi";
export default {
  name: "App",
  components: {},
  data() {
    return {
      loader: false,
      listLoader: false,
      pageSize: 10,
      currentPage: 1,
      product: {
        id: null,
        name: null,
        details: null,
      },
      productsList: [],
      errors: [],
    };
  },
  methods: {
    async getProductsList() {
      this.listLoader = true;
      const response = ProductApi.getProductsList();
      this.listLoader = false;
      this.productList = response.data.list;
      this.rows = response.data.total ?? 0;
      return true;
    },
    async createProduct() {
      this.loader = true;
      const response = ProductApi.createProduct(
        this.product.name,
        this.product.details
      );
      if (response.status === 201) {
        this.loader = false;
        this.$refs["create-modal"].hide();
        this.getProductsList();
      } else {
        this.errors = response.data.erros;
        this.loader = false;
      }
    },
    fillProduct(productValues) {
      this.product = {
        name: productValues.name,
        details: productValues.details,
        id: productValues.id,
      };
      this.$refs["Edit-modal"].show();
    },
    deleteProduct(product) {
      this.$refs["delete-modal"].show();
      this.product = product;
    },

    async updateProduct() {
      const response = ProductApi.updateProduct(
        this.product.name,
        this.product.details
      );
      if (response.status === 200) {
        this.$refs["Edit-modal"].hide();
        this.getProductsList();
      }else{
         this.errors = response.data.erros;
      }
    },
    async removeProduct() {
      const response = ProductApi.removeProduct(this.product.id);
      if (response.status === 204) {
        this.getProductsList();
        this.loader = false;
      } else {
        this.loader = false;
        this.errors = response.data.erros;
      }

      this.$refs["delete-modal"].hide();
    },

    openModal(modal) {
      this.$refs[modal].show();
    },
    pagination(paginate) {
      this.currentPage = paginate;
      this.getProductsList();
    },
  },
  async mounted() {
    this.getProductsList();
  },
};
</script>

<style lang="css" scoped>
.btn-search {
  background-color: #e6e6e6;
}

.btn-search:hover {
  background-color: #cbcbcb;
  border: none;
}
</style>

