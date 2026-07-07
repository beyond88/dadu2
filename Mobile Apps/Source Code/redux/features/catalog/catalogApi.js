import { apiSlice } from "../api/apiSlice";

const catalogApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getWeightUnits: builder.query({
      query: (page) => {
        let queryString = "";
        if (page) {
          queryString = `page=${page}`;
        }
        return {
          url: `/admin/weight-units?${queryString}`,
          method: "GET",
        };
      },
      providesTags: ["WeightUnit"],
    }),
    createWeightUnit: builder.mutation({
      query: (body) => ({
        url: `/admin/weight-units`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["WeightUnit"],
    }),
    detailsWeightUnit: builder.query({
      query: (id) => `/admin/weight-units/${id}`,
      providesTags: ["WeightUnit"],
    }),
    updateWeightUnit: builder.mutation({
      query: ({ id, body }) => ({
        url: `/admin/weight-units/${id}`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["WeightUnit"],
    }),
    deleteWeightUnit: builder.mutation({
      query: (id) => ({
        url: `/admin/weight-units/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["WeightUnit"],
    }),
    getMeasurementUnits: builder.query({
      query: (page) => {
        let queryString = "";
        if (page) {
          queryString = `page=${page}`;
        }
        return {
          url: `/admin/measurement-units?${queryString}`,
          method: "GET",
        };
      },
      providesTags: ["MeasurementUnit"],
    }),
    createMeasurementUnit: builder.mutation({
      query: (body) => ({
        url: `/admin/measurement-units`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["MeasurementUnit"],
    }),
    detailsMeasurementUnit: builder.query({
      query: (id) => `/admin/measurement-units/${id}`,
      providesTags: ["MeasurementUnit"],
    }),
    updateMeasurementUnit: builder.mutation({
      query: ({ id, body }) => ({
        url: `/admin/measurement-units/${id}`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["MeasurementUnit"],
    }),
    deleteMeasurementUnit: builder.mutation({
      query: (id) => ({
        url: `/admin/measurement-units/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["MeasurementUnit"],
    }),
    getAttributes: builder.query({
      query: (page) => {
        let queryString = "";
        if (page) {
          queryString = `page=${page}`;
        }
        return {
          url: `/admin/attributes?${queryString}`,
          method: "GET",
        };
      },
      providesTags: ["Attribute"],
    }),
    createAttribute: builder.mutation({
      query: (body) => ({
        url: `/admin/attributes`,
        method: "POST",
        body,
      }),
      invalidatesTags: ["Attribute"],
    }),
    updateAttribute: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/attributes/${id}`,
        method: "PUT",
        body: data,
      }),
      invalidatesTags: ["Attribute"],
    }),

    deleteAttribute: builder.mutation({
      query: (id) => ({
        url: `/admin/attributes/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Attribute"],
    }),
    detailsAttribute: builder.query({
      query: (id) => `/admin/attributes/${id}`,
      providesTags: ["Attribute"],
    }),
  }),
});

export const {
  useGetWeightUnitsQuery,
  useCreateWeightUnitMutation,
  useDetailsWeightUnitQuery,
  useUpdateWeightUnitMutation,
  useDeleteWeightUnitMutation,
  useGetMeasurementUnitsQuery,
  useCreateMeasurementUnitMutation,
  useDetailsMeasurementUnitQuery,
  useUpdateMeasurementUnitMutation,
  useDeleteMeasurementUnitMutation,
  useGetAttributesQuery,
  useCreateAttributeMutation,
  useUpdateAttributeMutation,
  useDeleteAttributeMutation,
  useDetailsAttributeQuery,
} = catalogApi;
