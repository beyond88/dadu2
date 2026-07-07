import { configureStore } from "@reduxjs/toolkit";
import { apiSlice } from "../features/api/apiSlice";
import authSliceReducer from "../features/auth/authSlice";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { persistReducer, persistStore } from "redux-persist";
import settingSliceReducer from "../features/common/settingSlice";

//auth Persisted reducer
const authPersistConfig = {
  key: "auth",
  storage: AsyncStorage,
};
//auth Persisted reducer
const authPersistedReducer = persistReducer(
  authPersistConfig,
  authSliceReducer
);
export const store = configureStore({
  reducer: {
    [apiSlice.reducerPath]: apiSlice.reducer,
    auth: authPersistedReducer,
    settings: settingSliceReducer,
  },
  //   devTools: process.env.NODE_ENV !== "production",
  middleware: (getDefaultMiddlewares) =>
    getDefaultMiddlewares({
      serializableCheck: false,
    }).concat(apiSlice.middleware),
});
export const persistor = persistStore(store);
