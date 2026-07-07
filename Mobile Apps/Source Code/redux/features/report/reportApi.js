import { apiSlice } from "../api/apiSlice";

const reportApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getExpenseReport: builder.query({
      query: ({ fromDate, toDate, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString = `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (query) {
          queryString = `q=${query}`;
        }
        return {
          url: `admin/expense-report?${queryString}`,
          method: "GET",
        };
      },
    }),
    getSalesReport: builder.query({
      query: ({ fromDate, toDate, warehouse, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString += `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (warehouse) {
          queryString += `&warehouse=${warehouse}`;
        }
        if (query) {
          queryString += `q=${query}`;
        }
        return {
          url: `/admin/sale-report?${queryString}`,
          method: "GET",
        };
      },
    }),
    getPurchaseReport: builder.query({
      query: ({ fromDate, toDate, warehouse, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString += `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (warehouse) {
          queryString += `&warehouse=${warehouse}`;
        }
        if (query) {
          queryString += `q=${query}`;
        }
        return {
          url: `/admin/purchase-report?${queryString}`,
          method: "GET",
        };
      },
    }),
    getCustomerPurchaseReport: builder.query({
      query: ({ fromDate, toDate, warehouse, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString += `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (warehouse) {
          queryString += `&warehouse=${warehouse}`;
        }
        if (query) {
          queryString += `q=${query}`;
        }
        return {
          url: `/customer/purchase-report?${queryString}`,
          method: "GET",
        };
      },
    }),
    getPaymentReport: builder.query({
      query: ({ fromDate, toDate, warehouse, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString += `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (warehouse) {
          queryString += `&warehouse=${warehouse}`;
        }
        if (query) {
          queryString += `q=${query}`;
        }
        return {
          url: `/admin/payment-report?${queryString}`,
          method: "GET",
        };
      },
    }),
    getCustomerPaymentReport: builder.query({
      query: ({ fromDate, toDate, warehouse, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString += `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (warehouse) {
          queryString += `&warehouse=${warehouse}`;
        }
        if (query) {
          queryString += `q=${query}`;
        }
        return {
          url: `/customer/payment-report?${queryString}`,
          method: "GET",
        };
      },
    }),
    getWarehouseStockReport: builder.query({
      query: () => ({
        url: `/admin/warehouse-stock-report`,
        method: "GET",
      }),
    }),
    getLossProfitReport: builder.query({
      query: ({ fromDate, toDate, warehouse, query }) => {
        let queryString = "";
        if (fromDate && toDate) {
          queryString += `from_date=${fromDate}&to_date=${toDate}`;
        }
        if (warehouse) {
          queryString += `&warehouse=${warehouse}`;
        }
        if (query) {
          queryString += `q=${query}`;
        }
        return {
          url: `/admin/loss-profit-report?${queryString}`,
          method: "GET",
        };
      },
    }),
  }),
});

export const {
  useGetExpenseReportQuery,
  useGetSalesReportQuery,
  useGetPurchaseReportQuery,
  useGetCustomerPurchaseReportQuery,
  useGetPaymentReportQuery,
  useGetCustomerPaymentReportQuery,
  useGetWarehouseStockReportQuery,
  useGetLossProfitReportQuery,
} = reportApi;
