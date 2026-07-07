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
import { useGetWarehouseStockReportQuery } from "../../../../redux/features/report/reportApi";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";

const WarehouseStock = () => {
  const height = Dimensions.get("window").height - 310;

  const renderItem = ({ item }) => (
    <View style={[styles.tableRow, styles.tableBody]}>
      <View style={[styles.tableCol1, styles.tableBodyCol]}>
        <Text preset="h6_m" style={{ color: colors.black }}>
          {item?.name}
        </Text>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          SKU: {item?.sku}
        </Text>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          Category: {item?.category?.name} | Barcode : {item?.barcode}
        </Text>
      </View>
      <View style={[styles.tableCol2, styles.tableBodyCol]}>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          {item?.stock_alert_quantity}
        </Text>
      </View>
      <View style={[styles.tableCol3, styles.tableBodyCol]}>
        <Text preset="h6" style={{ color: colors.pcolor }}>
          {item?.all_stock?.reduce((a, b) => a + Number(b.quantity), 0)}
        </Text>
      </View>
    </View>
  );
  //get data sales return list
  const {
    data: warehouseStockReportData,
    isLoading,
    error,
    isError,
  } = useGetWarehouseStockReportQuery();

  //Render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (warehouseStockReportData?.data?.length === 0) {
    content = <NotFoundMessage message="Warehouse Stock Report Not Found" />;
  } else if (warehouseStockReportData?.data?.length > 0) {
    content = (
      <FlatList
        data={warehouseStockReportData?.data}
        renderItem={renderItem}
        keyExtractor={(item) => item.id}
      />
    );
  }
  return (
    <>
      <Topbar title="Ware Stock Report" />
      <View style={{ paddingHorizontal: 20 }}>
        <View>
          <View style={styles.reportInfoCard}>
            <Text preset="h3_r" style={[styles.infoItem, styles.borderNone]}>
              Purchase Report : All Time
            </Text>
          </View>
          <View style={styles.tableWrap}>
            <View style={[styles.tableRow, styles.tableHeader]}>
              <View style={styles.tableCol1}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Product
                </Text>
              </View>
              <View style={styles.tableCol2}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Alert
                </Text>
              </View>
              <View style={styles.tableCol3}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Stock
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

export default WarehouseStock;

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
    width: "53%",
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
