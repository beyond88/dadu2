import { apiSlice } from "../api/apiSlice";

const posInvoiceApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getPosInvoice: builder.query({
      query: (page) => `/admin/invoice-list?page=${page}`,
      providesTags: ["Invoice"],
    }),
    getSingleInvoice: builder.query({
      query: (id) => `/admin/invoice-details/${id}`,
      providesTags: ["Invoice"],
    }),
    createAdminInvoice: builder.mutation({
      query: (data) => ({
        url: `/admin/invoice-create`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    updateAdminInvoice: builder.mutation({
      query: ({ data, id }) => ({
        url: `/admin/invoice-update/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    invoiceMakePayment: builder.mutation({
      query: (data) => ({
        url: `/admin/invoices/make-payment/${data.id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    changeStatus: builder.mutation({
      query: ({ id, status }) => ({
        url: `/admin/invoices/delivered/${id}/${status}`,
        method: "POST",
      }),
      invalidatesTags: ["Invoice"],
    }),
    deleteInvoice: builder.mutation({
      query: (id) => ({
        url: `/admin/invoice-delete/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Invoice"],
    }),
    getInvoiceCustomerEmail: builder.query({
      query: (id) => `/admin/invoices/customer-email/${id}`,
    }),
    sendInvoice: builder.mutation({
      query: (data) => ({
        url: `/admin/invoices/payments/send`,
        method: "POST",
        body: data,
      }),
    }),
    getCustomerInvoice: builder.query({
      query: (page) => `/customer/invoice-list?page=${page}`,
      providesTags: ["Invoice"],
    }),
    getCustomerSingleInvoice: builder.query({
      query: (id) => `/customer/invoice-details/${id}`,
      providesTags: ["Invoice"],
    }),
    createCustomerInvoice: builder.mutation({
      query: (data) => ({
        url: `/customer/invoice-create`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    createCustomerDraftInvoice: builder.mutation({
      query: (data) => ({
        url: `/customer/draft-invoice-create`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    getDraftInvoice: builder.query({
      query: (page) => `/customer/draft-invoice-list?page=${page}`,
      providesTags: ["Invoice"],
    }),
    getSingleDraftInvoice: builder.query({
      query: (id) => `/customer/draft-invoice-details/${id}`,
      providesTags: ["Invoice"],
    }),
    deleteDraftInvoice: builder.mutation({
      query: (id) => ({
        url: `/customer/delete-draft-invoice/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Invoice"],
    }),
    draftInvoiceUpdate: builder.mutation({
      query: ({ data, id }) => ({
        url: `/customer/draft-invoice-update/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    draftInvoiceToStore: builder.mutation({
      query: ({ data, id }) => ({
        url: `/customer/invoices/store/from-draft/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Invoice"],
    }),
    adminInvoiceViewPayment: builder.query({
      query: (id) => `/admin/invoices/payments/${id}`,
    }),
  }),
});

export const {
  useGetPosInvoiceQuery,
  useGetSingleInvoiceQuery,
  useGetCustomerInvoiceQuery,
  useGetCustomerSingleInvoiceQuery,
  useCreateCustomerInvoiceMutation,
  useCreateCustomerDraftInvoiceMutation,
  useGetDraftInvoiceQuery,
  useGetSingleDraftInvoiceQuery,
  useDeleteDraftInvoiceMutation,
  useDraftInvoiceUpdateMutation,
  useDraftInvoiceToStoreMutation,
  useCreateAdminInvoiceMutation,
  useAdminInvoiceViewPaymentQuery,
  useInvoiceMakePaymentMutation,
  useChangeStatusMutation,
  useGetInvoiceCustomerEmailQuery,
  useSendInvoiceMutation,
  useDeleteInvoiceMutation,
  useUpdateAdminInvoiceMutation,
} = posInvoiceApi;
