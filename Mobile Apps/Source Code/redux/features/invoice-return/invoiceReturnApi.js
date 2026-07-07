import { apiSlice } from "../api/apiSlice";

const invoiceReturnApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getReturnInvoiceList: builder.query({
      query: () => `/customer/returnable-invoice-list`,
    }),
    getReturnInvoiceRequest: builder.query({
      query: () => `/customer/invoice-return-requests`,
    }),
    getReturnRequestDetails: builder.query({
      query: (id) => `/customer/invoice-return-request/show/${id}`,
    }),
    productReturnRequest: builder.mutation({
      query: (data) => ({
        url: `/customer/products-return-request`,
        method: "POST",
        body: data,
      }),
    }),
  }),
});

export const {
  useGetReturnInvoiceListQuery,
  useGetReturnInvoiceRequestQuery,
  useGetReturnRequestDetailsQuery,
  useProductReturnRequestMutation,
} = invoiceReturnApi;
