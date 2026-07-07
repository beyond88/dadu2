import { apiSlice } from "../api/apiSlice";

const settingApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getSettings: builder.query({
      query: () => "/settings",
    }),
  }),
});

export const { useGetSettingsQuery } = settingApi;
