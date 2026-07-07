import {
  Image,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Text from "../../components/text/Text";
import { colors } from "../../themes/colors";
import AdminWidget from "../../components/Widget/AdminWidget";
import { useState } from "react";
import DateTimePicker from "@react-native-community/datetimepicker";
import { LinearGradient } from "expo-linear-gradient";
import DashboardLineChart from "../../components/Chart/LineChart";
import DashboardPieChart from "../../components/Chart/PieChart";
import Svg, { Path } from "react-native-svg";
import TopProduct from "../../components/TopProduct/TopProduct";
import BestProduct from "../../components/BestProduct/BestProduct";
import LatestSales from "../../components/LatestSales/LatestSales";
import {
  useGetAdminHomeQuery,
  useSalesChartQuery,
  useTopProductsQuery,
} from "../../redux/features/home/homeApi";
import { Link, useRouter } from "expo-router";
import { useDispatch, useSelector } from "react-redux";
import { userLoggedOut } from "../../redux/features/auth/authSlice";
import dayjs from "dayjs";

const Dashboard = () => {
  const [fromDatePicker, setFromDatePicker] = useState(new Date());
  const [toDatePicker, setToDatePicker] = useState(new Date());
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);
  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");

  const router = useRouter();
  const dispatch = useDispatch();

  //toggle date picker
  const toggleFromDatePicker = () => {
    setShowFromPicker(true);
  };
  const toggleToDatePicker = () => {
    setShowToPicker(true);
  };
  //Set from date
  const fromDateChange = (event, selectedDate) => {
    const currentDate = selectedDate || date;
    setShowFromPicker(false);
    setFromDatePicker(currentDate);
    setFromDate(currentDate.toDateString());
  };
  //Set to date
  const toDateChange = (event, selectedDate) => {
    const currentDate = selectedDate || date;
    setShowToPicker(false);
    setToDatePicker(currentDate);
    setToDate(currentDate.toDateString());
  };

  //get top product
  const { data: topProductData } = useTopProductsQuery();

  //get home data

  const { data: homeData, isLoading, isError, error } = useGetAdminHomeQuery();

  const {
    data: salesChartData,
    isSuccess: salesChartIsSuccess,
    error: salesChartError,
    isError: salesChartsIsError,
    isLoading: salesChartIsLoading,
    refetch: salesChartRefetch,
  } = useSalesChartQuery({
    fromDate: dayjs(FormData).format("YYYY-MM-DD"),
    toDate: dayjs(toDate).format("YYYY-MM-DD"),
  });

  //handle sales apply
  const handleSalesApply = () => {
    salesChartRefetch();
  };
  const {
    total_customer,
    total_supplier,
    total_product,
    total_sale,
    total_purchase,
    total_expenses,
    total_sale_amount,
    total_purchase_amount,
    total_expenses_amount,
    total_sale_return,
    total_sale_return_request,
    total_active_coupon,
    best_item_all_time,
    latest_sale,
  } = homeData?.data || {};

  const widgetDta = {
    total_customer,
    total_supplier,
    total_product,
    total_sale,
    total_purchase,
    total_expenses,
    total_sale_amount,
    total_purchase_amount,
    total_expenses_amount,
    total_sale_return,
    total_sale_return_request,
    total_active_coupon,
  };

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //get user Data

  const { user } = useSelector((state) => state.auth);

  //Handle logout

  const handleLogout = () => {
    dispatch(userLoggedOut());
    router.push("/auth/admin-login");
  };

  return (
    <>
      <View style={styles.dashboardTop}>
        <View>
          <Image source={require("../../assets/images/logo-small.png")} />
        </View>
        <View style={styles.dropdownWrap}>
          <View style={styles.profileImgWrap}>
            <Image
              source={{ uri: user?.avatar_url }}
              style={styles.profileImg}
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
            <Image source={require("../../assets/images/package.png")} />
          </View>
          <Text preset="h1" style={{ color: colors.black }}>
            Admin Overview
          </Text>
        </View>

        <View>
          <AdminWidget widgetDta={widgetDta} />
        </View>
        <View style={styles.salesCard}>
          <View>
            <Text
              preset="h4"
              style={{ color: colors.pcolor, marginBottom: "2px" }}
            >
              Sales This Year
            </Text>
            <Text preset="h1" style={{ color: colors.black }}>
              {currency_symbol} {salesChartData?.data?.total}
            </Text>
          </View>
          <View>
            <Image source={require("../../assets/images/dot-menu.png")} />
          </View>
        </View>
        <View style={{ marginBottom: 20 }}>
          <View>
            <Pressable onPress={toggleFromDatePicker}>
              <TextInput
                style={styles.dateInput}
                placeholder="From Date"
                editable={false}
                value={fromDate}
              />
            </Pressable>
            {showFromPicker && (
              <DateTimePicker
                mode="date"
                display="spinner"
                value={fromDatePicker}
                onChange={fromDateChange}
              />
            )}
          </View>
          <View>
            <Pressable onPress={toggleToDatePicker}>
              <TextInput
                style={styles.dateInput}
                placeholder="To Date"
                editable={false}
                value={toDate}
              />
            </Pressable>
            {showToPicker && (
              <DateTimePicker
                mode="date"
                display="spinner"
                value={toDatePicker}
                onChange={toDateChange}
              />
            )}
          </View>
          <Pressable onPress={handleSalesApply}>
            <LinearGradient
              colors={["#37DBD9", "#008AA1"]}
              style={styles.applyButton}
            >
              <Text preset="h3" style={styles.buttonText}>
                Apply
              </Text>
            </LinearGradient>
          </Pressable>
        </View>
        <View>
          {salesChartData && salesChartIsSuccess && (
            <DashboardLineChart
              graphChartData={salesChartData?.data?.graph_data || []}
            />
          )}
        </View>
        <View style={styles.salesCard}>
          <View>
            <Text
              preset="h4"
              style={{ color: colors.pcolor, marginBottom: "2px" }}
            >
              Sales All Time
            </Text>
            <Text preset="h1" style={{ color: colors.black }}>
              {currency_symbol}
              {salesChartData?.data?.total_all_time}
            </Text>
          </View>
          <View>
            <View style={{ flexDirection: "row", marginBottom: 2 }}>
              <Text preset="h6" style={{ color: colors.fontColor }}>
                This Month :
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.themeColor, marginLeft: 5 }}
              >
                {currency_symbol}
                {salesChartData?.data?.this_month_total}
              </Text>
            </View>
            <View style={{ flexDirection: "row" }}>
              <Text preset="h6" style={{ color: colors.fontColor }}>
                Last Month :
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.green, marginLeft: 5 }}
              >
                {currency_symbol}
                {salesChartData?.data?.last_month_total}
              </Text>
            </View>
          </View>
        </View>

        <View style={styles.dashboardPieChart}>
          {salesChartData && (
            <DashboardPieChart
              pieChartData={salesChartData?.data?.pie_graph_data || []}
            />
          )}

          <View
            style={{
              flexDirection: "row",
              justifyContent: "space-between",
              paddingHorizontal: 20,
            }}
          >
            <View style={styles.chartLegend}>
              <View style={[styles.legendDot, styles.dotThisMonth]}></View>
              <View style={styles.legendText}>
                <Text preset="h4" style={{ color: styles.fontColor }}>
                  This Month:{" "}
                </Text>
                <Text preset="h5" style={{ color: "#10A0B1" }}>
                  {currency_symbol}
                  {salesChartData?.data?.this_month_total}
                </Text>
              </View>
            </View>
            <View style={styles.chartLegend}>
              <View style={[styles.legendDot, styles.dotThisMonth]}></View>
              <View style={styles.legendText}>
                <Text preset="h4" style={{ color: styles.fontColor }}>
                  Last Month:{" "}
                </Text>
                <Text preset="h5" style={{ color: "#42CA7F" }}>
                  {currency_symbol}
                  {salesChartData?.data?.last_month_total}
                </Text>
              </View>
            </View>
          </View>
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
              <Image source={require("../../assets/images/package.png")} />
            </View>
            <Text preset="h1" style={{ color: colors.black }}>
              Top Product
            </Text>
          </View>

          <Link href="/admin/product">
            <View>
              <Svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="15"
                viewBox="0 0 18 15"
                fill="none"
              >
                <Path
                  d="M1 7.54541H17"
                  stroke="#4F5B6D"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M10.4545 1L17 7.54545L10.4545 14.0909"
                  stroke="#4F5B6D"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </Svg>
            </View>
          </Link>
        </View>
        <View>
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
              <Image source={require("../../assets/images/package.png")} />
            </View>
            <Text preset="h1" style={{ color: colors.black }}>
              Best Item
            </Text>
          </View>
          <Link href="/admin/product">
            <View>
              <Svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="15"
                viewBox="0 0 18 15"
                fill="none"
              >
                <Path
                  d="M1 7.54541H17"
                  stroke="#4F5B6D"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M10.4545 1L17 7.54545L10.4545 14.0909"
                  stroke="#4F5B6D"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </Svg>
            </View>
          </Link>
        </View>
        <View>
          <BestProduct bestItem={best_item_all_time} />
        </View>
        <View style={styles.dashboardTitle}>
          <View style={styles.titleIcon}>
            <Image source={require("../../assets/images/package.png")} />
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

export default Dashboard;

const styles = StyleSheet.create({
  dashboardTop: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    backgroundColor: colors.white,
    paddingVertical: 15,
    paddingHorizontal: 20,
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
  salesCard: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    backgroundColor: colors.white,
    padding: 20,
    borderRadius: 5,
    marginVertical: 20,
  },
  dateInput: {
    width: "100%",
    height: 45,
    paddingHorizontal: 16,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    marginBottom: 15,
  },
  applyButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 32,
    borderRadius: 5,
    elevation: 3,
    height: 48,
  },
  buttonText: {
    color: colors.white,
  },

  dashboardPieChart: {
    backgroundColor: colors.primaryShade,
    borderRadius: 5,
    paddingVertical: 20,
    marginBottom: 20,
  },
  chartLegend: {
    flexDirection: "row",
    alignItems: "center",
  },
  legendDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    marginRight: 5,
  },
  dotThisMonth: {
    backgroundColor: "#10A0B1",
  },
  legendText: {
    flexDirection: "row",
    alignItems: "center",
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
  logoutSvg: {
    width: 20,
    fill: colors.red,
  },
});
