import React, { useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import {
  Image,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import { useSelector } from "react-redux";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import { useGetExpenseQuery } from "../../../redux/features/expense/expenseApi";
import { LinearGradient } from "expo-linear-gradient";
import DateTimePicker from "@react-native-community/datetimepicker";
import DashboardLineChart from "../../../components/Chart/LineChart";
import DashboardPieChart from "../../../components/Chart/PieChart";
import ExpenseList from "../../../components/ExpenseList/ExpenseList";

const Expense = () => {
  const [fromDatePicker, setFromDatePicker] = useState(new Date());
  const [toDatePicker, setToDatePicker] = useState(new Date());
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [showToPicker, setShowToPicker] = useState(false);
  const [fromDate, setFromDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [toDate, setToDate] = useState(new Date().toISOString().slice(0, 10));

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
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //get expense
  const {
    data: getExpense,
    isLoading: expenseIsLoading,
    isError: expenseIsError,
    isSuccess: expenseIsSuccess,
    error: expenseError,
  } = useGetExpenseQuery({
    from_date: fromDate,
    to_date: toDate,
  });

  return (
    <>
      <Topbar title="Expense" />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={styles.salesCard}>
          <View>
            <Text
              preset="h4"
              style={{ color: colors.pcolor, marginBottom: "2px" }}
            >
              Expense This Month
            </Text>
            <Text preset="h1" style={{ color: colors.black }}>
              {currency_symbol} {getExpense?.data?.this_month_total}
            </Text>
          </View>
          <View>
            <Image source={require("../../../assets/images/dot-menu.png")} />
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
          <Pressable>
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
          {getExpense && (
            <DashboardLineChart
              graphChartData={getExpense?.data?.graph_data || []}
            />
          )}
        </View>
        <View style={styles.salesCard}>
          <View>
            <Text
              preset="h4"
              style={{ color: colors.pcolor, marginBottom: "2px" }}
            >
              Expense All Time
            </Text>
            <Text preset="h1" style={{ color: colors.black }}>
              {currency_symbol} {getExpense?.data?.total_all_time}
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
                {currency_symbol} {getExpense?.data?.this_month_total}
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
                {currency_symbol} {getExpense?.data?.last_month_total}
              </Text>
            </View>
          </View>
        </View>
        <View style={styles.dashboardPieChart}>
          {getExpense && (
            <DashboardPieChart
              pieChartData={getExpense?.data?.pie_graph_data || []}
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
                  {currency_symbol} {getExpense?.data?.this_month_total}
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
                  {currency_symbol} {getExpense?.data?.last_month_total}
                </Text>
              </View>
            </View>
          </View>
        </View>
        <View style={{ marginBottom: 50 }}>
          <ExpenseList
            expenses={getExpense?.data?.expenses_list}
            expenseIsLoading={expenseIsLoading}
            expenseIsError={expenseIsError}
            expenseIsSuccess={expenseIsSuccess}
            expenseError={expenseError}
          />
        </View>
      </ScrollView>
    </>
  );
};

export default Expense;

const styles = StyleSheet.create({
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
});
