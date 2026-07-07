import { apiSlice } from "../api/apiSlice";

export const couponApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getCouponProducts: builder.query({
      query: (page) => {
        let queryString = "";
        if (page) {
          queryString = `page=${page}`;
        }
        return {
          url: `/admin/coupon-products?${queryString}`,
          method: "GET",
        };
      },
    }),
  }),
});
