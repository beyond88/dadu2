import { apiSlice } from "../api/apiSlice";

const userApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getUsersList: builder.query({
      query: () => `/admin/users`,
      providesTags: ["User"],
    }),
    getUser: builder.query({
      query: () => "/admin/login_user/details",
      providesTags: ["User"],
    }),
    updateUser: builder.mutation({
      query: (data) => {
        return {
          url: "/admin/profile-update",
          method: "POST",
          body: data,
        };
      },
      invalidatesTags: ["User"],
    }),
    getCustomerLoginUser: builder.query({
      query: () => "/customer/login_user/details",
      providesTags: ["User"],
    }),
    customerUpdateUser: builder.mutation({
      query: (data) => {
        return {
          url: "/customer/profile-update",
          method: "POST",
          body: data,
        };
      },
      invalidatesTags: ["User"],
    }),
  }),
});

export const {
  useGetUsersListQuery,
  useGetUserQuery,
  useUpdateUserMutation,
  useGetCustomerLoginUserQuery,
  useCustomerUpdateUserMutation,
} = userApi;
