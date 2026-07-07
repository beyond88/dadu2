import { Pressable, ScrollView, StyleSheet, View } from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import { colors } from "../../../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import { Link, useLocalSearchParams } from "expo-router";
import { useGetSingleInvoiceQuery } from "../../../../redux/features/pos-invoice/posInvoiceApi";
import { TextInput } from "react-native";
import Text from "../../../../components/text/Text";
import { showMessage } from "react-native-flash-message";
import * as Clipboard from "expo-clipboard";

const InvoiceLiveUrl = () => {
  const { slug } = useLocalSearchParams();
  //get invoice data
  const {
    data: invoiceData,
    isLoading,
    error,
    isError,
  } = useGetSingleInvoiceQuery(slug);

  //copy to clipboard
  const copyToClipboard = async () => {
    await Clipboard.setStringAsync(invoiceData?.data?.token);
    showMessage({
      message: "Copied to clipboard",
      type: "success",
    });
  };
  return (
    <>
      <Topbar title="Invoice live url" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 100 }}>
        <View style={styles.card}>
          <View style={{ padding: 20 }}>
            <Text style={styles.label} preset="h2_sb">
              Invoice Live URL
            </Text>
            <TextInput
              style={styles.textInput}
              editable={false}
              value={invoiceData?.data?.token || ""}
            />
            <View style={styles.download_back_btn}>
              <Link href="/admin/pos-invoice" asChild>
                <View style={styles.back_btn}>
                  <Text preset="h3" style={styles.buttonText}>
                    Back
                  </Text>
                </View>
              </Link>
              <Pressable onPress={copyToClipboard}>
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.authButton}
                >
                  <Text preset="h3" style={styles.buttonText}>
                    Copy URL
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

export default InvoiceLiveUrl;

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
