import { Image, Pressable, ScrollView, StyleSheet, View } from "react-native";
import Text from "../../components/text/Text";
import { useDispatch, useSelector } from "react-redux";
import { colors } from "../../themes/colors";
import Svg, { Path } from "react-native-svg";
import {
  useCustomerTopProductsQuery,
  useGetCustomerHomeQuery,
} from "../../redux/features/home/homeApi";
import CustomerWidget from "../../components/Widget/CustomerWidget";
import { Link, useRouter } from "expo-router";
import TopProduct from "../../components/TopProduct/TopProduct";
import { userLoggedOut } from "../../redux/features/auth/authSlice";
import BestProduct from "../../components/BestProduct/BestProduct";
import LatestSales from "../../components/LatestSales/LatestSales";

const CustomerDashboard = () => {
  //get user Data

  const { user } = useSelector((state) => state.auth);
  const dispatch = useDispatch();
  const router = useRouter();

  //get top product data
  const { data: topProductData } = useCustomerTopProductsQuery();
  //get home data
  const { data: homeData } = useGetCustomerHomeQuery();

  const {
    total_draft_invoice,
    total_invoice,
    total_invoice_amount,
    total_invoice_amount_paid,
    total_product,
    total_return_request,
    total_return_request_accepted,
    total_return_request_amount,
    total_return_request_rejected,
    best_item_all_time,
    latest_sale,
  } = homeData?.data || {};

  const widgetDta = {
    total_draft_invoice,
    total_invoice,
    total_invoice_amount,
    total_invoice_amount_paid,
    total_product,
    total_return_request,
    total_return_request_accepted,
    total_return_request_amount,
    total_return_request_rejected,
  };
  //Handle logout

  const handleLogout = () => {
    dispatch(userLoggedOut());
    router.push("/auth/customer-login");
  };

  return (
    <>
      <View style={styles.dashboardTop}>
        <View style={{ width: 30 }}>
          <Image source={require("../../assets/images/logo-small.png")} />
        </View>
        <View style={styles.dropdownWrap}>
          <View style={styles.profileImgWrap}>
            <Image
              source={{ uri: user?.avatar_url }}
              style={{ width: 30, height: 30 }}
            />
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
      <ScrollView style={styles.dashboardWrapper}>
        <View style={styles.dashboardTitle}>
          <View style={styles.titleIcon}>
            <Image
              source={require("../../assets/images/package.png")}
              contentFit="cover"
              transition={1000}
            />
          </View>
          <Text preset="h1" style={{ color: colors.black }}>
            Customer Overview
          </Text>
        </View>
        <View style={{ marginBottom: 25 }}>
          <CustomerWidget widgetDta={widgetDta} />
        </View>
        <View
          style={{
            flexDirection: "row",
            justifyContent: "space-between",
            alignItems: "center",
            marginBottom: 25,
          }}
        >
          <View style={[styles.dashboardTitle, styles.dashboardTitleArrow]}>
            <View style={styles.titleIcon}>
              <Image
                source={require("../../assets/images/package.png")}
                contentFit="cover"
                transition={1000}
              />
            </View>
            <Text preset="h1" style={{ color: colors.black }}>
              Top Product List
            </Text>
          </View>
        </View>
        <View style={{ marginBottom: 25 }}>
          <TopProduct topProductData={topProductData} />
        </View>
        <View
          style={{
            flexDirection: "row",
            justifyContent: "space-between",
            alignItems: "center",
            marginBottom: 20,
          }}
        >
          <View style={[styles.dashboardTitle, styles.dashboardTitleArrow]}>
            <View style={styles.titleIcon}>
              <Image
                source={require("../../assets/images/package.png")}
                contentFit="cover"
                transition={1000}
              />
            </View>
            <Text preset="h1" style={{ color: colors.black }}>
              Popular item List
            </Text>
          </View>
        </View>
        <View>
          <BestProduct bestItem={best_item_all_time} />
        </View>
        <View style={styles.dashboardTitle}>
          <View style={styles.titleIcon}>
            <Image
              source={require("../../assets/images/package.png")}
              contentFit="cover"
              transition={1000}
            />
          </View>
          <Text preset="h1" style={{ color: colors.black }}>
            Latest Sales
          </Text>
        </View>
        <View style={{ marginBottom: 50 }}>
          <LatestSales latestSales={latest_sale} />
        </View>
      </ScrollView>
    </>
  );
};

export default CustomerDashboard;

const styles = StyleSheet.create({
  dashboardTop: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    backgroundColor: colors.white,
    paddingVertical: 15,
    paddingHorizontal: 20,
  },
  dropdownWrap: {
    alignItems: "center",
    flexDirection: "row",
    flex: 1,
    justifyContent: "flex-end",
  },
  profileImgWrap: {},
  profileImg: {
    width: 30,
    height: 30,
    borderRadius: 5,
  },
  logoutIcon: {
    marginLeft: 10,
    width: 30,
  },
  logoutSvg: {
    width: 20,
    fill: colors.red,
  },
  dashboardWrapper: {
    flex: 1,
    padding: 20,
  },
  dashboardTitle: {
    flexDirection: "row",
    alignItems: "center",
    gap: 10,
    marginBottom: 20,
  },
  dashboardTitleArrow: {
    marginBottom: 0,
  },
  titleIcon: {
    width: 30,
    height: 30,
    borderRadius: 15,
    backgroundColor: colors.primaryShade,
    alignItems: "center",
    justifyContent: "center",
  },
});
