import {
  ScrollView,
  StyleSheet,
  TextInput,
  View,
  Image,
  Pressable,
  TouchableOpacity,
} from "react-native";
import Text from "../../../components/text/Text";
import Topbar from "../../../components/Topbar/Topbar";
import FormSelect from "../../../components/Form/FormSelect";
import {
  useGetCategoryQuery,
  useGetWarehouseQuery,
} from "../../../redux/features/common/commonApi";
import { useEffect, useState } from "react";
import { colors } from "../../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import FormRadio from "../../../components/Form/FormRadio";
import { useGetCustomerProductsQuery } from "../../../redux/features/product/productApi";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import { addItemToInvoice } from "../../../utils/helper";
import {
  useCreateCustomerDraftInvoiceMutation,
  useCreateCustomerInvoiceMutation,
} from "../../../redux/features/pos-invoice/posInvoiceApi";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
import { useRouter } from "expo-router";
import FormDate from "../../../components/FormDate/FormDate";

const CreateInvoice = () => {
  //component state
  const [selectedWarehouse, setSelectedWarehouse] = useState(null);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [paymentType, setPaymentType] = useState("online");
  const [searchTerm, setSearchTerm] = useState("");
  const [invoiceItem, setInvoiceItem] = useState([]);
  const [selectedDate, setSelectedDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [selectedDueDate, setSelectedDueDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [notes, setNotes] = useState("");
  const [totalPaid, setTotalPaid] = useState(0);

  //router

  const router = useRouter();

  //get user Data

  const { user: customer } = useSelector((state) => state.auth);

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  // get warehouse list and set selected warehouse
  const warehouseList = [];
  const { data: warehouseListData } = useGetWarehouseQuery();

  if (warehouseListData?.data?.length > 0) {
    warehouseListData?.data?.map((item) => {
      warehouseList.push({
        label: item.name,
        value: item.id,
      });
    });
  }
  const wareHouseOnchange = (value) => {
    setSelectedWarehouse(value);
  };

  //get category list and set selected category
  const categoryList = [];
  const { data: categoryListData } = useGetCategoryQuery();
  if (categoryListData?.data?.length > 0) {
    categoryListData?.data?.map((item) => {
      categoryList.push({
        label: item.name,
        value: item.id,
      });
    });
  }

  const categoryOnchange = (value) => {
    setSelectedCategory(value);
  };

  //payment select

  const handlePaymentType = (value) => {
    setPaymentType(value);
  };

  //handle search product
  const handleSearchProduct = (e) => {
    setSearchTerm(e.target.value);
  };
  //get product list

  const {
    data: productListData,
    isLoading: pIsloading,
    error: pError,
    isError: pIsError,
  } = useGetCustomerProductsQuery(selectedWarehouse);

  //handle invoice

  const handleInvoice = (item) => {
    const addedItem = addItemToInvoice(invoiceItem, item);
    setInvoiceItem(addedItem);
  };

  //handle quantity

  const handleQuantity = (text, item) => {
    //update invoice quantity
    let quantity = Number(text);

    const stock = item?.quantity;

    if (Number(quantity) > Number(stock)) {
      showMessage({
        message: "Stock not available",
        type: "danger",
        position: "bottom",
      });
      quantity = stock;
    }
    const updatedInvoiceItem = invoiceItem.map((invoiceItem) =>
      invoiceItem.product_id === item.product_id
        ? { ...invoiceItem, cAddedQuantity: quantity }
        : invoiceItem
    );
    setInvoiceItem(updatedInvoiceItem);
  };

  //handle note

  const handleNotes = (text) => {
    setNotes(text);
  };

  //handle total paid

  const handleTotalPaid = (text) => {
    setTotalPaid(Number(text));
  };

  //render product list

  let filteredProducts = productListData?.data || [];

  if (selectedCategory) {
    filteredProducts = filteredProducts.filter((item) => {
      return item?.product?.category_id == selectedCategory;
    });
  }

  if (searchTerm) {
    filteredProducts = filteredProducts.filter((item) => {
      return item?.product?.name
        ?.toLowerCase()
        .includes(searchTerm.toLowerCase());
    });
  }

  let content = null;

  if (pIsloading) {
    content = (
      <View style={{ textAlign: "center", width: "100%" }}>
        <TableLoader />
      </View>
    );
  } else if (pIsError) {
    content = <Text>{pError?.data?.message}</Text>;
  } else if (filteredProducts?.length === 0) {
    content = (
      <Text preset="h2" style={{ paddingVertical: 20, textAlign: "center" }}>
        No Product found
      </Text>
    );
  } else if (filteredProducts?.length > 0) {
    content = filteredProducts?.map((item) => (
      <TouchableOpacity
        style={styles.invoiceProductItem}
        key={item?.id}
        onPress={() => handleInvoice(item)}
      >
        <View style={styles.pImage}>
          <Image source={item?.product?.thumb_url} style={{ flex: 1 }} />
          <Text preset="h6_m" style={styles.priceBadge}>
            {currency_symbol}{" "}
            {item?.customer_buying_price || item?.price || item?.price_for_sale}
          </Text>
        </View>
        <View style={styles.pContent}>
          <Text preset="h2_m">{item?.product?.name}</Text>
        </View>
      </TouchableOpacity>
    ));
  }

  //calculation invoice

  const subTotal = invoiceItem?.reduce((acc, item) => {
    return (
      acc +
      Number(item?.cAddedQuantity) *
        Number(
          item?.customer_buying_price || item?.price || item?.price_for_sale
        )
    );
  }, 0);

  const taxVatCal = invoiceItem?.reduce((acc, item) => {
    return (
      acc +
      Number(
        item?.customer_buying_price || item?.price || item?.price_for_sale
      ) *
        Number(item?.cAddedQuantity) *
        Number(item?.product?.custom_tax / 100)
    );
  }, 0);

  //Create invoice Handler

  const [
    createCustomerInvoice,
    {
      data: createData,
      isLoading: createLoading,
      isError: createIsError,
      error: createError,
      isSuccess: createIsSuccess,
    },
  ] = useCreateCustomerInvoiceMutation();

  const [
    createCustomerDraftInvoice,
    {
      data: createDraftData,
      isLoading: createDraftLoading,
      isError: createDraftIsError,
      error: createDraftError,
      isSuccess: createDraftIsSuccess,
    },
  ] = useCreateCustomerDraftInvoiceMutation();

  const handleCreateInvoice = (invoiceType) => {
    const invoiceData = {
      warehouse_id: selectedWarehouse,
      payment_type: paymentType,
      is_walkin_customer: 0,
      date: selectedDate,
      due_date: selectedDueDate,
      customer_id: customer?.id,
      billing: {
        name: `${customer?.b_first_name} ${customer?.b_last_name}`,
        email: customer?.b_email,
        phone: customer?.b_phone,
        address_line_1: customer?.b_address_line_1,
        address_line_2: customer?.b_address_line_2,
        country: customer?.b_country,
        city: customer?.b_city,
        state: customer?.b_state,
        zip: customer?.b_zipcode,
      },
      shipping: {
        name: customer?.full_name,
        email: customer?.email,
        phone: customer?.phone,
        address_line_1: customer?.address_line_1,
        address_line_2: customer?.address_line_2,
        country: customer?.country,
        city: customer?.city,
        state: customer?.state,
        zip: customer?.zipcode,
      },
      items: [],
      notes: notes,
      tax: taxVatCal,
      discount: 0,
      discount_type: "percent",
      total_paid: totalPaid,
    };
    invoiceItem?.forEach((item) => {
      invoiceData.items.push({
        id: item?.id,
        attribute: {
          id: item?.attribute_id,
          name: item?.attribute,
        },
        attribute_item: {
          id: item?.attribute_id,
          name: item?.attribute,
        },
        is_variant: item?.product?.is_variant,
        product_id: item?.product_id,
        split_sale: item?.product?.split_sale,
        sku: item?.product?.sku,
        name: item?.product?.name,
        price:
          item?.customer_buying_price || item?.price || item?.price_for_sale,
        stock: item?.product?.stock,
        quantity: item?.cAddedQuantity,
        tax_status: item?.product?.tax_status,
        custom_tax: item?.product?.custom_tax,
        discount: 0,
        discount_type: "percent",
      });
    });

    if (invoiceType === "newInvoice") {
      createCustomerInvoice(invoiceData);
    } else {
      createCustomerDraftInvoice(invoiceData);
    }
  };

  //success error

  useEffect(() => {
    if (createIsSuccess) {
      showMessage({
        message: createData?.message,
        type: "success",
      });
      router.push(`/customer/invoice/${createData?.data?.id}`);
    } else if (createIsError) {
      showMessage({
        message: createError?.data?.message,
        type: "danger",
      });
    }
  }, [createIsError, createError, createIsSuccess, createData]);

  useEffect(() => {
    if (createDraftIsSuccess) {
      showMessage({
        message: createDraftData?.message,
        type: "success",
      });
      router.push(`/customer/draft-invoice/${createDraftData?.data?.id}`);
    } else if (createDraftIsError) {
      showMessage({
        message: createDraftError?.data?.message,
        type: "danger",
      });
    }
  }, [
    createDraftIsError,
    createDraftError,
    createDraftIsSuccess,
    createDraftData,
  ]);

  return (
    <View>
      {createLoading && <Loading />}
      {createDraftLoading && <Loading />}
      <Topbar title="Create Invoice" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={styles.filterCard}>
          <View style={{ marginBottom: 15 }}>
            <TextInput
              placeholder="Search Product"
              style={styles.textInput}
              placeholderTextColor={colors.pcolor}
              onChange={handleSearchProduct}
            />
          </View>
          <View style={{ zIndex: 1 }}>
            <FormSelect
              items={warehouseList}
              placeholder="Select warehouse"
              onChange={wareHouseOnchange}
              searchable={true}
              zIndex={3000}
              zIndexInverse={2000}
              position={"BOTTOM"}
            />
          </View>

          <View style={{ zIndex: 0 }}>
            <FormSelect
              items={categoryList}
              placeholder="Select categories"
              onChange={categoryOnchange}
              searchable={true}
              zIndex={1000}
              zIndexInverse={3000}
              position={"BOTTOM"}
            />
          </View>
        </View>
        <View style={styles.invoiceProductWrap}>{content}</View>
        <View style={{ marginBottom: 90 }}>
          <View style={styles.invoiceDateWrap}>
            <View>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Date
              </Text>
              <FormDate setSelectedDate={setSelectedDate} />
            </View>
            <View>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Due Date
              </Text>
              <FormDate setSelectedDate={setSelectedDueDate} />
            </View>
          </View>
          <View style={styles.invoiceTableWrap}>
            <View>
              <View style={styles.invoiceTableHead}>
                <Text preset="h4" style={{ width: 80, color: colors.pcolor }}>
                  DESCRIPTION
                </Text>
                <Text preset="h4" style={{ width: 60, color: colors.pcolor }}>
                  RATE
                </Text>
                <Text preset="h4" style={{ width: 30, color: colors.pcolor }}>
                  QTY
                </Text>
                <Text
                  preset="h4"
                  style={{
                    width: 70,
                    color: colors.pcolor,
                    textAlign: "right",
                  }}
                >
                  SUBTOTAL
                </Text>
              </View>
              <View
                style={{
                  paddingHorizontal: 20,
                  paddingVertical: 10,
                  borderBottomColor: "#adb5bd4d",
                  borderBottomWidth: 1,
                }}
              >
                {invoiceItem?.map((item) => (
                  <View style={styles.tableBody} key={item?.id}>
                    <Text
                      preset="h6_m"
                      style={{ width: 80, color: colors.black }}
                    >
                      {item?.product?.name}
                    </Text>
                    <Text
                      preset="h6_m"
                      style={{ width: 60, color: colors.black }}
                    >
                      {currency_symbol}
                      {item?.customer_buying_price ||
                        item?.price ||
                        item?.price_for_sale}
                    </Text>
                    <View
                      preset="h6_m"
                      style={{ width: 50, color: colors.black }}
                    >
                      <TextInput
                        keyboardType="numeric"
                        style={styles.inputQuantity}
                        value={item?.cAddedQuantity?.toString()}
                        onChangeText={(text) => handleQuantity(text, item)}
                      />
                    </View>
                    <Text
                      preset="h6_m"
                      style={{
                        width: 70,
                        color: colors.black,
                        textAlign: "right",
                      }}
                    >
                      {currency_symbol}{" "}
                      {(
                        Number(item?.cAddedQuantity) *
                        Number(
                          item?.customer_buying_price ||
                            item?.price ||
                            item?.price_for_sale
                        )
                      ).toFixed(2)}
                    </Text>
                  </View>
                ))}
              </View>
            </View>
            <View style={styles.calculation}>
              <View style={styles.itemCalculation}>
                <Text
                  preset="h5"
                  style={{ color: colors.pcolor, marginBottom: 8 }}
                >
                  Sub Total
                </Text>
                <Text
                  preset="h6_m"
                  style={{ color: colors.black, marginBottom: 8 }}
                >
                  {currency_symbol} {subTotal.toFixed(2)}
                </Text>
              </View>

              <View style={styles.itemCalculation}>
                <Text
                  preset="h5"
                  style={{ color: colors.pcolor, marginBottom: 8 }}
                >
                  Tax/Vat
                </Text>
                <Text
                  preset="h6_m"
                  style={{ color: colors.black, marginBottom: 8 }}
                >
                  {currency_symbol} {taxVatCal.toFixed(2)}
                </Text>
              </View>
              <View style={styles.itemCalculation}>
                <Text
                  preset="h5"
                  style={{ color: colors.pcolor, marginBottom: 8 }}
                >
                  Total
                </Text>
                <Text
                  preset="h6_m"
                  style={{ color: colors.black, marginBottom: 8 }}
                >
                  {currency_symbol} {(subTotal + taxVatCal).toFixed(2)}
                </Text>
              </View>
            </View>
            <View style={{ marginBottom: 15 }}>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Payment
              </Text>
              <FormRadio
                items={[
                  {
                    label: "Online",
                    value: "online",
                  },
                ]}
                onChange={handlePaymentType}
                selectedValue={paymentType}
              />
            </View>
            <View style={{ marginBottom: 15 }}>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Total Paid
              </Text>
              <TextInput
                keyboardType="numeric"
                style={styles.textInput}
                placeholder="Type amount"
                placeholderTextColor={colors.pcolor}
                onChangeText={(text) => handleTotalPaid(text)}
              />
            </View>
            <View>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Note
              </Text>
              <TextInput
                multiline={true}
                style={[styles.textInput, { height: 100, paddingTop: 10 }]}
                placeholder="Type Note"
                placeholderTextColor={colors.pcolor}
                onChangeText={(text) => handleNotes(text)}
              />
            </View>
          </View>
          <View style={styles.draftConfirmBtnWrap}>
            <Pressable
              style={[styles.draftConfirmBtn, styles.draftBtn]}
              onPress={() => handleCreateInvoice("draftInvoice")}
            >
              <Text preset="h3">Draft</Text>
            </Pressable>
            <Pressable onPress={() => handleCreateInvoice("newInvoice")}>
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.draftConfirmBtn}
              >
                <Text preset="h3" style={{ color: colors.white }}>
                  Confirm
                </Text>
              </LinearGradient>
            </Pressable>
          </View>
        </View>
      </ScrollView>
    </View>
  );
};

export default CreateInvoice;

const styles = StyleSheet.create({
  filterCard: {
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    paddingVertical: 20,
    marginBottom: 20,
    borderRadius: 5,
    position: "relative",
    zIndex: 1,
  },
  warehouseSelectTitle: {
    color: colors.black,
    paddingBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: colors.lineBorder,
    textAlign: "center",
    marginBottom: 20,
  },
  textInput: {
    backgroundColor: colors.white,
    height: 48,
    paddingHorizontal: 15,
    borderRadius: 5,
    borderWidth: 1,
    borderColor: colors.lineBorder,
  },
  inputQuantity: {
    backgroundColor: colors.white,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    paddingHorizontal: 5,
    width: 50,
    height: 25,
    borderColor: colors.lineBorder,
    borderWidth: 1,
  },
  invoiceProductWrap: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 30,
    paddingBottom: 20,
    gap: 20,
    flexWrap: "wrap",
  },
  invoiceProductItem: {
    width: "47%",
    backgroundColor: colors.white,
    padding: 8,
    borderRadius: 5,
  },
  pImage: {
    width: "100%",
    aspectRatio: 1,
    backgroundColor: "lightgray",
    marginBottom: 10,
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
  invoiceTableHead: {
    paddingHorizontal: 20,
    paddingVertical: 15,
    flexDirection: "row",
    justifyContent: "space-between",
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
  tableBody: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
  },
  paymentTable: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
    paddingHorizontal: 20,
  },
  calculation: {
    padding: 20,
  },
  itemCalculation: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 5,
  },
  totalGrand: {
    paddingVertical: 20,
    paddingBottom: 30,
  },
  totalGrandItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 10,
  },
  invoiceTableWrap: {
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    paddingVertical: 20,
    borderRadius: 5,
  },
  invoiceDateWrap: {
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    paddingTop: 20,
    marginBottom: 20,
    borderRadius: 5,
  },
  draftConfirmBtnWrap: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 20,
  },
  draftConfirmBtn: {
    height: 48,
    width: 150,
    alignItems: "center",
    justifyContent: "center",
    borderRadius: 5,
  },
  draftBtn: {
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: colors.lineBorder,
  },
});
