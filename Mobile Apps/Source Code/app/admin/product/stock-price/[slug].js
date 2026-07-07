import React, { useEffect, useState } from "react";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";
import { Image } from "expo-image";
import { AntDesign } from "@expo/vector-icons";
import FormSelect from "../../../../components/Form/FormSelect";
import {
  useAdminProductsStockPriceShowQuery,
  useAdminProductsStockPriceUpdateMutation,
} from "../../../../redux/features/product/productApi";
import { Link, useLocalSearchParams, useRouter } from "expo-router";
import { generateUniqueId } from "../../../../utils/helper";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const StockPrice = () => {
  //component state
  const [oldStocks, setOldStocks] = useState([]);
  const [newStocks, setNewStocks] = useState([
    {
      id: generateUniqueId(),
      quantity: "0",
      price: "0",
      customer_buying_price: "0",
    },
  ]);
  const [alertQuantity, setAlertQuantity] = useState(null);
  const [selectedWareHouse, setSelectedWarehouse] = useState(null);
  const [selectedSupplier, setSelectedSupplier] = useState(null);
  const [adjustmentType, setAdjustmentType] = useState([]);
  const [quantity, setQuantity] = useState([]);
  const [price, setPrice] = useState([]);
  const [customerBuyingPrice, setCustomerBuyingPrice] = useState([]);

  //router
  const router = useRouter();
  //get slug
  const { slug } = useLocalSearchParams();

  //get stock price
  const { data: stockPriceData } = useAdminProductsStockPriceShowQuery(slug);
  const { warehouses, product_details, old_stocks, suppliers } =
    stockPriceData?.data || {};

  //set old stock

  useEffect(() => {
    setOldStocks(old_stocks);
  }, [old_stocks]);

  //supplier list

  const supplierList = [];

  if (suppliers?.length > 0) {
    suppliers?.map((item) => {
      supplierList.push({
        label: item.fist_name,
        value: item.id,
      });
    });
  }

  const supplierOnchange = (value) => {
    setSelectedSupplier(value);
  };

  // get warehouse list and set selected warehouse
  const warehouseList = [];

  if (warehouses?.length > 0) {
    warehouses?.map((item) => {
      warehouseList.push({
        label: item.name,
        value: item.id,
      });
    });
  }
  const wareHouseOnchange = (value, index) => {
    const newWareHouse = [...selectedWareHouse];
    newWareHouse[index] = `${value}`;
    setSelectedWarehouse(newWareHouse);
  };
  //handle alert quantity change
  const handleAlertQuantityChange = (value) => {
    setAlertQuantity(value);
  };
  //Handle price change

  const handlePriceChange = (value, index) => {
    const newPrice = [...price];
    newPrice[index] = value;
    setPrice(newPrice);
  };
  //handle customer buying price change
  const handleCustomerBuyingPriceChange = (value, index) => {
    const newCustomerBuyingPrice = [...customerBuyingPrice];
    newCustomerBuyingPrice[index] = value;
    setCustomerBuyingPrice(newCustomerBuyingPrice);
  };

  //handle quantity change
  const handleQuantityChange = (value, index) => {
    const newQuantity = [...quantity];
    newQuantity[index] = value;
    setQuantity(newQuantity);
  };

  //handle adjustment type change

  const adjustmentOnchange = (value, index) => {
    const newAdjustmentType = [...adjustmentType];
    newAdjustmentType[index] = value;
    setAdjustmentType(newAdjustmentType);
  };

  //set alert quantity
  useEffect(() => {
    setAlertQuantity(product_details?.product?.stock_alert_quantity);
  }, [stockPriceData]);

  //set warehouse
  useEffect(() => {
    const newWareHouse = [];
    old_stocks?.map((item) => {
      newWareHouse.push(item.warehouse_id);
    });
    setSelectedWarehouse(newWareHouse);
  }, [stockPriceData]);

  //set price
  useEffect(() => {
    const newPrice = [];
    old_stocks?.map((item) => {
      newPrice.push(item.price);
    });
    setPrice(newPrice);
  }, [stockPriceData]);
  //set customer buying price
  useEffect(() => {
    const newCustomerBuyingPrice = [];
    old_stocks?.map((item) => {
      newCustomerBuyingPrice.push(item.customer_buying_price);
    });
    setCustomerBuyingPrice(newCustomerBuyingPrice);
  }, [stockPriceData]);

  //same item add in old stock array  and show in ui
  const handleAdd = () => {
    setQuantity([...quantity, "0"]); // Add a new quantity
    setPrice([...price, "0"]); // Add a new price
    setCustomerBuyingPrice([...customerBuyingPrice, "0"]);
    setOldStocks([
      ...oldStocks,
      {
        id: generateUniqueId(),
        quantity: "0",
        price: "0",
        customer_buying_price: "0",
        isNew: true,
      },
    ]);
  };
  //handle new stock add
  const handleNewStockAdd = () => {
    setQuantity([...quantity, "0"]); // Add a new quantity
    setPrice([...price, "0"]); // Add a new price
    setCustomerBuyingPrice([...customerBuyingPrice, "0"]);
    setNewStocks([
      ...newStocks,
      {
        id: generateUniqueId(),
        quantity: "0",
        price: "0",
        customer_buying_price: "0",
      },
    ]);
  };
  //Handle delete
  const handleDelete = (id) => {
    const newOldStocks = oldStocks?.filter((item) => {
      return item?.id != id;
    });
    setOldStocks(newOldStocks);
  };
  //handle new stock delete
  const handleNewStockDelete = (id) => {
    const newNewStocks = newStocks?.filter((item) => {
      return item?.id != id;
    });
    setNewStocks(newNewStocks);
  };

  //update mutation

  const [
    adminProductsStockPriceUpdate,
    { data: updatedData, isLoading, isError, error, isSuccess },
  ] = useAdminProductsStockPriceUpdateMutation();

  //handle update

  const handleUpdate = () => {
    const data = {
      _method: "PUT",
      id: slug,
      alert_quantity: alertQuantity,
      warehouse_stock: [],
    };
    if (product_details?.product?.is_variant === "1") {
      if (oldStocks?.length > 0) {
        oldStocks?.map((item, index) => {
          data.warehouse_stock.push({
            warehouse: selectedWareHouse[index],
            stock: item?.quantity,
            quantity: quantity[index],
            price: price[index],
            customer_buying_price: customerBuyingPrice[index],
            adjust_type: adjustmentType[index],
          });
        });
      } else {
        newStocks?.map((item, index) => {
          data.warehouse_stock.push({
            warehouse: selectedWareHouse[index],
            stock: item?.quantity,
            quantity: quantity[index],
            price: price[index],
            customer_buying_price: customerBuyingPrice[index],
            adjust_type: adjustmentType[index],
          });
        });
        data.supplier_id = selectedSupplier;
      }
    } else {
      if (oldStocks?.length > 0) {
        oldStocks?.map((item, index) => {
          data.warehouse_stock.push({
            warehouse: selectedWareHouse[index],
            stock: item?.quantity,
            quantity: quantity[index],
            price: price[index],
            customer_buying_price: customerBuyingPrice[index],
            adjust_type: adjustmentType[index],
          });
        });
      } else {
        newStocks?.map((item, index) => {
          data.warehouse_stock.push({
            warehouse: selectedWareHouse[index],
            stock: item?.quantity,
            quantity: quantity[index],
            price: price[index],
            customer_buying_price: customerBuyingPrice[index],
            adjust_type: adjustmentType[index],
          });
        });
        data.supplier_id = selectedSupplier;
      }
    }

    adminProductsStockPriceUpdate({ id: slug, data: data });
  };

  //show success & error message
  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: updatedData.message,
        type: "success",
      });
      router.push("/admin/product");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [updatedData, isSuccess, isError, error]);
  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Stock & Price" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.infoCard}>
            <View style={{ marginBottom: 20 }}>
              <Image
                source={product_details?.product?.thumb_url}
                style={styles.thumbImg}
              />
            </View>
            <View style={{ marginBottom: 10, flexDirection: "row", gap: 10 }}>
              <Text preset="h3" style={{ width: 105 }}>
                Product Name:
              </Text>
              <Text style={{ flex: 1 }}>{product_details?.product?.name}</Text>
            </View>
            <View style={{ flexDirection: "row" }}>
              <Text preset="h3" style={{ width: 105 }}>
                Stock Alert Quantity:
              </Text>
              <View style={{ flex: 1 }}>
                <TextInput
                  style={styles.textInput}
                  value={alertQuantity || ""}
                  onChangeText={(value) => handleAlertQuantityChange(value)}
                  keyboardType="numeric"
                  placeholder="Stock alert quantity"
                />
              </View>
            </View>
          </View>
          {oldStocks?.length > 0
            ? oldStocks?.map((item, index) => (
                <View style={styles.tableCardWrap} key={item?.id}>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Warehouse Name
                    </Text>
                    <View style={styles.itemRight}>
                      <FormSelect
                        items={warehouseList}
                        placeholder="Select warehouse"
                        selectedValue={Number(selectedWareHouse[index])}
                        onChange={wareHouseOnchange}
                        index={index}
                        zIndex={3000}
                        zIndexInverse={2000}
                        position={"TOP"}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Current Stock
                    </Text>
                    <Text style={styles.itemRight}>{item?.quantity}</Text>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Quantity
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={quantity[index] || ""}
                        keyboardType="numeric"
                        onChangeText={(value) =>
                          handleQuantityChange(value, index)
                        }
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Price
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={price[index] || ""}
                        keyboardType="numeric"
                        onChangeText={(value) =>
                          handlePriceChange(value, index)
                        }
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Customer Buying Price
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={customerBuyingPrice[index] || ""}
                        keyboardType="numeric"
                        onChangeText={(value) =>
                          handleCustomerBuyingPriceChange(value, index)
                        }
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Adjustment Type (stock)
                    </Text>
                    <View style={styles.itemRight}>
                      <FormSelect
                        items={[
                          { label: "Add", value: "Add" },
                          { label: "Subtract", value: "Subtract" },
                        ]}
                        placeholder="Select adjustment type"
                        onChange={adjustmentOnchange}
                        index={index}
                        zIndex={3000}
                        zIndexInverse={2000}
                        position={"TOP"}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}></Text>
                    <View style={[styles.itemRight, styles.flexEnd]}>
                      <Pressable
                        style={styles.deleteBtn}
                        disabled={!item?.isNew}
                        onPress={() => handleDelete(item?.id)}
                      >
                        <AntDesign name="delete" size={16} color="white" />
                      </Pressable>
                    </View>
                  </View>
                </View>
              ))
            : newStocks?.map((item, index) => (
                <View style={styles.tableCardWrap} key={item?.id}>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Warehouse Name
                    </Text>
                    <View style={styles.itemRight}>
                      <FormSelect
                        items={warehouseList}
                        placeholder="Select warehouse"
                        onChange={wareHouseOnchange}
                        index={index}
                        zIndex={3000}
                        zIndexInverse={2000}
                        position={"TOP"}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Current Stock
                    </Text>
                    <Text style={styles.itemRight}>{item?.quantity}</Text>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Quantity
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={quantity[index] || ""}
                        keyboardType="numeric"
                        onChangeText={(value) =>
                          handleQuantityChange(value, index)
                        }
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Price
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={price[index] || ""}
                        keyboardType="numeric"
                        onChangeText={(value) =>
                          handlePriceChange(value, index)
                        }
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Customer Buying Price
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={customerBuyingPrice[index] || ""}
                        keyboardType="numeric"
                        onChangeText={(value) =>
                          handleCustomerBuyingPriceChange(value, index)
                        }
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Adjustment Type (stock)
                    </Text>
                    <View style={styles.itemRight}>
                      <FormSelect
                        items={[{ label: "Add", value: "Add" }]}
                        placeholder="Select adjustment type"
                        onChange={adjustmentOnchange}
                        index={index}
                        zIndex={3000}
                        zIndexInverse={2000}
                        position={"BOTTOM"}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}></Text>
                    <View style={[styles.itemRight, styles.flexEnd]}>
                      <Pressable
                        style={styles.deleteBtn}
                        onPress={() => handleNewStockDelete(item?.id)}
                      >
                        <AntDesign name="delete" size={16} color="white" />
                      </Pressable>
                    </View>
                  </View>
                </View>
              ))}
          {oldStocks?.length > 0 ? (
            <View style={{ alignItems: "center", marginTop: 20 }}>
              <Pressable style={styles.addBtn} onPress={handleAdd}>
                <AntDesign name="plus" size={20} color="white" />
              </Pressable>
            </View>
          ) : (
            <>
              <View
                style={{
                  alignItems: "center",
                  marginTop: 20,
                  marginBottom: 20,
                }}
              >
                <Pressable style={styles.addBtn} onPress={handleNewStockAdd}>
                  <AntDesign name="plus" size={20} color="white" />
                </Pressable>
              </View>
              <View style={styles.infoCard}>
                <Text style={{ color: colors.themeColor, marginBottom: 10 }}>
                  NB:The stock is not created yet. First stock will be added in
                  purchase list.
                </Text>
                <Text preset="h5" style={{ marginBottom: 10 }}>
                  Select Supplier
                </Text>
                <FormSelect
                  items={supplierList}
                  placeholder="Select supplier"
                  onChange={supplierOnchange}
                  zIndex={3000}
                  zIndexInverse={2000}
                  position={"BOTTOM"}
                />
              </View>
            </>
          )}
          <View style={styles.formActionBtn}>
            <Pressable style={styles.formBtn} onPress={handleUpdate}>
              <Text preset="h3" style={styles.btnText}>
                Submit
              </Text>
            </Pressable>
            <Link href="/admin/product">
              <View style={[styles.formBtn, styles.cancelBtn]}>
                <Text preset="h3" style={styles.btnText}>
                  Cancel
                </Text>
              </View>
            </Link>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default StockPrice;

const styles = StyleSheet.create({
  infoCard: {
    backgroundColor: "#fff",
    padding: 20,
    borderRadius: 5,
    marginBottom: 20,
  },
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  thumbImg: {
    width: "100%",
    height: 100,
    borderRadius: 5,
    objectFit: "cover",
  },
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
  flexEnd: {
    alignItems: "flex-end",
  },
  deleteBtn: {
    backgroundColor: colors.red,
    width: 36,
    height: 36,
    borderRadius: 5,
    justifyContent: "center",
    alignItems: "center",
  },
  addBtn: {
    backgroundColor: colors.themeColor,
    width: 40,
    height: 40,
    borderRadius: 5,
    justifyContent: "center",
    alignItems: "center",
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "flex-end",
    gap: 10,
    marginTop: 20,
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 25,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
});
