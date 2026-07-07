import {
  Dimensions,
  FlatList,
  ScrollView,
  StyleSheet,
  View,
} from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";
import { DataTable } from "react-native-paper";
import { useGetSalesReportQuery } from "../../../../redux/features/report/reportApi";
import { useLocalSearchParams } from "expo-router";
import { generate8DigitNumber } from "../../../../utils/helper";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";

const SalesList = () => {
  const { warehouse, fromDate, toDate, query } = useLocalSearchParams();
  const height = Dimensions.get("window").height - 470;
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  const renderItem = ({ item }) => (
    <View style={[styles.tableRow, styles.tableBody]}>
      <View style={[styles.tableCol1, styles.tableBodyCol]}>
        <Text preset="h6_m" style={{ color: colors.black }}>
          # {generate8DigitNumber(item?.id)}
        </Text>
      </View>
      <View style={[styles.tableCol2, styles.tableBodyCol]}>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          {item?.warehouse?.name}
        </Text>
      </View>
      <View style={[styles.tableCol3, styles.tableBodyCol]}>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          {currency_symbol} {item?.total}
        </Text>
      </View>
      <View style={styles.tableCol4}>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          {currency_symbol} {Number(item?.total) - Number(item?.total_paid)}
        </Text>
      </View>
    </View>
  );
  //get data sales return list
  const {
    data: salesReportData,
    isLoading,
    error,
    isError,
  } = useGetSalesReportQuery({ fromDate, toDate, warehouse, query });

  //Render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (salesReportData?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Sales Report Not Found" />;
  } else if (salesReportData?.data?.data?.length > 0) {
    content = (
      <FlatList
        data={salesReportData?.data?.data}
        renderItem={renderItem}
        keyExtractor={(item) => item.id}
      />
    );
  }
  return (
    <>
      <Topbar title="Sales Report" />
      <View style={{ paddingHorizontal: 20 }}>
        <View>
          <View style={styles.reportInfoCard}>
            <Text preset="h3_r" style={styles.infoItem}>
              Purchase Report :{" "}
              {salesReportData?.data?.report_range || "All Time"}
            </Text>
            <Text preset="h3_r" style={styles.infoItem}>
              Gross Total : {currency_symbol}{" "}
              {salesReportData?.data?.gross_total || "0.00"}
            </Text>
            <Text preset="h3_r" style={styles.infoItem}>
              Net Total : {currency_symbol}{" "}
              {salesReportData?.data?.total_paid || "0.00"}
            </Text>
            <Text preset="h3_r" style={[styles.infoItem, styles.borderNone]}>
              Due : {currency_symbol}{" "}
              {Number(salesReportData?.data?.gross_total || 0) -
                Number(salesReportData?.data?.total_paid || 0) || "0.00"}
            </Text>
          </View>
          <View style={styles.tableWrap}>
            <View style={[styles.tableRow, styles.tableHeader]}>
              <View style={styles.tableCol1}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Invoice ID
                </Text>
              </View>
              <View style={styles.tableCol2}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Warehouse
                </Text>
              </View>
              <View style={styles.tableCol3}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Total
                </Text>
              </View>
              <View style={styles.tableCol4}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Due
                </Text>
              </View>
            </View>
            <View style={{ height: height }}>{content}</View>
          </View>
        </View>
      </View>
    </>
  );
};

export default SalesList;

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
