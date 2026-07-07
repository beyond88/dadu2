import {
  ScrollView,
  StyleSheet,
  TextInput,
  View,
  Image,
  Pressable,
  TouchableOpacity,
  Modal,
  Dimensions,
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
import { useGetAdminProductsQuery } from "../../../redux/features/product/productApi";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import { addItemToInvoice } from "../../../utils/helper";
import { useCreateAdminInvoiceMutation } from "../../../redux/features/pos-invoice/posInvoiceApi";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
import { useRouter } from "expo-router";
import FormCheckbox from "../../../components/Form/FormCheckbox";
import { Feather } from "@expo/vector-icons";
import { useForm, Controller, set } from "react-hook-form";
import DropDownPicker from "react-native-dropdown-picker";
import { AntDesign } from "@expo/vector-icons";
import {
  useGetCustomerListQuery,
  useGetSingleCustomerQuery,
} from "../../../redux/features/customer/customerApi";
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
  const [transactionDate, setTransactionDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [notes, setNotes] = useState("");
  const [totalPaid, setTotalPaid] = useState(0);
  const [walkInCustomer, setWalkInCustomer] = useState(false);
  const [selectCustomer, setSelectCustomer] = useState(null);
  const [walkInCustomerName, setWalkInCustomerName] = useState(null);
  const [phoneNumber, setPhoneNumber] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [shippingModalVisible, setShippingModalVisible] = useState(false);
  const [billingAddress, setBillingAddress] = useState({});
  const [shippingAddress, setShippingAddress] = useState({});
  const [sameBillingAddress, setSameBillingAddress] = useState(false);
  const [isDelivered, setIsDelivered] = useState(false);
  const [open, setOpen] = useState(false);
  const [totalDiscountOpen, setTotalDiscountOpen] = useState(false);
  const [value, setValue] = useState("percent");
  const [calculatedItems, setCalculatedItems] = useState([]);
  const [totalDiscountValue, setTotalDiscountValue] = useState(0);
  const [totalDiscountType, setTotalDiscountType] = useState("percent");
  const [accountNumber, setAccountNumber] = useState("");
  const [transactionNumber, setTransactionNumber] = useState("");

  //router

  const router = useRouter();
  //handle billing address

  //get form data
  const { control, handleSubmit, reset } = useForm();

  const billingOnSubmit = (data) => {
    setBillingAddress(data);
    setModalVisible(!modalVisible);
  };

  const {
    control: shippingControl,
    handleSubmit: shippingHandleSubmit,
    reset: shippingReset,
  } = useForm();

  const shippingOnSubmit = (data) => {
    setShippingAddress(data);
    setShippingModalVisible(!shippingModalVisible);
  };

  //same billing address
  const toggleSameBilling = () => {
    setSameBillingAddress(!sameBillingAddress);
    const updatedSameBilling = !sameBillingAddress;
    if (updatedSameBilling) {
      setShippingAddress(billingAddress);
    } else {
      setShippingAddress({});
    }
  };
  //get user Data

  const { user: customer } = useSelector((state) => state.auth);

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //toggle walk in customer
  const toggleWalkInCustomer = () => {
    setWalkInCustomer(!walkInCustomer);
  };

  //get customer list
  const { data: customerListData } = useGetCustomerListQuery();
  const customerList = [];

  if (customerListData?.data?.data?.length > 0) {
    customerListData?.data?.data?.map((item) => {
      customerList.push({
        label: item.full_name,
        value: item.id,
      });
    });
  }

  //get single customer

  const { data: singleCustomerData, isSuccess: singleCustomerIsSuccess } =
    useGetSingleCustomerQuery(selectCustomer);

  useEffect(() => {
    if (singleCustomerData) {
      setBillingAddress({
        name: singleCustomerData?.data?.full_name,
        email: singleCustomerData?.data?.email,
        phone: singleCustomerData?.data?.phone,
        address_line_1: singleCustomerData?.data?.address_line_1,
        address_line_2: singleCustomerData?.data?.address_line_2,
        city: singleCustomerData?.data?.city?.name,
        state: singleCustomerData?.data?.state?.name,
        zip: singleCustomerData?.data?.zipcode,
        country: singleCustomerData?.data?.country?.name,
      });
    }
  }, [singleCustomerData, singleCustomerIsSuccess]);

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

  //customer onchange

  const customerOnchange = (value) => {
    setSelectCustomer(value);
  };

  //handle walk in customer name
  const handleWalkInCustomerName = (text) => {
    setWalkInCustomerName(text);
  };

  //handle phone number
  const handlePhoneNumber = (text) => {
    setPhoneNumber(text);
  };
  //payment select

  const handlePaymentType = (value) => {
    setPaymentType(value);
  };

  //handle is delivered

  const toggleIsDelivered = () => {
    setIsDelivered(!isDelivered);
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
  } = useGetAdminProductsQuery(selectedWarehouse);

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

  //handle discount

  const handleDiscount = (text, item) => {
    let discount = Number(text);
    const updatedInvoiceItem = invoiceItem.map((invoiceItem) =>
      invoiceItem.product_id === item.product_id
        ? { ...invoiceItem, givenDiscount: discount }
        : invoiceItem
    );
    setInvoiceItem(updatedInvoiceItem);
  };

  //handle discount type change

  const discountTypeOnchange = (value, item) => {
    const updatedInvoiceItem = invoiceItem.map((invoiceItem) =>
      invoiceItem.product_id === item.product_id
        ? { ...invoiceItem, givenDiscountType: value }
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
          <Text preset="h2_m">Stock: {item?.quantity}</Text>
        </View>
      </TouchableOpacity>
    ));
  }

  //calculation invoice

  const subTotal = invoiceItem?.reduce((acc, item) => {
    return acc + item?.calSubTotal;
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

  const totalDiscount = invoiceItem?.reduce((acc, item) => {
    return acc + item?.discountAmount;
  }, 0);

  let mainDiscountAmount = 0;
  if (totalDiscountType === "percent") {
    mainDiscountAmount = subTotal * (Number(totalDiscountValue) / 100);
  } else {
    mainDiscountAmount = totalDiscountValue;
  }

  //Create invoice Handler

  const [
    createAdminInvoice,
    {
      data: createData,
      isLoading: createLoading,
      isError: createIsError,
      error: createError,
      isSuccess: createIsSuccess,
    },
  ] = useCreateAdminInvoiceMutation();

  const handleCreateInvoice = () => {
    const invoiceData = {
      warehouse_id: selectedWarehouse,
      payment_type: paymentType,
      is_walkin_customer: walkInCustomer ? 1 : 0,
      walkin_customer: {},
      date: selectedDate,
      bank_info: {},
      due_date: selectedDueDate,
      customer_id: customer?.id,
      billing: {
        name: billingAddress?.name,
        email: billingAddress?.email,
        phone: billingAddress?.phone,
        address_line_1: billingAddress?.address_line_1,
        address_line_2: billingAddress?.address_line_2,
        country: billingAddress?.country,
        city: billingAddress?.city,
        state: billingAddress?.state,
        zip: billingAddress?.zipCode,
      },
      shipping: {
        name: shippingAddress?.name,
        email: shippingAddress?.email,
        phone: shippingAddress?.phone,
        address_line_1: shippingAddress?.address_line_1,
        address_line_2: shippingAddress?.address_line_2,
        country: shippingAddress?.country,
        city: shippingAddress?.city,
        state: shippingAddress?.state,
        zip: shippingAddress?.zipCode,
      },
      items: [],
      notes: notes,
      tax: taxVatCal,
      discount: totalDiscountValue || 0,
      discount_type: totalDiscountType || "percent",
      total_paid: totalPaid,
      isDelivered: isDelivered,
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
        discount: item?.givenDiscount || 0,
        discount_type: item?.givenDiscountType || "percent",
      });
    });

    if (walkInCustomer) {
      invoiceData.walkin_customer = {
        full_name: walkInCustomerName,
        phone: phoneNumber,
      };
    }
    if (paymentType === "bank") {
      invoiceData.bank_info = {
        ac_no: accountNumber,
        t_no: transactionNumber,
        date: transactionDate,
      };
    }

    createAdminInvoice(invoiceData);
  };

  //success error

  useEffect(() => {
    if (createIsSuccess) {
      showMessage({
        message: createData?.message,
        type: "success",
      });
      router.push(`/admin/pos-invoice`);
    } else if (createIsError) {
      showMessage({
        message: createError?.data?.message,
        type: "danger",
      });
    }
  }, [createIsError, createError, createIsSuccess, createData]);

  const width = Dimensions.get("window").width;

  useEffect(() => {
    if (invoiceItem) {
      const updatedItems = invoiceItem.map((item) => {
        let quantity = Number(item?.cAddedQuantity);
        let discountTotal = 0;
        let giverDiscountType = item?.givenDiscountType || "percent";
        let givenDiscount = Number(item?.givenDiscount) || 0;
        let price =
          Number(
            item?.customer_buying_price || item?.price || item?.price_for_sale
          ) * quantity;

        if (giverDiscountType === "percent") {
          // discount with quantity
          discountTotal = (price * Number(givenDiscount)) / 100;
        } else {
          discountTotal = Number(givenDiscount) * quantity;
        }

        let subTotal = price - discountTotal;

        return {
          ...item,
          calSubTotal: subTotal,
          discountAmount: discountTotal,
        };
      });

      if (!arraysAreEqual(calculatedItems, updatedItems)) {
        setCalculatedItems(updatedItems);
        setInvoiceItem(updatedItems);
      }
    }
  }, [invoiceItem, calculatedItems]);

  function arraysAreEqual(arr1, arr2) {
    return JSON.stringify(arr1) === JSON.stringify(arr2);
  }

  return (
    <View>
      {/* Billing address */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={modalVisible}
        style={{ width: width }}
        onRequestClose={() => {
          setModalVisible(!modalVisible);
        }}
      >
        <View style={styles.centeredView}>
          <View style={styles.modalView}>
            <ScrollView>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Name
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="name"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Email
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="email"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Phone
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="phone"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Address Line 1
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="address_line_1"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Address Line 2
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="address_line_2"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  City
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="city"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  State
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="state"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Zip Code
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="zipCode"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Country
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="country"
                />
              </View>
            </ScrollView>
            <View>
              <Pressable onPress={handleSubmit(billingOnSubmit)}>
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.authButton}
                >
                  <Text preset="h3" style={styles.buttonText}>
                    Save & Close
                  </Text>
                </LinearGradient>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
      {/* shipping address */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={shippingModalVisible}
        style={{ width: width }}
        onRequestClose={() => {
          setShippingModalVisible(!shippingModalVisible);
        }}
      >
        <View style={styles.centeredView}>
          <View style={styles.modalView}>
            <ScrollView>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Name
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="name"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Email
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="email"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Phone
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="phone"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Address Line 1
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="address_line_1"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Address Line 2
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="address_line_2"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  City
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="city"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  State
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="state"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Zip Code
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="zipCode"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Country
                </Text>
                <Controller
                  control={shippingControl}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="country"
                />
              </View>
            </ScrollView>
            <View>
              <Pressable onPress={shippingHandleSubmit(shippingOnSubmit)}>
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.authButton}
                >
                  <Text preset="h3" style={styles.buttonText}>
                    Save & Close
                  </Text>
                </LinearGradient>
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
      {createLoading && <Loading />}

      <Topbar title="Create Invoice" />
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
              <View
                style={{
                  display: "flex",
                  alignItems: "center",
                  justifyContent: "space-between",
                  flexDirection: "row",
                  marginBottom: 10,
                }}
              >
                <Text preset="h3">
                  {walkInCustomer ? "Customer Name" : "Customer"}
                </Text>
                <View>
                  <FormCheckbox
                    checked={walkInCustomer}
                    toggleCheckbox={toggleWalkInCustomer}
                    label="Walk-in Customer"
                  />
                </View>
              </View>
              {!walkInCustomer ? (
                <View style={{ zIndex: 1 }}>
                  <FormSelect
                    items={customerList}
                    placeholder="Select Customer"
                    onChange={customerOnchange}
                    searchable={true}
                    zIndex={2000}
                    zIndexInverse={3000}
                    position={"TOP"}
                  />
                </View>
              ) : (
                <>
                  <View style={{ marginBottom: 10 }}>
                    <TextInput
                      style={styles.textInput}
                      onChangeText={(text) => handleWalkInCustomerName(text)}
                    />
                  </View>
                  <View style={{ marginBottom: 15 }}>
                    <Text preset="h3" style={{ marginBottom: 8 }}>
                      Phone
                    </Text>
                    <TextInput
                      keyboardType="numeric"
                      style={styles.textInput}
                      placeholderTextColor={colors.pcolor}
                      onChangeText={(text) => handlePhoneNumber(text)}
                    />
                  </View>
                </>
              )}
            </View>
            <View>
              <View
                style={{
                  flexDirection: "row",
                  justifyContent: "space-between",
                  alignItems: "center",
                  marginBottom: 10,
                }}
              >
                <Text preset="h3">Billing Info</Text>
                <Pressable onPress={() => setModalVisible(!modalVisible)}>
                  <Feather name="edit" size={20} color="black" />
                </Pressable>
              </View>
              {Object.keys(billingAddress).length > 0 && (
                <View style={{ marginBottom: 20 }}>
                  {billingAddress?.name && (
                    <Text preset="h5_m" style={{ marginBottom: 2 }}>
                      {billingAddress?.name}
                    </Text>
                  )}
                  {billingAddress?.email && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.email}
                    </Text>
                  )}
                  {billingAddress?.phone && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.phone}
                    </Text>
                  )}
                  {billingAddress?.address_line_1 && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.address_line_1}
                    </Text>
                  )}
                  {billingAddress?.address_line_2 && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.address_line_2}
                    </Text>
                  )}
                  {billingAddress?.city && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.city}
                    </Text>
                  )}
                  {billingAddress?.state && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.state}
                    </Text>
                  )}
                  {billingAddress?.zip && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {billingAddress?.zip}
                    </Text>
                  )}
                  {billingAddress?.country && (
                    <Text preset="h5_m">{billingAddress?.country}</Text>
                  )}
                </View>
              )}
            </View>
            <View>
              <View
                style={{
                  flexDirection: "row",
                  justifyContent: "space-between",
                  alignItems: "center",
                  marginBottom: 10,
                }}
              >
                <Text preset="h3">Shipping Info</Text>
                <Pressable
                  onPress={() => setShippingModalVisible(!shippingModalVisible)}
                >
                  <Feather name="edit" size={20} color="black" />
                </Pressable>
              </View>
              <View style={{ marginBottom: 20 }}>
                <FormCheckbox
                  checked={sameBillingAddress}
                  toggleCheckbox={toggleSameBilling}
                  label="Same as billing"
                />
              </View>
              {Object.keys(shippingAddress).length > 0 && (
                <View style={{ marginBottom: 20 }}>
                  {shippingAddress?.name && (
                    <Text preset="h5_m" style={{ marginBottom: 2 }}>
                      {shippingAddress?.name}
                    </Text>
                  )}
                  {shippingAddress?.email && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.email}
                    </Text>
                  )}
                  {shippingAddress?.phone && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.phone}
                    </Text>
                  )}
                  {shippingAddress?.address_line_1 && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.address_line_1}
                    </Text>
                  )}
                  {shippingAddress?.address_line_2 && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.address_line_2}
                    </Text>
                  )}
                  {shippingAddress?.city && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.city}
                    </Text>
                  )}
                  {shippingAddress?.state && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.state}
                    </Text>
                  )}
                  {shippingAddress?.zip && (
                    <Text preset="h5_m" style={{ marginBottom: 3 }}>
                      {shippingAddress?.zip}
                    </Text>
                  )}
                  {shippingAddress?.country && (
                    <Text preset="h5_m">{shippingAddress?.country}</Text>
                  )}
                </View>
              )}
            </View>
            <View>
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
          </View>
          {invoiceItem?.map((item) => (
            <View
              style={[styles.returnCreateWrap, { marginBottom: 20 }]}
              key={item?.id}
            >
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Description
                </Text>
                <Text style={styles.itemRight}>{item?.product?.name}</Text>
              </View>
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Price
                </Text>
                <Text style={styles.itemRight}>
                  {currency_symbol}{" "}
                  {item?.customer_buying_price ||
                    item?.price ||
                    item?.price_for_sale}
                </Text>
              </View>
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Quantity
                </Text>
                <View style={styles.itemRight}>
                  <TextInput
                    keyboardType="numeric"
                    style={[styles.inputQuantity, { width: "100%" }]}
                    value={item?.cAddedQuantity?.toString()}
                    onChangeText={(text) => handleQuantity(text, item)}
                  />
                </View>
              </View>
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Dis
                </Text>
                <View style={styles.itemRight}>
                  <TextInput
                    keyboardType="numeric"
                    style={[styles.inputQuantity, { width: "100%" }]}
                    value={item?.givenDiscount?.toString()}
                    onChangeText={(text) => handleDiscount(text, item)}
                  />
                </View>
              </View>
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Dis Type
                </Text>
                <View style={styles.itemRight}>
                  <DropDownPicker
                    open={open}
                    value={value}
                    items={[
                      {
                        label: "Percent",
                        value: "percent",
                      },
                      {
                        label: "Fixed",
                        value: "fixed",
                      },
                    ]}
                    setOpen={setOpen}
                    setValue={setValue}
                    listMode="SCROLLVIEW"
                    onSelectItem={(dItem) => {
                      discountTypeOnchange(dItem.value, item);
                    }}
                    dropDownDirection={"TOP"}
                    placeholderStyle={{
                      color: "#727F8B",
                      fontWeight: "regular",
                    }}
                    style={styles.dropdown}
                  />
                </View>
              </View>
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Sub Total
                </Text>
                <Text style={styles.itemRight}>
                  {currency_symbol} {item?.calSubTotal}
                </Text>
              </View>
              <View style={styles.returnCreateItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Action
                </Text>
                <Text style={styles.itemRight}>
                  <AntDesign name="delete" size={16} color="red" />
                </Text>
              </View>
            </View>
          ))}
          <View style={[styles.returnCreateWrap, { marginBottom: 20 }]}>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Sub Total
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol} {subTotal.toFixed(2)}
              </Text>
            </View>

            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Discount type
              </Text>
              <View style={[styles.itemRight, { justifyContent: "flex-end" }]}>
                <DropDownPicker
                  open={totalDiscountOpen}
                  value={totalDiscountType}
                  items={[
                    {
                      label: "Percent",
                      value: "percent",
                    },
                    {
                      label: "Fixed",
                      value: "fixed",
                    },
                  ]}
                  setOpen={setTotalDiscountOpen}
                  setValue={setTotalDiscountType}
                  listMode="SCROLLVIEW"
                  onSelectItem={(dItem) => {
                    setTotalDiscountType(dItem.value);
                  }}
                  dropDownDirection={"TOP"}
                  placeholderStyle={{
                    color: "#727F8B",
                    fontWeight: "regular",
                  }}
                  style={[styles.dropdown, styles.dropdownDiscount]}
                />
              </View>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Discount amount
              </Text>
              <View style={[styles.itemRight]}>
                <TextInput
                  keyboardType="numeric"
                  style={[styles.inputQuantity, { width: "100%" }]}
                  onChangeText={(text) => setTotalDiscountValue(text)}
                />
              </View>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Total Discount
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol}{" "}
                {(Number(totalDiscount) + Number(mainDiscountAmount)).toFixed(
                  2
                )}
              </Text>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Tax/Vat
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol} {taxVatCal.toFixed(2)}
              </Text>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Total
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol}{" "}
                {(subTotal + taxVatCal - Number(mainDiscountAmount)).toFixed(2)}
              </Text>
            </View>
          </View>
          <View style={styles.invoiceTableWrap}>
            {/* <View style={{ marginBottom: 15 }}>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Coupon
              </Text>
              <View>
                <TextInput style={styles.textInput} />
              </View>
            </View> */}
            <View style={{ marginBottom: 15 }}>
              <Text preset="h3" style={{ marginBottom: 8 }}>
                Payment
              </Text>
              <FormRadio
                items={[
                  {
                    label: "Cash",
                    value: "cash",
                  },
                  {
                    label: "Online",
                    value: "online",
                  },
                  {
                    label: "Bank",
                    value: "bank",
                  },
                ]}
                onChange={handlePaymentType}
                selectedValue={paymentType}
              />
            </View>

            <View style={{ marginBottom: 10 }}>
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
            {paymentType === "bank" && (
              <>
                <View style={{ marginBottom: 10 }}>
                  <Text preset="h3" style={{ marginBottom: 8 }}>
                    Account Number
                  </Text>
                  <TextInput
                    keyboardType="numeric"
                    style={styles.textInput}
                    placeholder="Type account number"
                    placeholderTextColor={colors.pcolor}
                    onChangeText={(text) => setAccountNumber(text)}
                  />
                </View>
                <View style={{ marginBottom: 10 }}>
                  <Text preset="h3" style={{ marginBottom: 8 }}>
                    Transaction No
                  </Text>
                  <TextInput
                    style={styles.textInput}
                    placeholder="Type transaction number"
                    placeholderTextColor={colors.pcolor}
                    onChangeText={(text) => setTransactionNumber(text)}
                  />
                </View>
                <View style={{ marginBottom: 10 }}>
                  <Text preset="h3" style={{ marginBottom: 8 }}>
                    Transaction Date
                  </Text>
                  <FormDate setSelectedDate={setTransactionDate} />
                </View>
              </>
            )}

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
            <View style={{ marginTop: 10 }}>
              <FormCheckbox
                checked={isDelivered}
                toggleCheckbox={toggleIsDelivered}
                label="Is Delivered"
              />
            </View>
          </View>
          <View style={styles.draftConfirmBtnWrap}>
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
    paddingHorizontal: 0,
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
    justifyContent: "flex-end",
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

  centeredView: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    marginTop: 22,
  },
  modalView: {
    margin: 20,
    backgroundColor: "white",
    borderRadius: 6,
    padding: 20,
    shadowColor: "#000",
    width: 350,
    shadowOffset: {
      width: 0,
      height: 2,
    },
    shadowOpacity: 0.25,
    shadowRadius: 4,
    elevation: 5,
  },
  inputWrap: {
    marginBottom: 15,
  },
  inputLabel: {
    marginBottom: 10,
    color: colors.black,
  },
  input: {
    height: 40,
    borderColor: colors.border,
    borderWidth: 1,
    borderRadius: 5,
    paddingHorizontal: 20,
  },
  authButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 32,
    borderRadius: 5,
    elevation: 3,
    height: 48,
    minWidth: 130,
  },
  buttonText: {
    color: colors.white,
  },
  returnCreateWrap: {
    backgroundColor: "#fff",
    marginTop: 20,
  },
  returnCreateItem: {
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
  dropdown: {
    backgroundColor: colors.white,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    borderRadius: 5,
    zIndex: 1000,
    minHeight: 40,
    height: 40,
    width: 120,
    marginLeft: "auto",
  },
  dropdownDiscount: {
    width: "100%",
    marginLeft: 10,
  },
});
