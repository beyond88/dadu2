import { Dimensions, Image, StyleSheet, View } from "react-native";
import Text from "../text/Text";
import { colors } from "../../themes/colors";

import { useSelector } from "react-redux";

const TopProduct = ({ topProductData }) => {
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  return (
    <View style={styles.itemWrap}>
      {topProductData?.data?.map((product) => (
        <View style={styles.topProductItem} key={product?.id}>
          <View style={styles.imageWrap}>
            <Image source={{ uri: product?.thumb_url }} style={{ flex: 1 }} />
            <Text preset="h6_m" style={styles.priceBadge}>
              {currency_symbol} {product?.price}
            </Text>
          </View>
          <View style={{ marginTop: 8 }}>
            <Text preset="h2_m">{product?.name}</Text>
          </View>
        </View>
      ))}
    </View>
  );
};

export default TopProduct;

const styles = StyleSheet.create({
  itemWrap: {
    flexDirection: "row",
    gap: 20,
    flexWrap: "wrap",
    justifyContent: "space-between",
    marginBottom: 20,
  },
  topProductItem: {
    backgroundColor: colors.white,
    borderRadius: 5,
    padding: 8,
    width: "47%",
  },
  imageWrap: {
    position: "relative",
    width: "100%",
    aspectRatio: 1,
    position: "relative",
  },

  priceBadge: {
    position: "absolute",
    bottom: 0,
    left: 0,
    backgroundColor: "#EC4561",
    color: colors.white,
    paddingHorizontal: 6,
    paddingVertical: 5,
    borderRadius: 2,
  },
});
