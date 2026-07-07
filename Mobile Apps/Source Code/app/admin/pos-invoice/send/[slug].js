import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import { Link, router, useLocalSearchParams } from "expo-router";
import { colors } from "../../../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import Text from "../../../../components/text/Text";
import {
  useGetInvoiceCustomerEmailQuery,
  useSendInvoiceMutation,
} from "../../../../redux/features/pos-invoice/posInvoiceApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const SendInvoice = () => {
  const [email, setEmail] = useState("");
  const { slug } = useLocalSearchParams();

  // get customer Email
  const { data: customerEmail, isSuccess } =
    useGetInvoiceCustomerEmailQuery(slug);
  //send invoice
  const [
    sendInvoice,
    {
      data: sendData,
      isLoading: sendIsLoading,
      isSuccess: sendIsSuccess,
      isError: sendIsError,
      error: sendError,
    },
  ] = useSendInvoiceMutation();

  const handleSendInvoice = () => {
    const data = {
      invoice_id: Number(slug),
      email,
    };

    sendInvoice(data);
  };

  useEffect(() => {
    if (customerEmail) {
      setEmail(customerEmail?.data);
    }
  }, [customerEmail, isSuccess]);

  //success & error message
  useEffect(() => {
    if (sendIsSuccess) {
      showMessage({
        message: sendData.message,
        type: "success",
      });
      router.push(`/admin/pos-invoice`);
    }
    if (sendIsError) {
      showMessage({
        message: sendError.data.message,
        type: "danger",
      });
    }
  }, [sendData, sendIsSuccess, sendIsError, sendError]);
  return (
    <>
      {sendIsLoading && <Loading />}
      <Topbar title="Send Invoice" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 100 }}>
        <View style={styles.card}>
          <View style={{ padding: 20 }}>
            <Text style={styles.label} preset="h2_sb">
              Email <Text style={{ color: "red" }}>*</Text>
            </Text>
            <TextInput
              style={styles.textInput}
              onChangeText={(text) => setEmail(text)}
              value={email}
            />
            <View style={styles.download_back_btn}>
              <Link href="/admin/pos-invoice" asChild>
                <View style={styles.back_btn}>
                  <Text preset="h3" style={styles.buttonText}>
                    Back
                  </Text>
                </View>
              </Link>
              <Pressable onPress={handleSendInvoice}>
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.authButton}
                >
                  <Text preset="h3" style={styles.buttonText}>
                    Send
                  </Text>
                </LinearGradient>
              </Pressable>
            </View>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default SendInvoice;

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
    borderRadius: 5,
  },
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },

  label: {
    color: colors.black,
    marginBottom: 10,
  },
  download_back_btn: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingVertical: 20,
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
});
