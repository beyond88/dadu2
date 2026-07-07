import {
  SafeAreaView,
  StyleSheet,
  StatusBar,
  Pressable,
  View,
} from "react-native";
import { Slot, useRootNavigationState, useRouter } from "expo-router";
import BottomTab from "../../components/BottomTab/BottomTab";
import SidebarNav from "../../components/SidebarNav/SidebarNav";
import { useState } from "react";
import { useGetSettingQuery } from "../../redux/features/common/commonApi";
import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { generalSettings } from "../../redux/features/common/settingSlice";

const AdminLayout = () => {
  const [backDrop, setBackDrop] = useState(false);
  const [isOpen, setIsOpen] = useState(false);
  const router = useRouter();
  const dispatch = useDispatch();
  const handleMenuOpen = () => {
    setBackDrop(true);
    setIsOpen(true);
  };
  const handleMenuClose = () => {
    setBackDrop(false);
    setIsOpen(false);
  };
  //get general setting
  const { data: generalSettingData, isSuccess } = useGetSettingQuery();

  useEffect(() => {
    if (isSuccess) {
      dispatch(generalSettings(generalSettingData?.data?.general_settings));
    }
  }, [isSuccess]);

  //get user
  const rootNavigationState = useRootNavigationState();
  const { token } = useSelector((state) => state.auth);

  // useEffect(() => {
  //   if (token == undefined) {
  //     router.replace("/auth/admin-login");
  //   }
  // }, [token, rootNavigationState]);

  return (
    <SafeAreaView style={styles.container}>
      {backDrop && (
        <Pressable
          style={styles.menuBackDrop}
          onPress={handleMenuClose}
        ></Pressable>
      )}
      {isOpen && <SidebarNav handleMenuClose={handleMenuClose} />}
      <View style={styles.mainContent}>
        <Slot />
      </View>
      <View>
        <BottomTab handleMenuOpen={handleMenuOpen} role="admin" />
      </View>
    </SafeAreaView>
  );
};

export default AdminLayout;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    marginTop: StatusBar.currentHeight,
    backgroundColor: "#F9F9F9",
    flexDirection: "column",
  },
  mainContent: {
    flex: 1,
    marginBottom: 80,
  },
  menuBackDrop: {
    position: "absolute",
    height: "100%",
    width: "100%",
    backgroundColor: "rgba(26, 37, 48,  0.46)",
    zIndex: 2,
    display: "block",
  },
});
