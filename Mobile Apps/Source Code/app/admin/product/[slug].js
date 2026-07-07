import React from "react";
import Topbar from "../../../components/Topbar/Topbar";
import { useLocalSearchParams } from "expo-router";
import { ScrollView, StyleSheet } from "react-native";
import { useGetSingleAdminProductQuery } from "../../../redux/features/product/productApi";
import { View } from "react-native";
import Text from "../../../components/text/Text";
import { Image } from "expo-image";

const ProductDetails = () => {
  const { slug } = useLocalSearchParams();
  //get single products
  const { data: product, isLoading } = useGetSingleAdminProductQuery(slug);
  const {
    name,
    sku,
    barcode,
    category,
    manufacturer,
    model,
    price,
    weight,
    weight_unit,
    notes,
    desc,
    custom_tax_amount,
    tax,
    is_variant,
    is_split_sale,
    image,
    stock_quantity,
  } = product?.data || {};

  return (
    <>
      <Topbar title="Product Details" />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={styles.tableCardWrap}>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Product Name
            </Text>
            <Text style={styles.itemRight}>{name}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              SKU
            </Text>
            <Text style={styles.itemRight}>{sku}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Barcode
            </Text>
            <Text style={styles.itemRight}>{barcode}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Category Name
            </Text>
            <Text style={styles.itemRight}>{category?.name}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Manufacturer
            </Text>
            <Text style={styles.itemRight}>{manufacturer?.name}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Model
            </Text>
            <Text style={styles.itemRight}>{model}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Price
            </Text>
            <Text style={styles.itemRight}>{price}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Weight
            </Text>
            <Text style={styles.itemRight}>{weight}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Weight Unit
            </Text>
            <Text style={styles.itemRight}>{weight_unit?.name}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Notes
            </Text>
            <Text style={styles.itemRight}>{notes}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Description
            </Text>
            <Text style={styles.itemRight}>{desc}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Custom tax amount
            </Text>
            <Text style={styles.itemRight}>{custom_tax_amount}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Tax/Vat
            </Text>
            <Text style={styles.itemRight}>{tax}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Is variant
            </Text>
            <Text style={styles.itemRight}>{is_variant}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Is split sale
            </Text>
            <Text style={styles.itemRight}>{is_split_sale}</Text>
          </View>
          <View style={styles.tableCardItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Image
            </Text>
            <View style={styles.itemRight}>
              <View style={{ alignItems: "flex-end" }}>
                <Image
                  source={image}
                  style={{ width: 60, height: 60, borderRadius: 4 }}
                />
              </View>
            </View>
          </View>
        </View>
        <Text preset="h3" style={{ marginTop: 20 }}>
          Stock Quantity
        </Text>
        {stock_quantity?.map((item, index) => (
          <View style={styles.tableCardWrap} key={index}>
            <View style={styles.tableCardItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Warehouse
              </Text>
              <Text style={styles.itemRight}>{item?.warehouse}</Text>
            </View>
            <View style={styles.tableCardItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Stock quantity
              </Text>
              <Text style={styles.itemRight}>{item?.quantity}</Text>
            </View>
          </View>
        ))}
      </ScrollView>
    </>
  );
};

export default ProductDetails;

const styles = StyleSheet.create({
  tableCardWrap: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    marginTop: 20,
  },
  tableCardItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderBottomColor: "#E9ECF2",
    borderBottomWidth: 1,
    alignItems: "center",
  },
  itemLeft: {
    width: "32%",
  },
  itemRight: {
    flex: 1,
    textAlign: "right",
  },
});
