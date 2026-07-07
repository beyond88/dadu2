import { apiSlice } from "../api/apiSlice";

const manufactureApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getManufactures: builder.query({
      query: (page) => {
        let queryString = "";
        if (page) {
          queryString = `page=${page}`;
        }
        return {
          url: `/admin/manufacturers?${queryString}`,
          method: "GET",
        };
      },
      providesTags: ["Manufacture"],
    }),
    createManufacture: builder.mutation({
      query: (body) => ({
        url: `/admin/manufacturers`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["Manufacture"],
    }),
    detailManufacture: builder.query({
      query: (id) => `/admin/manufacturers/${id}`,
      providesTags: ["Manufacture"],
    }),
    updateManufacture: builder.mutation({
      query: ({ id, body }) => ({
        url: `/admin/manufacturers/${id}`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["Manufacture"],
    }),
    deleteManufacture: builder.mutation({
      query: (id) => ({
        url: `/admin/manufacturers/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Manufacture"],
    }),
  }),
});

export const {
  useGetManufacturesQuery,
  useCreateManufactureMutation,
  useDetailManufactureQuery,
  useUpdateManufactureMutation,
  useDeleteManufactureMutation,
} = manufactureApi;
