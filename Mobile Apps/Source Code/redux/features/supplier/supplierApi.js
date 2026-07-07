import { apiSlice } from "../api/apiSlice";

export const supplierApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getSuppliers: builder.query({
      query: (page) => `/admin/suppliers?page=${page}`,
      providesTags: ["Supplier"],
    }),
    deleteSupplier: builder.mutation({
      query: (id) => ({
        url: `/admin/suppliers/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Supplier"],
    }),
    getSingleSupplier: builder.query({
      query: (id) => `/admin/suppliers/${id}`,
      providesTags: ["Supplier"],
    }),
    createSupplier: builder.mutation({
      query: (data) => ({
        url: `/admin/suppliers`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Supplier"],
    }),
    updateSupplier: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/suppliers/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Supplier"],
    }),
  }),
});

export const {
  useGetSuppliersQuery,
  useDeleteSupplierMutation,
  useGetSingleSupplierQuery,
  useCreateSupplierMutation,
  useUpdateSupplierMutation,
} = supplierApi;
