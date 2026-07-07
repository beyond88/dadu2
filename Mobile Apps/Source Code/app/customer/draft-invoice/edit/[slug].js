import { useLocalSearchParams, useRouter } from "expo-router";
import {
  Image,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import Text from "../../../../components/text/Text";
import Topbar from "../../../../components/Topbar/Topbar";
import { colors } from "../../../../themes/colors";
import { useEffect, useState } from "react";
import { useGetCategoryQuery } from "../../../../redux/features/common/commonApi";
import FormSelect from "../../../../components/Form/FormSelect";
import {
  useDraftInvoiceToStoreMutation,
  useDraftInvoiceUpdateMutation,
  useGetSingleDraftInvoiceQuery,
} from "../../../../redux/features/pos-invoice/posInvoiceApi";
import { useGetCustomerProductsQuery } from "../../../../redux/features/product/productApi";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import FormDate from "../../../../components/Form/FormDate";
import { updatedAddItemToInvoice } from "../../../../utils/helper";
import FormRadio from "../../../../components/Form/FormRadio";
import { LinearGradient } from "expo-linear-gradient";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const EditDraftInvoice = () => {
  const { slug } = useLocalSearchParams();
  //Get single draft invoice

  const { data: invoiceData } = useGetSingleDraftInvoiceQuery(slug);

  const {
    warehouse_id,
    date: invoiceDate,
    due_date: invoiceDueDate,
  } = invoiceData?.data || {};

  //component state
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [searchTerm, setSearchTerm] = useState("");
  const [date, setDate] = useState(new Date());
  const [dueDate, setDueDate] = useState(new Date());
  const [invoiceItem, setInvoiceItem] = useState([]);
  const [totalPaid, setTotalPaid] = useState(0);
  const [paymentType, setPaymentType] = useState("online");
  const [notes, setNotes] = useState("");

  const router = useRouter();
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
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
  } = useGetCustomerProductsQuery(warehouse_id);

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
  //handle IInvoice

  const handleInvoice = (item) => {
    const addedItem = updatedAddItemToInvoice(invoiceItem, item);
    setInvoiceItem(addedItem);
  };

  //handle quantity

  const handleQuantity = (e, item) => {
    let quantity = e.target.value;
    const stock = item?.stock;
    if (Number(quantity) > Number(stock)) {
      showMessage({
        message: "Stock not available",
        type: "danger",
        position: "bottom",
      });
      quantity = stock;
    }
    //update invoice quantity
    const updatedInvoiceItems = invoiceItem?.items_data?.map((invoiceItem) =>
      invoiceItem.product_id === item.product_id
        ? { ...invoiceItem, quantity: quantity }
        : invoiceItem
    );
    setInvoiceItem({ ...invoiceItem, items_data: updatedInvoiceItems });
  };

  //payment select

  const handlePaymentType = (value) => {
    setPaymentType(value);
  };

  //handle total paid

  const handleTotalPaid = (e) => {
    setTotalPaid(e.target.value);
  };
  //handle note

  const handleNotes = (e) => {
    setNotes(e.target.value);
  };
  //Render content

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

  const subTotal =
    invoiceItem?.items_data?.reduce((acc, item) => {
      return (
        acc +
        Number(item?.quantity) *
          Number(
            item?.customer_buying_price || item?.price || item?.price_for_sale
          )
      );
    }, 0) || 0;

  const taxVatCal =
    invoiceItem?.items_data?.reduce((acc, item) => {
      return (
        acc +
        Number(
          item?.customer_buying_price || item?.price || item?.price_for_sale
        ) *
          Number(item?.quantity) *
          Number(item?.custom_tax / 100)
      );
    }, 0) || 0;

  //Handle update invoice

  const [
    draftInvoiceUpdate,
    {
      data: updateData,
      isLoading: updateIsLoading,
      isSuccess: UpdateIsSuccess,
      isError: updateIsError,
      error: updateError,
    },
  ] = useDraftInvoiceUpdateMutation();
  const [
    draftInvoiceToStore,
    {
      data: moveInvoiceData,
      isLoading: moveIsLoading,
      isSuccess: moveIsSuccess,
      isError: moveIsError,
      error: moveError,
    },
  ] = useDraftInvoiceToStoreMutation();

  const handleUpdateInvoice = (status) => {
    const invoiceData = {
      _method: "PUT",
      warehouse_id: invoiceItem?.warehouse_id,
      payment_type: paymentType,
      is_walkin_customer: 0,
      date: date,
      due_date: dueDate,
      customer_id: invoiceItem?.customer?.id,
      billing: {
        name: `${invoiceItem?.billing_info?.name}`,
        email: invoiceItem?.billing_info?.email,
        phone: invoiceItem?.billing_info?.phone,
        address_line_1: invoiceItem?.billing_info?.address_line_1,
        address_line_2: invoiceItem?.billing_info?.address_line_2,
        country: invoiceItem?.billing_info?.country,
        city: invoiceItem?.billing_info?.city,
        state: invoiceItem?.billing_info?.state,
        zip: invoiceItem?.billing_info?.zipcode,
      },
      shipping: {
        name: `${invoiceItem?.shipping_info?.name}`,
        email: invoiceItem?.shipping_info?.email,
        phone: invoiceItem?.shipping_info?.phone,
        address_line_1: invoiceItem?.shipping_info?.address_line_1,
        address_line_2: invoiceItem?.shipping_info?.address_line_2,
        country: invoiceItem?.shipping_info?.country,
        city: invoiceItem?.shipping_info?.city,
        state: invoiceItem?.shipping_info?.state,
        zip: invoiceItem?.shipping_info?.zipcode,
      },
      items: invoiceItem?.items_data,
      notes: notes,
      tax: taxVatCal,
      discount: 0,
      discount_type: "percent",
      total_paid: totalPaid,
    };
    if (status === "draftInvoiceUpdate") {
      const { _method, ...rest } = invoiceData;
      draftInvoiceUpdate({ data: invoiceData, id: slug });
    } else if (status === "moveInvoice") {
      const { _method, ...updatedData } = invoiceData;
      draftInvoiceToStore({ data: updatedData, id: slug });
    }
  };

  //set date

  useEffect(() => {
    setDate(invoiceDate);
    setDueDate(invoiceDueDate);
  }, [invoiceData]);

  //set invoice item

  useEffect(() => {
    setInvoiceItem(invoiceData?.data);
    setTotalPaid(invoiceData?.data?.total);
    setPaymentType(invoiceData?.data?.payment_type);
    setNotes(invoiceData?.data?.notes);
  }, [invoiceData]);

  //success & error message

  useEffect(() => {
    if (UpdateIsSuccess) {
      showMessage({
        message: updateData?.message,
        type: "success",
      });

      router.push(`/customer/draft-invoice/${updateData?.data?.id}`);
    } else if (updateIsError) {
      showMessage({
        message: updateError?.data?.message,
        type: "danger",
      });
    }
  }, [updateIsError, updateError, UpdateIsSuccess, updateData]);

  useEffect(() => {
    if (moveIsSuccess) {
      showMessage({
        message: moveInvoiceData?.message,
        type: "success",
      });

      router.push(`/customer/invoice/${moveInvoiceData?.data?.id}`);
    } else if (moveIsError) {
      showMessage({
        message: moveError?.data?.message,
        type: "danger",
      });
    }
  }, [moveIsError, moveError, moveIsSuccess, moveInvoiceData]);

  return (
    <View>
      {updateIsLoading && <Loading />}
      {moveIsLoading && <Loading />}
      <Topbar title="Edit Invoice" customer={true} />
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
            <FormDate
              date1={setDate}
              date2={setDueDate}
              selectedDate1={date}
              selectedDate2={dueDate}
              placeholder1="Select Date"
              placeholder2="Select Due Date"
            />
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
            </View>
            <View
              style={{
                paddingHorizontal: 20,
                paddingVertical: 10,
                borderBottomColor: "#adb5bd4d",
                borderBottomWidth: 1,
              }}
            >
              {invoiceItem?.items_data?.map((item) => (
                <View style={styles.tableBody} key={item?.id}>
                  <Text
                    preset="h6_m"
                    style={{ width: 80, color: colors.black }}
                  >
                    {item?.name || item?.product?.name}
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
                  <Text
                    preset="h6_m"
                    style={{ width: 30, color: colors.black }}
                  >
                    <TextInput
                      keyboardType="numeric"
                      style={styles.inputQuantity}
                      value={item?.quantity}
                      onChange={(e) => handleQuantity(e, item)}
                    />
                  </Text>
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
                      Number(item?.quantity) *
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
                  onChange={handleTotalPaid}
                  value={totalPaid}
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
                  onChange={handleNotes}
                  value={notes}
                />
              </View>
            </View>
          </View>
          <View style={styles.draftConfirmBtnWrap}>
            <Pressable
              style={[styles.draftConfirmBtn, styles.draftBtn]}
              onPress={() => handleUpdateInvoice("draftInvoiceUpdate")}
            >
              <Text preset="h3">Update Draft</Text>
            </Pressable>
            <Pressable onPress={() => handleUpdateInvoice("moveInvoice")}>
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

export default EditDraftInvoice;

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
