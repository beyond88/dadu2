import { apiSlice } from "../api/apiSlice";

export const customerApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getCustomerList: builder.query({
      query: (page) => `/admin/customers?page=${page}`,
      providesTags: ["Customer"],
    }),
    verifyUnverifyCustomer: builder.mutation({
      query: (id) => ({
        url: `/admin/customers/verify/${id}`,
        method: "POST",
      }),
      invalidatesTags: ["Customer"],
    }),
    getSingleCustomer: builder.query({
      query: (id) => `/admin/customers/${id}`,
      providesTags: ["Customer"],
    }),
    createCustomer: builder.mutation({
      query: (data) => ({
        url: `/admin/customers`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Customer"],
    }),
    updateCustomer: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/customers/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Customer"],
    }),
    deleteCustomer: builder.mutation({
      query: (id) => ({
        url: `/admin/customers/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Customer"],
    }),
  }),
});

export const {
  useGetCustomerListQuery,
  useVerifyUnverifyCustomerMutation,
  useGetSingleCustomerQuery,
  useCreateCustomerMutation,
  useUpdateCustomerMutation,
  useDeleteCustomerMutation,
} = customerApi;
