import { apiSlice } from "../api/apiSlice";

export const expenseApi = apiSlice.injectEndpoints({
  endpoints: (builder) => ({
    getExpensesCategories: builder.query({
      query: (page) => `/admin/expenses-categories?page=${page}`,
      providesTags: ["Expense"],
    }),
    deleteExpenseCategory: builder.mutation({
      query: (id) => ({
        url: `/admin/expenses-categories/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Expense"],
    }),
    createExpenseCategory: builder.mutation({
      query: (data) => ({
        url: `/admin/expenses-categories`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Expense"],
    }),
    getDetailsExpenseCategory: builder.query({
      query: (id) => `/admin/expenses-categories/${id}`,
      providesTags: ["Expense"],
    }),
    updateExpenseCategory: builder.mutation({
      query: ({ id, data }) => ({
        url: `/admin/expenses-categories/${id}`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Expense"],
    }),
    getExpense: builder.query({
      query: ({ from_date, to_date }) => {
        let queryString = "";
        if (from_date && to_date) {
          queryString += `?from_date=${from_date}&to_date=${to_date}`;
        }
        return `/admin/expenses${queryString}`;
      },
      providesTags: ["Expense"],
    }),
    getExpenseList: builder.query({
      query: (page) => `/admin/expenses?page=${page}`,
      providesTags: ["Expense"],
    }),
    deleteExpense: builder.mutation({
      query: (id) => ({
        url: `/admin/expenses/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Expense"],
    }),
    getSingleExpense: builder.query({
      query: (id) => `/admin/expenses/${id}`,
      providesTags: ["Expense"],
    }),
    expenseDeleteFile: builder.mutation({
      query: (id) => ({
        url: `/admin/expenses/file/delete/${id}`,
        method: "DELETE",
      }),
      invalidatesTags: ["Expense"],
    }),
    createExpense: builder.mutation({
      query: (data) => ({
        url: `/admin/expenses`,
        method: "POST",
        body: data,
      }),
      invalidatesTags: ["Expense"],
    }),
  }),
});

export const {
  useGetExpensesCategoriesQuery,
  useDeleteExpenseCategoryMutation,
  useCreateExpenseCategoryMutation,
  useGetDetailsExpenseCategoryQuery,
  useUpdateExpenseCategoryMutation,
  useGetExpenseQuery,
  useGetExpenseListQuery,
  useDeleteExpenseMutation,
  useGetSingleExpenseQuery,
  useExpenseDeleteFileMutation,
  useCreateExpenseMutation,
} = expenseApi;
