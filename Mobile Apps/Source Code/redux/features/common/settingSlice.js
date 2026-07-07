import { createSlice } from "@reduxjs/toolkit";

const initialState = {
  generalSettings: {},
};

const settingSlice = createSlice({
  name: "setting",
  initialState,
  reducers: {
    generalSettings: (state, action) => {
      state.generalSettings = action.payload;
    },
  },
});

export const { generalSettings } = settingSlice.actions;
export default settingSlice.reducer;
