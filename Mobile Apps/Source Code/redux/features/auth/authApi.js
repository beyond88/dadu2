import { apiSlice } from "../api/apiSlice";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { userLoggedIn } from "./authSlice";

export const authApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    login: builder.mutation({
      query: (data) => ({
        url: "/admin/login",
        method: "POST",
        body: data,
      }),
      async onQueryStarted(arg, { queryFulfilled, dispatch }) {
        try {
          const result = await queryFulfilled;

          await AsyncStorage.setItem(
            "auth",
            JSON.stringify({
              token: result.data.data.token,
              user: result.data.data.user,
            })
          );
          dispatch(
            userLoggedIn({
              token: result.data.data.token,
              user: result.data.data.user,
            })
          );
        } catch (err) {
          // do nothing
        }
      },
    }),
    customerSignup: builder.mutation({
      query: (data) => ({
        url: "/customer/signup",
        method: "POST",
        body: data,
      }),
    }),
    customerLogin: builder.mutation({
      query: (data) => ({
        url: "/customer/login",
        method: "POST",
        body: data,
      }),
      async onQueryStarted(arg, { queryFulfilled, dispatch }) {
        try {
          const result = await queryFulfilled;

          await AsyncStorage.setItem(
            "auth",
            JSON.stringify({
              token: result.data.data.token,
              user: result.data.data.user,
            })
          );
          dispatch(
            userLoggedIn({
              token: result.data.data.token,
              user: result.data.data.user,
            })
          );
        } catch (err) {
          // do nothing
        }
      },
    }),
  }),
});

export const {
  useLoginMutation,
  useCustomerSignupMutation,
  useCustomerLoginMutation,
} = authApi;
