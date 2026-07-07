import { apiSlice } from "../api/apiSlice";

const homeApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getAdminHome: builder.query({
      query: () => "/admin/home-page",
    }),
    topProducts: builder.query({
      query: () => "/admin/top-product",
    }),
    salesChart: builder.query({
      query: ({ fromDate, toDate }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString = `?from_date=${fromDate}&to_date=${toDate}`;
        }
        return {
          url: `/admin/sale-chart-data${queryString}`,
          method: "GET",
        };
      },
    }),
    getCustomerHome: builder.query({
      query: () => "/customer/home-page",
    }),
    CustomerTopProducts: builder.query({
      query: () => "/customer/top-product",
    }),
  }),
});

export const {
  useGetAdminHomeQuery,
  useTopProductsQuery,
  useSalesChartQuery,
  useGetCustomerHomeQuery,
  useCustomerTopProductsQuery,
} = homeApi;
