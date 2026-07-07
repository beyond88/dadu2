import { apiSlice } from "../api/apiSlice";

const productApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getProducts: builder.query({
      query: (page) => `/admin/product-list?page=${page}`,
      providesTags: ["Product"],
    }),
    getCustomerProducts: builder.query({
      query: (selectedWarehouse) => {
        let queryString = "";
        if (selectedWarehouse) {
          queryString = `warehouse=${selectedWarehouse}`;
        }
        return {
          url: `/customer/stock-wise-products?${queryString}`,
          method: "GET",
        };
      },
    }),
    getAdminProducts: builder.query({
      query: (selectedWarehouse) => {
        let queryString = "";
        if (selectedWarehouse) {
          queryString = `warehouse=${selectedWarehouse}`;
        }
        return {
          url: `/admin/stock-wise-products?${queryString}`,
          method: "GET",
        };
      },
    }),
    getSingleAdminProduct: builder.query({
      query: (id) => `/admin/products/${id}`,
      providesTags: ["Product"],
    }),
    adminProductStockShow: builder.query({
      query: (id) => `/admin/products/stock-update/${id}`,
      providesTags: ["Product"],
    }),
    adminProductStockUpdate: builder.mutation({
      query: (data) => ({
        url: `/admin/product-stocks-update-by-stock/${data.id}`,
        method: "PUT",
        body: data,
      }),
      invalidatesTags: ["Product"],
    }),
    adminProductsStockPriceShow: builder.query({
      query: (id) => `/admin/product-stocks/${id}`,
      providesTags: ["Product"],
    }),
    adminProductsStockPriceUpdate: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/product-stocks/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Product"],
    }),
    adminGetProductCreateInfo: builder.query({
      query: () => `/admin/products/create`,
    }),
    adminProductCreate: builder.mutation({
      query: (data) => ({
        url: `/admin/products/store`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Product"],
    }),
    adminProductUpdate: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/products/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Product"],
    }),
  }),
});

export const {
  useGetProductsQuery,
  useGetCustomerProductsQuery,
  useGetAdminProductsQuery,
  useGetSingleAdminProductQuery,
  useAdminProductStockShowQuery,
  useAdminProductStockUpdateMutation,
  useAdminProductsStockPriceShowQuery,
  useAdminProductsStockPriceUpdateMutation,
  useAdminGetProductCreateInfoQuery,
  useAdminProductCreateMutation,
  useAdminProductUpdateMutation,
} = productApi;
