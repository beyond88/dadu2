import { Pressable, StyleSheet, View } from "react-native";
import Text from "../text/Text";
import { colors } from "../../themes/colors";
import { useRouter } from "expo-router";
import { useDispatch, useSelector } from "react-redux";
import { Image } from "expo-image";
import Svg, { Path } from "react-native-svg";
import { userLoggedOut } from "../../redux/features/auth/authSlice";
const Topbar = ({ title, customer }) => {
  const router = useRouter();
  const dispatch = useDispatch();
  const handleBack = () => {
    router.back();
  };
  //get user Data

  const { user } = useSelector((state) => state.auth);

  //Handle logout

  const handleLogout = () => {
    dispatch(userLoggedOut());
    if (customer) {
      router.push("/auth/customer-login");
      return;
    } else {
      router.push("/auth/admin-login");
    }
  };

  return (
    <View style={styles.topbar}>
      <View>
        <Pressable onPress={handleBack} style={styles.backBtn}>
          <Svg
            width="13"
            height="11"
            viewBox="0 0 13 11"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <Path
              d="M12 5.40637H1"
              stroke="#142A3E"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
            <Path
              d="M5.5 1L1 5.40636L5.5 9.81273"
              stroke="#142A3E"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </Svg>
        </Pressable>
      </View>
      <View>
        <Text preset="h1" style={{ color: colors.black }}>
          {title}
        </Text>
      </View>
      <View style={styles.dropdownWrap}>
        <View style={styles.profileImgWrap}>
          <Image source={user?.avatar_url} style={styles.profileImg} />
        </View>
        <Pressable style={styles.logoutIcon} onPress={handleLogout}>
          <Svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="16"
            viewBox="0 0 18 16"
            fill="none"
          >
            <Path
              d="M8 16C3.58172 16 0 12.4182 0 8C0 3.58172 3.58172 0 8 0C10.617 0 12.9406 1.25662 14.4002 3.19938L12.2327 3.19945C11.1046 2.20399 9.6228 1.6 8 1.6C4.46538 1.6 1.6 4.46538 1.6 8C1.6 11.5346 4.46538 14.4 8 14.4C9.6232 14.4 11.1053 13.7957 12.2335 12.7998H14.4007C12.9412 14.743 10.6174 16 8 16ZM13.6 11.2V8.8H7.2V7.2H13.6V4.8L17.6 8L13.6 11.2Z"
              fill="#EC4561"
            />
          </Svg>
        </Pressable>
      </View>
    </View>
  );
};

export default Topbar;

const styles = StyleSheet.create({
  backBtn: {
    width: 30,
    height: 30,
    backgroundColor: colors.grayBg,
    borderRadius: 5,
    justifyContent: "center",
    alignItems: "center",
  },
  topbar: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    backgroundColor: colors.white,
    marginBottom: 20,
    position: "relative",
  },
  profileImgWrap: {
    width: 30,
  },
  profileImg: {
    width: 30,
    height: 30,
    borderRadius: 5,
  },
  logoutIcon: {
    marginLeft: 10,
    width: 30,
  },
  dropdownWrap: {
    alignItems: "center",
    flexDirection: "row",
  },
});
