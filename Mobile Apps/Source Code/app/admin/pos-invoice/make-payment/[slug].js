import React, { useEffect, useState } from "react";
import {
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  useGetSingleInvoiceQuery,
  useInvoiceMakePaymentMutation,
} from "../../../../redux/features/pos-invoice/posInvoiceApi";
import { Link, router, useLocalSearchParams } from "expo-router";
import Text from "../../../../components/text/Text";
import { generate8DigitNumber } from "../../../../utils/helper";
import { colors } from "../../../../themes/colors";
import { useSelector } from "react-redux";
import { LinearGradient } from "expo-linear-gradient";
import * as FileSystem from "expo-file-system";
import FormRadio from "../../../../components/Form/FormRadio";
import FormDate from "../../../../components/FormDate/FormDate";
import Loading from "../../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";

const MakePayment = () => {
  const [paymentType, setPaymentType] = useState("cash");
  const [amount, setAmount] = useState(null);
  const [selectedDate, setSelectedDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [accountNumber, setAccountNumber] = useState("");
  const [transactionNumber, setTransactionNumber] = useState("");
  const [amountError, setAmountError] = useState("");
  const { slug } = useLocalSearchParams();
  const apiUrl = process.env.EXPO_PUBLIC_API_URL;

  //get single invoice data
  const { data: singleInvoiceData } = useGetSingleInvoiceQuery(slug);

  const {
    id,
    date,
    billing_info,
    shipping_info,
    status,
    items,
    discount_amount,
    tax_amount,
    total,
    total_paid,
  } = singleInvoiceData?.data || {};

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //Make payment mutation

  const [
    invoiceMakePayment,
    { data: paymentData, isLoading, isError, isSuccess, error },
  ] = useInvoiceMakePaymentMutation();

  //handle payment change

  const handlePaymentChange = (value) => {
    setPaymentType(value);
  };

  //Download pdf

  const handleDownloadPDF = async () => {
    const filename = "invoice";
    const result = await FileSystem.downloadAsync(
      `${apiUrl}/customer/invoice-download/${slug}`,
      FileSystem.documentDirectory + filename
    );
    save(result.uri, filename, result.headers["Content-Type"]);
  };

  //Handle payment

  const handlePayment = () => {
    const data = {
      id: slug,
      payment_type: paymentType,
      last_paid: amount,
      bank_info: {
        ac_no: accountNumber,
        t_no: transactionNumber,
        date: selectedDate,
      },
    };
    if (amount) {
      invoiceMakePayment(data);
    } else {
      setAmountError("Amount is required");
    }
  };

  const save = async (uri, filename, mimetype) => {
    if (Platform.OS === "android") {
      const permissions =
        await FileSystem.StorageAccessFramework.requestDirectoryPermissionsAsync();
      if (permissions.granted) {
        const base64 = await FileSystem.readAsStringAsync(uri, {
          encoding: FileSystem.EncodingType.Base64,
        });
        await FileSystem.StorageAccessFramework.createFileAsync(
          permissions.directoryUri,
          filename,
          mimetype
        )
          .then(async (uri) => {
            await FileSystem.writeAsStringAsync(uri, base64, {
              encoding: FileSystem.EncodingType.Base64,
            });
          })
          .catch((e) => console.log(e));
      } else {
        shareAsync(uri);
      }
    } else {
      shareAsync(uri);
    }
  };

  //show error & success message
  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: paymentData.message,
        type: "success",
      });

      router.push(`/admin/pos-invoice/${slug}`);
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [paymentData, isSuccess, isError, error]);
  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Make Payment" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 20 }}>
        <View style={styles.invoiceCard}>
          <View style={styles.topTile}>
            <View style={{ width: 120 }}>
              <Text></Text>
            </View>
            <View style={{ flex: 1 }}>
              <Text
                preset="h2_sb"
                style={{
                  textAlign: "right",
                  color: "#10A0B1",
                  marginBottom: 5,
                }}
              >
                Invoice#{id && generate8DigitNumber(`${id}`)}
              </Text>
              <Text
                preset="h5_m"
                style={{
                  textAlign: "right",
                  color: "#727F8B",
                  marginBottom: 3,
                }}
              >
                Date : {date ? date : "N/A"}
              </Text>
              <Text
                preset="h5_m"
                style={{
                  textAlign: "right",
                  color: "#727F8B",
                  marginBottom: 3,
                }}
              >
                Total : {total}
              </Text>
              <Text
                preset="h5_m"
                style={{ textAlign: "right", color: "#727F8B" }}
              >
                Status : {status}
              </Text>
            </View>
          </View>
          <View style={styles.address}>
            <View style={{ width: "45%" }}>
              <Text preset="h5_m" style={{ color: "#727F8B", marginBottom: 8 }}>
                Billed To :
              </Text>
              <Text preset="h3" style={{ color: "#142A3E", marginBottom: 4 }}>
                {billing_info?.name}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {billing_info?.address_line_1}, {billing_info?.address_line_2}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {billing_info?.email}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {billing_info?.phone}
              </Text>
            </View>
            <View style={{ width: "45%" }}>
              <Text
                preset="h5_m"
                style={{
                  color: "#727F8B",
                  marginBottom: 8,
                  textAlign: "right",
                }}
              >
                Shipped To :
              </Text>
              <Text
                preset="h3"
                style={{
                  color: "#142A3E",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {shipping_info?.name}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {shipping_info?.address_line_1}, {shipping_info?.address_line_2}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {shipping_info?.email}
              </Text>
              <Text
                preset="h6"
                style={{ color: "#727F8B", textAlign: "right" }}
              >
                {shipping_info?.phone}
              </Text>
            </View>
          </View>
          <View>
            <View style={styles.invoiceTableHead}>
              <Text preset="h4" style={{ width: 80, color: colors.pcolor }}>
                NAME
              </Text>
              <Text preset="h4" style={{ width: 60, color: colors.pcolor }}>
                PRICE
              </Text>
              <Text preset="h4" style={{ width: 30, color: colors.pcolor }}>
                QTY
              </Text>
              <Text
                preset="h4"
                style={{ width: 70, color: colors.pcolor, textAlign: "right" }}
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
              {items?.map((item, index) => (
                <View style={styles.tableBody} key={index}>
                  <Text
                    preset="h6_m"
                    style={{ width: 80, color: colors.black }}
                  >
                    {item?.product_name}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{ width: 60, color: colors.black }}
                  >
                    {}
                    {item?.price}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{ width: 30, color: colors.black }}
                  >
                    {item?.quantity}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{
                      width: 70,
                      color: colors.black,
                      textAlign: "right",
                    }}
                  >
                    {currency_symbol} {item?.sub_total}
                  </Text>
                </View>
              ))}
              <View
                style={[
                  styles.tableBody,
                  { borderTopColor: "#adb5bd4d", borderTopWidth: 1 },
                ]}
              >
                <Text preset="h5" style={{ width: "30%", color: colors.black }}>
                  Discount
                </Text>
                <Text
                  preset="h6_m"
                  style={{
                    width: "65%",
                    color: colors.black,
                    textAlign: "right",
                  }}
                >
                  {discount_amount}
                </Text>
              </View>
              <View
                style={[
                  styles.tableBody,
                  { borderTopColor: "#adb5bd4d", borderTopWidth: 1 },
                ]}
              >
                <Text preset="h5" style={{ width: "30%", color: colors.black }}>
                  Tax/Vat
                </Text>
                <Text
                  preset="h6_m"
                  style={{
                    width: "65%",
                    color: colors.black,
                    textAlign: "right",
                  }}
                >
                  {tax_amount}
                </Text>
              </View>
              <View
                style={[
                  styles.tableBody,
                  { borderTopColor: "#adb5bd4d", borderTopWidth: 1 },
                ]}
              >
                <Text preset="h5" style={{ width: "30%", color: colors.black }}>
                  Total
                </Text>
                <Text
                  preset="h6_m"
                  style={{
                    width: "65%",
                    color: colors.black,
                    textAlign: "right",
                  }}
                >
                  {total}
                </Text>
              </View>
              <View
                style={[
                  styles.tableBody,
                  { borderTopColor: "#adb5bd4d", borderTopWidth: 1 },
                ]}
              >
                <Text preset="h5" style={{ width: "30%", color: colors.black }}>
                  Total Paid
                </Text>
                <Text
                  preset="h6_m"
                  style={{
                    width: "65%",
                    color: colors.black,
                    textAlign: "right",
                  }}
                >
                  {total_paid}
                </Text>
              </View>
              <View
                style={[
                  styles.tableBody,
                  { borderTopColor: "#adb5bd4d", borderTopWidth: 1 },
                ]}
              >
                <Text preset="h5" style={{ width: "30%", color: colors.black }}>
                  Total Due
                </Text>
                <Text
                  preset="h6_m"
                  style={{
                    width: "65%",
                    color: colors.black,
                    textAlign: "right",
                  }}
                >
                  {(Number(total) - Number(total_paid)).toFixed(2)}
                </Text>
              </View>
            </View>
          </View>
          <View style={styles.download_back_btn}>
            <Link href="/admin/pos-invoice" asChild>
              <View style={styles.back_btn}>
                <Text preset="h3" style={styles.buttonText}>
                  Back
                </Text>
              </View>
            </Link>
            <Pressable onPress={handleDownloadPDF}>
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.authButton}
              >
                <Text preset="h3" style={styles.buttonText}>
                  Download
                </Text>
              </LinearGradient>
            </Pressable>
          </View>
          <View style={{ padding: 20, paddingTop: 0 }}>
            <View style={{ flexDirection: "row" }}>
              <FormRadio
                items={[
                  { label: "Cash", value: "cash" },
                  { label: "Online", value: "online" },
                  { label: "Bank", value: "bank" },
                ]}
                selectedValue={paymentType}
                row={true}
                onChange={handlePaymentChange}
              />
            </View>
            <View style={{ marginTop: 10, marginBottom: 16 }}>
              {paymentType === "bank" && (
                <View>
                  <View style={styles.formGroup}>
                    <Text style={styles.label} preset="h2_sb">
                      Account Number
                    </Text>
                    <TextInput
                      keyboardType="numeric"
                      style={styles.textInput}
                      onChangeText={(text) => setAccountNumber(text)}
                    />
                  </View>
                  <View style={styles.formGroup}>
                    <Text style={styles.label} preset="h2_sb">
                      Transaction Number
                    </Text>
                    <TextInput
                      style={styles.textInput}
                      onChangeText={(text) => setTransactionNumber(text)}
                    />
                  </View>
                  <View>
                    <Text style={styles.label} preset="h2_sb">
                      Transaction Date
                    </Text>
                    <FormDate setSelectedDate={setSelectedDate} />
                  </View>
                </View>
              )}
              <View>
                <Text style={styles.label} preset="h2_sb">
                  Amount
                </Text>
                <TextInput
                  keyboardType="numeric"
                  style={styles.textInput}
                  onChangeText={(text) => setAmount(text) || setAmountError("")}
                />

                <Text style={styles.validationError}>{amountError}</Text>
              </View>
            </View>
            <Pressable onPress={handlePayment}>
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.authButton}
              >
                <Text preset="h3" style={styles.buttonText}>
                  Make Payment
                </Text>
              </LinearGradient>
            </Pressable>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default MakePayment;

const styles = StyleSheet.create({
  topTile: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
    gap: 20,
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
  invoiceCard: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
  },
  address: {
    padding: 20,
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
    flexDirection: "row",
    justifyContent: "space-between",
    gap: 20,
    flexWrap: "wrap",
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
  download_back_btn: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
  },
  back_btn: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 5,
    elevation: 3,
    height: 40,
    backgroundColor: "#333",
  },
  authButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 5,
    elevation: 3,
    height: 40,
  },
  buttonText: {
    color: colors.white,
  },
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    color: colors.black,
    marginBottom: 10,
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
});
