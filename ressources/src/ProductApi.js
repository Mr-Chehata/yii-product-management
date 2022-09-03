import axios from "axios";

export default class ProductApi {
   baseUrl =  `http://127.0.0.1/product-management/index.php`;
  async getProductsList(currentPage = null, pageSize = null) {
    return await axios.get(
      baseUrl + `&page=${currentPage}&pageSize=${pageSize}`
    );
  }

  async updateProduct(id, name, details) {
    return await axios.put(
      baseUrl + `?r=product/update?id=${id}`,
      {
        name: name,
        details: details,
      }
    );
  }

  async removeProduct(id) {
    return await axios.delete( baseUrl + `?r=product/delete`);
  }

  async createProduct(name, details) {
    return await axios.get(
      baseUrl +`?r=product/create`,
      {
        name: this.product.name,
        details: this.product.details,
      }
    );
  }
}
