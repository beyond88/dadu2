import { StyleSheet, View } from "react-native";
import Text from "../text/Text";
import { colors } from "../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import { generate8DigitNumber } from "../../utils/helper";
import { useSelector } from "react-redux";

const LatestSales = ({ latestSales }) => {
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  return (
    <View style={styles.latestItemWrap}>
      {latestSales?.map((product) => (
        <View style={styles.latestItem} key={product?.id}>
          <View style={styles.left}>
            <Text
              preset="h2_m"
              style={{ color: colors.black, marginBottom: 2 }}
            >
              {product?.customer?.full_name || "Walk-In Customer"}
            </Text>
            <Text preset="h6_m" style={{ color: colors.themeColor }}>
              Invoice# {generate8DigitNumber(product?.id)}
            </Text>
            <View style={{ flexDirection: "row" }}>
              <Text preset="h6" style={{ color: colors.pcolor }}>
                {product?.date} /
              </Text>
              <Text preset="h6_m" style={{ color: colors.green }}>
                {product?.payment_type}
              </Text>
            </View>
          </View>
          <View style={styles.middle}>
            <Text preset="h6_m" style={{ color: colors.fontColor }}>
              Paid:{" "}
            </Text>
            <Text preset="h6_m" style={{ color: colors.yellow }}>
              {currency_symbol} {product?.total_paid}
            </Text>
          </View>
          <View style={styles.right}>
            <Text preset="h5" style={{ color: colors.themeColor }}>
              {currency_symbol} {product?.total}
            </Text>
            {product?.status == "partial_paid" && (
              <LinearGradient
                colors={["#FFACA2", "#FE5F4B"]}
                style={styles.paidBtn}
              >
                <Text style={styles.buttonText}>{product?.status}</Text>
              </LinearGradient>
            )}
            {product?.status == "pending" && (
              <LinearGradient
                colors={["#FDCC95", "#FF9138"]}
                style={styles.paidBtn}
              >
                <Text style={styles.buttonText}>{product?.status}</Text>
              </LinearGradient>
            )}
            {product?.status == "paid" && (
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.paidBtn}
              >
                <Text style={styles.buttonText}>{product?.status}</Text>
              </LinearGradient>
            )}
          </View>
        </View>
      ))}
    </View>
  );
};

export default LatestSales;

const styles = StyleSheet.create({
  latestItemWrap: {
    backgroundColor: colors.white,
    borderRadius: 5,
  },
  left: {
    width: "30%",
  },
  middle: {
    flexDirection: "row",
  },
  latestItem: {
    padding: 15,
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    flexWrap: "wrap",
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
  },
  paidBtn: {
    backgroundColor: colors.green,
    height: 30,
    paddingHorizontal: 16,
    alignItems: "center",
    justifyContent: "center",
    borderRadius: 2,
    marginTop: 5,
  },
  buttonText: {
    color: colors.white,
  },
});
