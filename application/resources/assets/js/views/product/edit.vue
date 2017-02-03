<template>

  <article>
    EDIT THIS PRODUCT

    <h1>
      <a v-link="{ name: 'product' }">&laquo;</a>
      {{ product.name }}
    </h1>

    <h4>{{ product.id }}</h4>

    <div>
      <label>
        Name
        <input name="name"
               type="text"
               id="lk-product-name"
               placeholder="Name"
               v-model="product.name">
      </label>

      <label>
        SKU
        <input name="sku"
               type="text"
               id="lk-product-sku"
               placeholder="SKU"
               v-model="product.sku">
      </label>

      <label>
        Description
        <textarea name="lk-product-description"
                  id="lk-product-description"
                  rows="3">{{ product.descriptionArea }}</textarea>
      </label>

      <label>
        Price
        <input name="price"
               type="number"
               id="lk-product-price"
               placeholder="Price"
               v-model="product.unitPrice">
      </label>

      <label>
        Quantity
        <input name="quantity"
               type="number"
               id="lk-product-quantity"
               placeholder="Quantity"
               v-model="product.quantity">
      </label>

      <fieldset>
        <legend>Inventory Settings</legend>
        <div>
          <input name="inventory-required"
                 type="checkbox"
                 id="lk-product-inventory-required"
                 v-model="product.isInventoryRequired"
          >
          <label for="lk-product-inventory-required">Inventory Required?</label>
        </div>
        <div>
          <input name="price-visible"
                 type="checkbox"
                 id="lk-product-price-visible"
                 v-model="product.isPriceVisible"
          >
          <label for="lk-product-price-visible">Price Visible?</label>
        </div>
        <div>
          <input name="active"
                 type="checkbox"
                 id="lk-product-active"
                 v-model="product.isActive"
          >
          <label for="lk-product-active">Active?</label>
        </div>
        <div>
          <input name="visible"
                 type="checkbox"
                 id="lk-product-visible"
                 v-model="product.isVisible"
          >
          <label for="lk-product-visible">Visible?</label>
        </div>
        <div>
          <input name="taxable"
                 type="checkbox"
                 id="lk-product-taxable"
                 v-model="product.isTaxable"
          >
          <label for="lk-product-taxable">Taxable?</label>
        </div>
        <div>
          <input name="shippable"
                 type="checkbox"
                 id="lk-product-shippable"
                 v-model="product.isShippable"
          >
          <label for="lk-product-shippable">Shippable?</label>
        </div>

        <div>
          <input name="attachments-enabled"
                 type="checkbox"
                 id="lk-product-attachments-enabled"
                 v-model="product.areAttachmentsEnabled"
          >
          <label for="lk-product-attachments-enabled">Attachments Enabled?</label>
        </div>
      </fieldset>

      <label>
        Shipping Weight
        <input name="shipping-weight"
               type="number"
               placeholder="Shipping Weight">
      </label>

      <label>
        Rating
        <input name="rating"
               type="number"
               placeholder="Rating">
      </label>

      <div>
        Default Image: {{ product.defaultImage }}
        <label for="lk-product-default-image" class="button">Upload File</label>
        <input name="default-image"
               type="file"
               id="lk-product-default-image"
               class="show-for-sr">
      </div>

      <label>
        Rating
        <input name="rating"
               type="number"
               id="lk-product-rating"
               placeholder="Rating"
               v-model="product.rating">
      </label>

      <label>
        Shipping Weight
        <input name="shipping-weight"
               type="number"
               id="lk-product-shipping-weight"
               placeholder="Shipping Weight"
               v-model="product.shippingWeight">
      </label>

      <button class="button" @click="submit">Save</button>
    </div>

  </article>

</template>

<script>

  export default {

    computed: {},

    ready() {
      this.resource = this.$resource('/api/products{/id}{/edit}');
      this.fetch();
    },

    data() {
      return {
        resource: null,
        product: {
          id: this.$route.params.id
        }
      }
    },

    methods: {

      fetch() {
        this.resource.get({id: this.product.id}).then((response) => {
          this.product = response.data[0];
        });
      },

      submit() {

        var data = {
          id: this.product.id,
          sku: this.product.sku,
          name: this.product.name,
          quantity: this.product.quantity,
          unitPrice: this.product.unitPrice,
          isInventoryRequired: this.product.isInventoryRequired,
          isPriceVisible: this.product.isPriceVisible,
          isActive: this.product.isActive,
          isVisible: this.product.isVisible,
          isTaxable: this.product.isTaxable,
          isShippable: this.product.isShippable,
          areAttachmentsEnabled: this.product.areAttachmentsEnabled,
        };

        console.log(data);


        this.resource.update({id: this.product.id}, data).then((response) => {
          this.$router.go({ name: 'product.show', params: {id: this.product.id} });
        }, (response) => {
          console.log('fail', response);
        });
      }


    }
  }
</script>
