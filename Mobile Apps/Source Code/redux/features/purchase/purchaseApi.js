import { apiSlice } from "../api/apiSlice";

const purchaseApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getPurchase: builder.query({
      query: (page) => `/admin/purchases?page=${page}`,
      providesTags: ["Purchase"],
    }),
    purchaseDetails: builder.query({
      query: (id) => `/admin/purchases/${id}`,
      providesTags: ["Purchase"],
    }),
    deletePurchase: builder.mutation({
      query: (id) => ({
        url: `/admin/purchases/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Purchase"],
    }),
    cancelPurchase: builder.mutation({
      query: (data) => {
        const { id, date, note } = data;
        return {
          url: `/admin/purchases/${id}/cancel`,
          method: "POST",
          body: { date, note },
        };
      },
      invalidatesTags: ["Purchase"],
    }),
    purchaseReceived: builder.mutation({
      query: ({ id, body }) => {
        return {
          url: `/admin/purchases/${id}/receive`,
          method: "POST",
          body: body,
        };
      },
      invalidatesTags: ["Purchase"],
    }),
    purchaseReturn: builder.mutation({
      query: ({ id, body }) => {
        return {
          url: `/admin/purchases/${id}/return`,
          method: "POST",
          body: body,
        };
      },
      invalidatesTags: ["Purchase"],
    }),
    getPurchaseReceiveList: builder.query({
      query: (page) => `/admin/purchases/receive/list?page=${page}`,
      providesTags: ["Purchase"],
    }),
    getPurchaseReturnList: builder.query({
      query: (page) => `/admin/purchases/return/list?page=${page}`,
      providesTags: ["Purchase"],
    }),
    purchaseReceivedDelete: builder.mutation({
      query: (id) => ({
        url: `/admin/purchases/receive/delete/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Purchase"],
    }),
    purchaseReturnDelete: builder.mutation({
      query: (id) => ({
        url: `/admin/purchases/return/delete/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Purchase"],
    }),
    getSearchProduct: builder.query({
      query: ({ query }) => {
        return {
          url: `/admin/product-stock/search/name-sku/${query}`,
        };
      },
    }),
    purchaseCreate: builder.mutation({
      query: (data) => ({
        url: `/admin/purchases`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Purchase"],
    }),
    purchaseUpdate: builder.mutation({
      query: ({ id, data }) => {
        return {
          url: `/admin/purchases/${id}`,
          method: "POST",
          body: data,
        };
      },
      invalidatesTags: ["Purchase"],
    }),
    purchaseReceivedDetails: builder.query({
      query: (id) => `/admin/purchases/receive/show/${id}`,
    }),
    purchaseReturnDetails: builder.query({
      query: (id) => `/admin/purchases/return/show/${id}`,
    }),
  }),
});

export const {
  useGetPurchaseQuery,
  usePurchaseDetailsQuery,
  useDeletePurchaseMutation,
  useCancelPurchaseMutation,
  usePurchaseReceivedMutation,
  usePurchaseReturnMutation,
  useGetPurchaseReceiveListQuery,
  useGetPurchaseReturnListQuery,
  usePurchaseReceivedDeleteMutation,
  usePurchaseReturnDeleteMutation,
  useGetSearchProductQuery,
  usePurchaseCreateMutation,
  usePurchaseReceivedDetailsQuery,
  usePurchaseReturnDetailsQuery,
  usePurchaseUpdateMutation,
} = purchaseApi;
