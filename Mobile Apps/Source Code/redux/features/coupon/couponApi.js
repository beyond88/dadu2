import { apiSlice } from "../api/apiSlice";

export const couponApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getCouponList: builder.query({
      query: (page) => `/admin/coupons?page=${page}`,
      providesTags: ["Coupon"],
    }),
    deleteCoupon: builder.mutation({
      query: (id) => ({
        url: `/admin/coupons/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Coupon"],
    }),
    getCouponProducts: builder.query({
      query: ({ slug, page }) => {
        let query = "";
        if (page) {
          query = `?page=${page}`;
        }
        return {
          url: `/admin/coupon-products/${slug}${query}`,
        };
      },
      providesTags: ["Coupon"],
    }),
    deleteCouponProduct: builder.mutation({
      query: (id) => ({
        url: `/admin/coupon-products/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Coupon"],
    }),
    addedCouponProduct: builder.mutation({
      query: (data) => ({
        url: `/admin/coupon-products`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Coupon"],
    }),
    addedCoupon: builder.mutation({
      query: (data) => ({
        url: `/admin/coupons`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Coupon"],
    }),
    showCoupon: builder.query({
      query: (id) => `/admin/coupons/${id}`,
      providesTags: ["Coupon"],
    }),
    updateCoupon: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/coupons/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Coupon"],
    }),
  }),
});

export const {
  useGetCouponListQuery,
  useDeleteCouponMutation,
  useGetCouponProductsQuery,
  useDeleteCouponProductMutation,
  useAddedCouponProductMutation,
  useAddedCouponMutation,
  useShowCouponQuery,
  useUpdateCouponMutation,
} = couponApi;
