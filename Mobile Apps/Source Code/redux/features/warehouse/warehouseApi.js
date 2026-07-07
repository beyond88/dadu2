import { apiSlice } from "../api/apiSlice";

const warehouseApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    createAdminWarehouse: builder.mutation({
      query: (body) => ({
        url: `/admin/warehouses`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["Warehouse"],
    }),
    getAdminWarehouse: builder.query({
      query: (page) => `/admin/warehouses?page=${page}`,
      providesTags: ["Warehouse"],
    }),
    getAdminSingleWarehouse: builder.query({
      query: (id) => `/admin/warehouses/${id}`,
      providesTags: ["Warehouse"],
    }),
    deleteAdminWarehouse: builder.mutation({
      query: (id) => ({
        url: `/admin/warehouses/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Warehouse"],
    }),
    updateAdminWarehouse: builder.mutation({
      query: (body) => ({
        url: `/admin/warehouses/${body.id}`,
        method: "PUT",
        body,
      }),
      invalidatesTags: ["Warehouse"],
    }),
  }),
});

export const {
  useGetAdminWarehouseQuery,
  useGetAdminSingleWarehouseQuery,
  useDeleteAdminWarehouseMutation,
  useCreateAdminWarehouseMutation,
  useUpdateAdminWarehouseMutation,
} = warehouseApi;
