import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";

export const apiSlice = createApi({
  reducerPath: "api",

  baseQuery: fetchBaseQuery({
    baseUrl: process.env.EXPO_PUBLIC_API_URL,

    prepareHeaders: (headers, { getState, endpoint }) => {
      const token = getState()?.auth?.token;

      if (token) {
        headers.set("authorization", `Bearer ${token}`);
      }
      headers.set("Api-Key", process.env.EXPO_PUBLIC_API_KEY);

      return headers;
    },
  }),
  tagTypes: [
    "Warehouse",
    "Product",
    "Category",
    "Brand",
    "Manufacture",
    "WeightUnit",
    "MeasurementUnit",
    "Attribute",
    "Invoice",
    "SalesReturnRequest",
    "Purchase",
    "Supplier",
    "Coupon",
    "Customer",
    "Supplier",
    "Expense",
    "Country",
    "State",
    "City",
    "User",
  ],
  endpoints: (builder) => ({}),
});
