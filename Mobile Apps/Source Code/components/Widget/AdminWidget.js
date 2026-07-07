import { Image, StyleSheet, View } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import Text from "../text/Text";
import { colors } from "../../themes/colors";
import { useSelector } from "react-redux";

const AdminWidget = ({ widgetDta }) => {
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  return (
    <View style={styles.widgetWrapper}>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#ACC5EF", "#2476FF"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/customer.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_customer || "00"}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Total Customer
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_supplier || "00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Total Supplier
          </Text>
        </View>
        <LinearGradient
          colors={["#60D9C3", "#34A490"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/supplier.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#FDCC95", "#FF9138"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-product.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_product || "00"}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Total Product
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_sale || "00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Total Sale
          </Text>
        </View>
        <LinearGradient
          colors={["#ACC5EF", "#2476FF"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-sale.png")}
            style={{ width: 23, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#FFACA2", "#FE5F4B"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-purchase.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_purchase || "00"}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Total Purchase
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_expenses_amount || "0.00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Total Expenses
          </Text>
        </View>
        <LinearGradient
          colors={["#37DBD9", "#008AA1"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-expense.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#CC8CFE", "#920CFA"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-sale-amount.png")}
            style={{ width: 22, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_sale_amount || "0.00"} {currency_symbol}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Sale Amount
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_purchase_amount || "0.00"} {currency_symbol}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Purchase Amount
          </Text>
        </View>
        <LinearGradient
          colors={["#FDCC95", "#FF9138"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-purchase-amount.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#ACC5EF", "#2476FF"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-expense-amount.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_expenses_amount || "0.00"} {currency_symbol}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Expense Amount
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_sale_return || "00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Sale Returns
          </Text>
        </View>
        <LinearGradient
          colors={["#60D9C3", "#34A490"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-sales-return.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#FFACA2", "#FE5F4B"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-return-request.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_sale_return_request || "00"}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Return Request
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_active_coupon || "00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Active Coupon
          </Text>
        </View>
        <LinearGradient
          colors={["#37DBD9", "#008AA1"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/widget-active-coupon.png")}
            style={{ width: 25, height: 25, objectFit: "contain" }}
          />
        </LinearGradient>
      </View>
    </View>
  );
};
export default AdminWidget;

const styles = StyleSheet.create({
  widgetWrapper: {
    gap: 15,
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
  },
  widget: {
    flexDirection: "row",
    padding: 10,
    backgroundColor: colors.white,
    borderRadius: 5,
    gap: 10,
    alignItems: "center",
    width: "47.5%",
  },

  widgetRight: {
    justifyContent: "flex-end",
  },

  widgetIcon: {
    width: 55,
    height: 55,
    borderRadius: 5,
    alignItems: "center",
    justifyContent: "center",
  },
});
