import { Pressable, ScrollView, View, StyleSheet } from "react-native";
import Text from "../../../../components/text/Text";
import Topbar from "../../../../components/Topbar/Topbar";
import { useState } from "react";
import { colors } from "../../../../themes/colors";
import ReportDateSelect from "../../../../components/ReportDateSelect/ReportDateSelect";
import { LinearGradient } from "expo-linear-gradient";
import { Link } from "expo-router";
import { showMessage } from "react-native-flash-message";

const ExpenseReport = () => {
  //Component  state
  const [selectedFromDate, setSelectedFromDate] = useState("");
  const [selectedToDate, setSelectedToDate] = useState("");

  //handle generate
  const handleGenerate = () => {
    showMessage({
      message: "Please select date",
      type: "danger",
    });
  };
  return (
    <View>
      <Topbar title="Expense Report" />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View>
          <ReportDateSelect
            setSelectedFromDate={setSelectedFromDate}
            setSelectedToDate={setSelectedToDate}
            wareHouseShow={false}
          />
          <View style={styles.generateBtnWrap}>
            {selectedFromDate && selectedToDate ? (
              <Link
                href={`/admin/report/expense/list?fromDate=${selectedFromDate}&toDate=${selectedToDate}`}
              >
                <View style={[styles.generateBtn, styles.btnWhite]}>
                  <Text style={{ color: colors.fontColor }}>Generate</Text>
                </View>
              </Link>
            ) : (
              <Pressable onPress={handleGenerate}>
                <View style={[styles.generateBtn, styles.btnWhite]}>
                  <Text style={{ color: colors.fontColor }}>Generate</Text>
                </View>
              </Pressable>
            )}
            <Link href={`/admin/report/expense/list?query=all-time`}>
              <View>
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.generateBtn}
                >
                  <Text preset="h3" style={{ color: colors.white }}>
                    All Time
                  </Text>
                </LinearGradient>
              </View>
            </Link>
          </View>
        </View>
      </ScrollView>
    </View>
  );
};

export default ExpenseReport;

const styles = StyleSheet.create({
  generateBtnWrap: {
    flexDirection: "row",
    justifyContent: "center",
    gap: 20,
  },
  generateBtn: {
    paddingHorizontal: 32,
    paddingVertical: 12,
    borderRadius: 5,
  },
  btnWhite: {
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: colors.lineBorder,
  },
});
