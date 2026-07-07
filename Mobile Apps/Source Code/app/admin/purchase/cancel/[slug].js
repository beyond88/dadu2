import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import { colors } from "../../../../themes/colors";
import FormDate from "../../../../components/FormDate/FormDate";
import { useCancelPurchaseMutation } from "../../../../redux/features/purchase/purchaseApi";
import Text from "../../../../components/text/Text";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";
import { Link, router, useLocalSearchParams } from "expo-router";

const CancelPurchase = () => {
  const [selectedDate, setSelectedDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [notes, setNotes] = useState("");

  const { slug } = useLocalSearchParams();
  //handle cancel

  const handleSubmit = () => {
    const data = {
      id: slug,
      date: selectedDate,
      note: notes,
    };

    cancelPurchase(data);
  };

  const [
    cancelPurchase,
    {
      data: cancelData,
      isSuccess: cancelIsSuccess,
      isLoading: cancelIsLoading,
      isError: cancelIsError,
      error: cancelError,
    },
  ] = useCancelPurchaseMutation();

  useEffect(() => {
    if (cancelIsSuccess) {
      showMessage({
        message: cancelData.message,
        type: "success",
      });
      router.push("/admin/purchase");
    }
    if (cancelIsError) {
      showMessage({
        message: cancelError?.data?.message,
        type: "danger",
      });
    }
  }, [cancelData, cancelIsSuccess, cancelError, cancelIsError]);
  return (
    <>
      {cancelIsLoading && <Loading />}
      <Topbar title="Cancel Purchase" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 180 }}>
        <View style={styles.card}>
          <View style={styles.formWrap}>
            <View>
              <Text style={styles.label} preset="h2_sb">
                Date<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <FormDate setSelectedDate={setSelectedDate} bg={"gray"} />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Note<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <TextInput
                style={[styles.textInput, styles.notesInput]}
                onChangeText={(text) => setNotes(text)}
                multiline={true}
              />
            </View>
            <View style={styles.formActionBtn}>
              <View style={{ flexDirection: "row", gap: 10 }}>
                <Pressable onPress={handleSubmit} style={styles.formBtn}>
                  <Text preset="h3" style={styles.btnText}>
                    Submit
                  </Text>
                </Pressable>
              </View>
              <Link href="/admin/purchase">
                <View style={[styles.formBtn, styles.cancelBtn]}>
                  <Text preset="h3" style={styles.btnText}>
                    Cancel
                  </Text>
                </View>
              </Link>
            </View>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default CancelPurchase;

const styles = StyleSheet.create({
  invoiceCard: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
  },
  formWrap: {
    backgroundColor: "#fff",
    padding: 20,
    borderRadius: 5,
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    color: colors.black,
    marginBottom: 10,
  },
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  notesInput: {
    height: 80,
    paddingTop: 10,
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 20,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
});
