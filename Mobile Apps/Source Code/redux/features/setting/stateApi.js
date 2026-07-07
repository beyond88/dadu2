import { apiSlice } from "../api/apiSlice";

export const stateApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getStatesList: builder.query({
      query: (page) => `/admin/states?page=${page}`,
      providesTags: ["State"],
    }),
    deleteState: builder.mutation({
      query: (id) => ({
        url: `/admin/states/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["State"],
    }),
    createState: builder.mutation({
      query: (data) => ({
        url: `/admin/states`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["State"],
    }),
    getSingleState: builder.query({
      query: (id) => `/admin/states/${id}`,
      providesTags: ["State"],
    }),
    updateState: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/states/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["State"],
    }),
  }),
});

export const {
  useGetStatesListQuery,
  useDeleteStateMutation,
  useCreateStateMutation,
  useGetSingleStateQuery,
  useUpdateStateMutation,
} = stateApi;
