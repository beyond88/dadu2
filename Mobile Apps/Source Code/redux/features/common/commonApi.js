import { apiSlice } from "../api/apiSlice";

const commonApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getWarehouse: builder.query({
      query: () => ({
        url: `/warehouses`,
        method: "GET",
      }),
    }),
    getSetting: builder.query({
      query: () => ({
        url: `/settings`,
        method: "GET",
      }),
    }),
    getCountries: builder.query({
      query: () => ({
        url: `/countries`,
        method: "GET",
      }),
    }),

    getStates: builder.query({
      query: (countryId) => ({
        url: `states?country_id=${countryId}`,
        method: "GET",
      }),
    }),
    getCities: builder.query({
      query: (stateId) => ({
        url: `cities?state_id=${stateId}`,
        method: "GET",
      }),
    }),
    getCategory: builder.query({
      query: () => ({
        url: `/categories`,
        method: "GET",
      }),
    }),
  }),
});

export const {
  useGetWarehouseQuery,
  useGetSettingQuery,
  useGetCountriesQuery,
  useGetStatesQuery,
  useGetCitiesQuery,
  useGetCategoryQuery,
} = commonApi;
