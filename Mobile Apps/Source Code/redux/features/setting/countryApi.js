import { apiSlice } from "../api/apiSlice";

export const countryApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getCountriesList: builder.query({
      query: (page) => `/admin/countries?page=${page}`,
      providesTags: ["Country"],
    }),
    deleteCountry: builder.mutation({
      query: (id) => ({
        url: `/admin/countries/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Country"],
    }),
    createCountry: builder.mutation({
      query: (data) => ({
        url: `/admin/countries`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Country"],
    }),
    getSingleCountry: builder.query({
      query: (id) => `/admin/countries/${id}`,
      providesTags: ["Country"],
    }),
    updateCountry: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/countries/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Country"],
    }),
  }),
});

export const {
  useGetCountriesListQuery,
  useDeleteCountryMutation,
  useCreateCountryMutation,
  useGetSingleCountryQuery,
  useUpdateCountryMutation,
} = countryApi;
