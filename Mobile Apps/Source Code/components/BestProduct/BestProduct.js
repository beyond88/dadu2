import { Image, StyleSheet, View } from "react-native";
import Text from "../text/Text";
import { colors } from "../../themes/colors";
import { useSelector } from "react-redux";

const BestProduct = ({ bestItem }) => {
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  return (
    <>
      {bestItem?.map((product) => (
        <View style={styles.bestProductItem} key={product?.id}>
          <View style={styles.imgWrap}>
            <Image
              source={{ uri: product?.thumb_url }}
              style={{ width: 138, height: 96 }}
            />
          </View>
          <View style={styles.contentWrap}>
            <Text preset="h2_m" style={styles.pTitle}>
              {product?.name}
            </Text>
            <View style={{ flexDirection: "row", flexWrap: "wrap" }}>
              <Text preset="h6_m" style={styles.priceBadge}>
                {currency_symbol} {product?.price}
              </Text>
            </View>
          </View>
        </View>
      ))}
    </>
  );
};

export default BestProduct;

const styles = StyleSheet.create({
  bestProductItem: {
    backgroundColor: colors.white,
    borderRadius: 5,
    padding: 8,
    marginBottom: 20,
    flexDirection: "row",
    gap: 20,
    alignItems: "center",
  },
  imgWrap: {
    width: 135,
  },
  contentWrap: {
    flex: 1,
  },
  pTitle: {
    marginBottom: 10,
  },
  priceBadge: {
    backgroundColor: "#EC4561",
    color: colors.white,
    paddingHorizontal: 6,
    paddingVertical: 5,
    borderRadius: 2,
  },
});
