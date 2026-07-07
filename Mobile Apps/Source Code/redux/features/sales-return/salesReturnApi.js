import { apiSlice } from "../api/apiSlice";

const saleApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getSaleReturnCreateList: builder.query({
      query: (page) => `/admin/sale-return-creatable-list?page=${page}`,
      providesTags: ["SalesReturnRequest"],
    }),
    getSaleReturn: builder.query({
      query: (page) => `/admin/sale-return-list?page=${page}`,
      providesTags: ["SalesReturnRequest"],
    }),
    getSalesReturnRequest: builder.query({
      query: (page) => `/admin/sale-return-requests?page=${page}`,
      providesTags: ["SalesReturnRequest"],
    }),
    getCreateReturnDetails: builder.query({
      query: (id) => `/admin/sales-return/${id}/create`,
      providesTags: ["SalesReturnRequest"],
    }),
    createSalesReturn: builder.mutation({
      query: (body) => ({
        url: `/admin/sale-return-store`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["SalesReturnRequest"],
    }),
    salesReturnListDetails: builder.query({
      query: (id) => `/admin/sale-return-show/${id}`,
      providesTags: ["SalesReturnRequest"],
    }),
    salesReturnRequestListDetails: builder.query({
      query: (id) => `/admin/sale-return-request/${id}`,
      providesTags: ["SalesReturnRequest"],
    }),
    salesReturnRequestAccept: builder.mutation({
      query: (id) => ({
        url: `/admin/sale-return-request/accept/${id}`,
        method: "POST",
      }),
      invalidatesTags: ["SalesReturnRequest"],
    }),
    salesReturnRequestReject: builder.mutation({
      query: (id) => ({
        url: `/admin/sale-return-request/reject/${id}`,
        method: "POST",
      }),
      invalidatesTags: ["SalesReturnRequest"],
    }),
  }),
});

export const {
  useGetSaleReturnCreateListQuery,
  useGetSaleReturnQuery,
  useGetSalesReturnRequestQuery,
  useGetCreateReturnDetailsQuery,
  useCreateSalesReturnMutation,
  useSalesReturnListDetailsQuery,
  useSalesReturnRequestListDetailsQuery,
  useSalesReturnRequestAcceptMutation,
  useSalesReturnRequestRejectMutation,
} = saleApi;
