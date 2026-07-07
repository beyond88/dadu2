import { apiSlice } from "../api/apiSlice";

export const cityApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getCitiesList: builder.query({
      query: (page) => `/admin/cities?page=${page}`,
      providesTags: ["City"],
    }),
    deleteCity: builder.mutation({
      query: (id) => ({
        url: `/admin/cities/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["City"],
    }),
    createCity: builder.mutation({
      query: (data) => ({
        url: `/admin/cities`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["City"],
    }),
    getSingleCity: builder.query({
      query: (id) => `/admin/cities/${id}`,
      providesTags: ["City"],
    }),
    updateCity: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/cities/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["City"],
    }),
  }),
});

export const {
  useGetCitiesListQuery,
  useDeleteCityMutation,
  useCreateCityMutation,
  useGetSingleCityQuery,
  useUpdateCityMutation,
} = cityApi;
