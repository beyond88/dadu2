import { View, ScrollView, StyleSheet, Pressable } from "react-native";
import { Link, useRouter } from "expo-router";
import Svg, { Path, Circle, Rect } from "react-native-svg";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import { useGetUserQuery } from "../../../redux/features/user/userApi";
import { Image } from "expo-image";
import { useEffect } from "react";

const ProfileScreen = () => {
  const { data: userData, refetch } = useGetUserQuery();
  useEffect(() => {
    refetch();
  }, []);
  return (
    <View>
      <Topbar title="View Profile" />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={{ marginBottom: 160 }}>
          <View style={styles.viewProfileCard}>
            <View style={{ alignItems: "center", marginBottom: 15 }}>
              <Image
                source={userData?.data?.avatar_url}
                style={styles.profileImg}
              />
            </View>
            <View>
              <Text
                preset="h1"
                style={{
                  textAlign: "center",
                  color: colors.black,
                  marginBottom: 5,
                }}
              >
                {userData?.data?.name}
              </Text>
              <Text
                preset="h3_r"
                style={{
                  textAlign: "center",
                  color: colors.pcolor,
                  marginBottom: 3,
                }}
              >
                {userData?.data?.email}
              </Text>
              <Text
                preset="h3_r"
                style={{
                  textAlign: "center",
                  color: colors.pcolor,
                  marginBottom: 3,
                }}
              >
                {userData?.data?.phone}
              </Text>
            </View>
            <View style={styles.editBtn}>
              <Link href="/admin/profile/edit-profile">
                <Svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="30"
                  height="30"
                  viewBox="0 0 30 30"
                  fill="none"
                >
                  <Rect width="30" height="30" rx="5" fill="#F9F9F9" />
                  <Path
                    d="M9 20.25H20.25M15.7403 10.4448C15.7403 10.4448 15.7403 11.4665 16.7619 12.4881C17.7835 13.5097 18.8052 13.5097 18.8052 13.5097M11.6998 18.3675L13.8452 18.0611C14.1547 18.0168 14.4415 17.8735 14.6625 17.6524L19.8268 12.4881C20.3911 11.9239 20.3911 11.0091 19.8268 10.4448L18.8052 9.42318C18.2409 8.85894 17.3261 8.85894 16.7619 9.42318L11.5976 14.5875C11.3765 14.8085 11.2332 15.0953 11.1889 15.4048L10.8825 17.5502C10.8143 18.027 11.223 18.4357 11.6998 18.3675Z"
                    stroke="#10A0B1"
                    strokeWidth="1.5"
                    strokeLinecap="round"
                  />
                </Svg>
              </Link>
            </View>
          </View>
          {/* <View style={styles.address}>
            <View style={{ width: "90%" }}>
              <Text
                preset="h2_sb"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Home Address
              </Text>
              <Text preset="h4" style={{ color: colors.pcolor }}>
                (269) 444-6853
              </Text>
              <Text preset="h4" style={{ color: colors.pcolor }}>
                Road Number 5649 Akali
              </Text>
            </View>
            <View>
              <Image source={require("../../../assets/images/address.png")} />
            </View>
          </View>
          <View style={styles.address}>
            <View style={{ width: "90%" }}>
              <Text
                preset="h2_sb"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Billing Address
              </Text>
              <Text preset="h4" style={{ color: colors.pcolor }}>
                (269) 444-6853
              </Text>
              <Text preset="h4" style={{ color: colors.pcolor }}>
                Road Number 5649 Akali
              </Text>
            </View>
            <View>
              <Image
                source={require("../../../assets/images/billing-address.png")}
              />
            </View>
          </View> */}
          <Link href="/admin/pos-invoice" asChild>
            <Pressable style={styles.importantLink}>
              <View>
                <Svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                >
                  <Path
                    d="M8.04 13.62L7.44 14.07H7.44L8.04 13.62ZM9.12 15.06L9.72 14.61L9.72 14.61L9.12 15.06ZM14.88 15.06L14.28 14.61L14.88 15.06ZM15.96 13.62L15.36 13.17V13.17L15.96 13.62ZM9.3 9.45C8.88579 9.45 8.55 9.78579 8.55 10.2C8.55 10.6142 8.88579 10.95 9.3 10.95V9.45ZM12 10.95C12.4142 10.95 12.75 10.6142 12.75 10.2C12.75 9.78579 12.4142 9.45 12 9.45V10.95ZM9.3 5.85C8.88579 5.85 8.55 6.18579 8.55 6.6C8.55 7.01421 8.88579 7.35 9.3 7.35V5.85ZM14.7 7.35C15.1142 7.35 15.45 7.01421 15.45 6.6C15.45 6.18579 15.1142 5.85 14.7 5.85V7.35ZM20.25 15.6V18.3H21.75V15.6H20.25ZM18.3 20.25H5.7V21.75H18.3V20.25ZM3.75 18.3V15.6H2.25V18.3H3.75ZM5.7 13.65H6.6V12.15H5.7V13.65ZM7.44 14.07L8.52 15.51L9.72 14.61L8.64 13.17L7.44 14.07ZM17.4 13.65H18.3V12.15H17.4V13.65ZM15.48 15.51L16.56 14.07L15.36 13.17L14.28 14.61L15.48 15.51ZM12 17.25C13.3692 17.25 14.6585 16.6054 15.48 15.51L14.28 14.61C13.7418 15.3276 12.8971 15.75 12 15.75V17.25ZM17.4 12.15C16.5974 12.15 15.8416 12.5279 15.36 13.17L16.56 14.07C16.7583 13.8056 17.0695 13.65 17.4 13.65V12.15ZM8.52 15.51C9.34152 16.6054 10.6308 17.25 12 17.25V15.75C11.1029 15.75 10.2582 15.3276 9.72 14.61L8.52 15.51ZM6.6 13.65C6.9305 13.65 7.2417 13.8056 7.44 14.07L8.64 13.17C8.15842 12.5279 7.40263 12.15 6.6 12.15V13.65ZM5.7 20.25C4.62304 20.25 3.75 19.377 3.75 18.3H2.25C2.25 20.2054 3.79462 21.75 5.7 21.75V20.25ZM20.25 18.3C20.25 19.377 19.377 20.25 18.3 20.25V21.75C20.2054 21.75 21.75 20.2054 21.75 18.3H20.25ZM21.75 15.6C21.75 13.6946 20.2054 12.15 18.3 12.15V13.65C19.377 13.65 20.25 14.523 20.25 15.6H21.75ZM3.75 15.6C3.75 14.523 4.62304 13.65 5.7 13.65V12.15C3.79462 12.15 2.25 13.6946 2.25 15.6H3.75ZM19.95 12.9V6.6H18.45V12.9H19.95ZM15.6 2.25H8.4V3.75H15.6V2.25ZM4.05 6.6V12.9H5.55V6.6H4.05ZM8.4 2.25C5.99756 2.25 4.05 4.19756 4.05 6.6H5.55C5.55 5.02599 6.82599 3.75 8.4 3.75V2.25ZM19.95 6.6C19.95 4.19756 18.0024 2.25 15.6 2.25V3.75C17.174 3.75 18.45 5.02599 18.45 6.6H19.95ZM9.3 10.95H12V9.45H9.3V10.95ZM9.3 7.35H14.7V5.85H9.3V7.35Z"
                    fill="#4F5B6D"
                  />
                </Svg>
              </View>
              <Text preset="h2_sb">Invoice</Text>
            </Pressable>
          </Link>
          <Link href="/admin/product" asChild>
            <Pressable style={styles.importantLink}>
              <View>
                <Svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                >
                  <Path
                    d="M18.5588 19.5488C17.5654 16.8918 15.0036 15 12 15C8.99638 15 6.4346 16.8918 5.44117 19.5488M18.5588 19.5488C20.6672 17.7154 22 15.0134 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.0134 3.33285 17.7154 5.44117 19.5488M18.5588 19.5488C16.8031 21.0756 14.5095 22 12 22C9.49052 22 7.19694 21.0756 5.44117 19.5488"
                    stroke="#4F5B6D"
                    strokeWidth="1.5"
                    strokeLinejoin="round"
                  />
                  <Circle
                    cx="3"
                    cy="3"
                    r="3"
                    transform="matrix(1 0 0 -1 9 12)"
                    stroke="#4F5B6D"
                    strokeWidth="1.5"
                    strokeLinejoin="round"
                  />
                </Svg>
              </View>
              <Text preset="h2_sb">Product</Text>
            </Pressable>
          </Link>
          <Link href="/admin/purchase" asChild>
            <Pressable style={styles.importantLink}>
              <View>
                <Svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                >
                  <Path
                    d="M21.8889 8C21.8889 5.79086 20.0981 4 17.8889 4H7.00002C4.79088 4 3.00003 5.79086 3.00003 8V17C3.00003 19.2091 4.79089 21 7.00003 21H17.8889C20.0981 21 21.8889 19.2091 21.8889 17V8Z"
                    stroke="#4F5B6D"
                    strokeWidth="1.5"
                    strokeLinejoin="round"
                  />
                  <Path
                    d="M8.66669 12.5001C8.66669 10.9353 7.39816 9.66675 5.83335 9.66675H3.00002V15.3334H5.83335C7.39816 15.3334 8.66669 14.0649 8.66669 12.5001V12.5001Z"
                    stroke="#4F5B6D"
                    strokeWidth="1.5"
                    strokeLinejoin="round"
                  />
                </Svg>
              </View>
              <Text preset="h2_sb">Purchase</Text>
            </Pressable>
          </Link>
        </View>
      </ScrollView>
    </View>
  );
};

export default ProfileScreen;

const styles = StyleSheet.create({
  viewProfileCard: {
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    paddingVertical: 20,
    marginBottom: 20,
    borderRadius: 5,
  },
  editBtn: {
    position: "absolute",
    top: 20,
    right: 20,
    zIndex: 1,
  },
  profileImg: {
    width: 78,
    height: 78,
    borderRadius: 39,
    margin: "auto",
  },
  address: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 16,
    backgroundColor: colors.white,
    borderRadius: 14,
    flexWrap: "wrap",
    marginBottom: 15,
  },
  importantLink: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    backgroundColor: colors.white,
    paddingHorizontal: 16,
    paddingVertical: 16,
    marginBottom: 15,
    borderRadius: 5,
  },
});
