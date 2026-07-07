import { Dimensions, StyleSheet, View } from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";

import {
  useGetLossProfitReportQuery,
  useGetPurchaseReportQuery,
} from "../../../../redux/features/report/reportApi";
import { useLocalSearchParams } from "expo-router";
import { generate8DigitNumber } from "../../../../utils/helper";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";

const LossProfitList = () => {
  const { warehouse, fromDate, toDate, query } = useLocalSearchParams();

  const height = Dimensions.get("window").height - 320;
  //get data sales return list
  const {
    data: lossProfitReportData,
    isLoading,
    error,
    isError,
  } = useGetLossProfitReportQuery({ fromDate, toDate, warehouse, query });

  const {
    total_sale_qty,
    total_purchase_qty,
    total_purchase_price,
    total_sales_price,
    total_profit,
  } = lossProfitReportData?.data?.data || {};

  //Render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (Object.keys(lossProfitReportData?.data?.data).length === 0) {
    content = <NotFoundMessage message="Loss Profit Report Not Found" />;
  } else if (Object.keys(lossProfitReportData?.data?.data).length > 0) {
    content = (
      <View style={[styles.tableRow, styles.tableBody]}>
        <View style={[styles.tableCol1, styles.tableBodyCol]}>
          <Text preset="h6" style={{ color: colors.pcolor }}>
            Sales Qty- {total_sale_qty}
          </Text>
          <Text preset="h6" style={{ color: colors.pcolor }}>
            Purchase Qty- {total_purchase_qty}
          </Text>
        </View>
        <View style={[styles.tableCol2, styles.tableBodyCol]}>
          <Text preset="h6" style={{ color: colors.pcolor }}>
            {total_sales_price}
          </Text>
        </View>
        <View style={[styles.tableCol3, styles.tableBodyCol]}>
          <Text preset="h6" style={{ color: colors.pcolor }}>
            {total_purchase_price}
          </Text>
        </View>
        <View style={styles.tableCol4}>
          <Text preset="h6" style={{ color: colors.pcolor }}>
            {total_profit?.toFixed(2) || 0}
          </Text>
        </View>
      </View>
    );
  }
  return (
    <View>
      <Topbar title="Loss Profit Report" />
      <View style={{ paddingHorizontal: 20 }}>
        <View>
          <View style={styles.reportInfoCard}>
            <Text preset="h3_r" style={styles.infoItem}>
              Purchase Report :{" "}
              {lossProfitReportData?.data?.report_range || "All Time"}
            </Text>
          </View>
          <View style={styles.tableWrap}>
            <View style={[styles.tableRow, styles.tableHeader]}>
              <View style={styles.tableCol1}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Quantity
                </Text>
              </View>
              <View style={styles.tableCol2}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Sale Amount
                </Text>
              </View>
              <View style={styles.tableCol3}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Purchase
                </Text>
              </View>
              <View style={styles.tableCol4}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Profit
                </Text>
              </View>
            </View>
            <View style={{ height: height }}>{content}</View>
          </View>
        </View>
      </View>
    </View>
  );
};

export default LossProfitList;

const styles = StyleSheet.create({
  reportInfoCard: {
    backgroundColor: colors.white,
    borderRadius: 5,
    marginBottom: 20,
  },
  infoItem: {
    color: colors.fontColor,
    padding: 16,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
  },
  borderNone: {
    borderBottomWidth: 0,
  },

  tableWrap: {
    backgroundColor: colors.white,
    borderBottomRightRadius: 5,
    borderBottomLeftRadius: 5,
    borderWidth: 1,
    borderColor: "#E9ECF2",
  },
  tableRow: {
    flexDirection: "row",
  },
  tableHeader: {
    backgroundColor: colors.themeColor,
    borderTopRightRadius: 5,
    borderTopLeftRadius: 5,
    paddingVertical: 12,
  },
  tableBody: {
    borderBottomWidth: 1,
    borderBottomColor: "#E9ECF2",
  },
  tableBodyCol: {
    borderRightWidth: 1,
    borderRightColor: "#E9ECF2",
    paddingVertical: 9,
    paddingHorizontal: 8,
  },
  tableCol1: {
    width: "30%",
    paddingLeft: 16,
  },
  tableCol2: {
    width: "25%",
    textAlign: "center",
    alignItems: "center",
    justifyContent: "center",
  },
  tableCol3: {
    width: "22%",
    textAlign: "center",
    alignItems: "center",
    justifyContent: "center",
  },
  tableCol4: {
    width: "22%",
    textAlign: "center",
    alignItems: "center",
    justifyContent: "center",
  },
});
