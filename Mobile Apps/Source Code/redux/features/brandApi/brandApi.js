import { apiSlice } from "../api/apiSlice";

const brandApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getBrands: builder.query({
      query: (page) => {
        let queryString = "";
        if (page) {
          queryString = `page=${page}`;
        }
        return {
          url: `/admin/brands?${queryString}`,
          method: "GET",
        };
      },
      providesTags: ["Brand"],
    }),
    deleteBrand: builder.mutation({
      query: (id) => ({
        url: `/admin/brands/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Brand"],
    }),
    createBrand: builder.mutation({
      query: (body) => ({
        url: `/admin/brands`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["Brand"],
    }),
    brandDetails: builder.query({
      query: (id) => `/admin/brands/${id}`,
      providesTags: ["Brand"],
    }),
    updateBrand: builder.mutation({
      query: ({ id, body }) => ({
        url: `/admin/brands/${id}`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["Brand"],
    }),
  }),
});

export const {
  useGetBrandsQuery,
  useDeleteBrandMutation,
  useCreateBrandMutation,
  useBrandDetailsQuery,
  useUpdateBrandMutation,
} = brandApi;
