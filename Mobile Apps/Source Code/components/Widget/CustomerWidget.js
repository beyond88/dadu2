import { Image, StyleSheet, View } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import Text from "../text/Text";
import { colors } from "../../themes/colors";
import { useSelector } from "react-redux";

const CustomerWidget = ({ widgetDta }) => {
  const blurhash =
    "|rF?hV%2WCj[ayj[a|j[az_NaeWBj@ayfRayfQfQM{M|azj[azf6fQfQfQIpWXofj[ayj[j[fQayWCoeoeaya}j[ayfQa{oLj?j[WVj[ayayj[fQoff7azayj[ayj[j[ayofayayayj[fQj[ayayj[ayfjj[j[ayjuayj[";

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
            source={require("../../assets/images/c-widget-product.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
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
            {widgetDta?.total_invoice || "00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Total Invoice
          </Text>
        </View>
        <LinearGradient
          colors={["#60D9C3", "#34A490"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-invoice-widget.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#FDCC95", "#FF9138"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-draft-invoice.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_draft_invoice || "00"}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Draft Invoice
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_return_request || "00"}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Return Request
          </Text>
        </View>
        <LinearGradient
          colors={["#ACC5EF", "#2476FF"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-return-request.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#FFACA2", "#FE5F4B"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-accept-request.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_return_request_accepted || "00"}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Accept Request
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_invoice_amount || 0.0} {currency_symbol}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Total Invoice Amount
          </Text>
        </View>
        <LinearGradient
          colors={["#37DBD9", "#008AA1"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-total-invoice-amount.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>
      </View>
      <View style={styles.widget}>
        <LinearGradient
          colors={["#CC8CFE", "#920CFA"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-total-paid.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>

        <View>
          <View>
            <Text preset="h2" style={{ color: colors.black }}>
              {widgetDta?.total_invoice_amount_paid || 0.0} {currency_symbol}
            </Text>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              Total Paid
            </Text>
          </View>
        </View>
      </View>
      <View style={[styles.widget, styles.widgetRight]}>
        <View>
          <Text preset="h2" style={{ color: colors.black, textAlign: "right" }}>
            {widgetDta?.total_invoice_amount -
              widgetDta?.total_invoice_amount_paid || 0.0}{" "}
            {currency_symbol}
          </Text>
          <Text
            preset="h6"
            style={{ color: colors.pcolor, textAlign: "right" }}
          >
            Total Due
          </Text>
        </View>
        <LinearGradient
          colors={["#FDCC95", "#FF9138"]}
          style={styles.widgetIcon}
        >
          <Image
            source={require("../../assets/images/c-total.png")}
            placeholder={blurhash}
            contentFit="cover"
            transition={1000}
          />
        </LinearGradient>
      </View>
    </View>
  );
};
export default CustomerWidget;

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
