import { Redirect, useRootNavigationState } from "expo-router";

const StartPage = () => {
  const rootNavigationState = useRootNavigationState();
  if (!rootNavigationState?.key) return null;
  return <Redirect href="/auth/admin-login" />;
};

export default StartPage;
