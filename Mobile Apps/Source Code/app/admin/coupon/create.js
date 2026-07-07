import React, { useEffect, useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import {
  View,
  ScrollView,
  StyleSheet,
  Pressable,
  TextInput,
} from "react-native";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import Text from "../../../components/text/Text";
import FormSelect from "../../../components/Form/FormSelect";
import FormDate from "../../../components/FormDate/FormDate";
import { colors } from "../../../themes/colors";
import { Link, router } from "expo-router";
import { Image } from "expo-image";
import * as ImagePicker from "expo-image-picker";
import FormRadio from "../../../components/Form/FormRadio";
import { capitalize } from "../../../utils/helper";
import { useAddedCouponMutation } from "../../../redux/features/coupon/couponApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../components/Loading/Loading";

//form validation schema
const schema = yup
  .object({
    title: yup.string().required(),
    code: yup.string().required(),
    status: yup.string().required(),
  })
  .required();

const CouponCreate = () => {
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);
  const [selectedStartDate, setSelectedStartDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [selectedEndDate, setSelectedEndDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [createUpdate, setCreateUpdate] = useState(false);

  const pickImage = async () => {
    // No permissions request is necessary for launching the image library
    let result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.All,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 1,
    });

    var filename = result.assets[0].uri.substring(
      result.assets[0].uri.lastIndexOf("/") + 1,
      result.assets[0].uri.length
    );

    if (!result.canceled) {
      setImage(result.assets[0].uri);
      setFileName(filename);
    }
  };
  //get form data
  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
    setValue,
    watch,
  } = useForm({
    resolver: yupResolver(schema),
  });

  const [
    addedCoupon,
    {
      data: createData,
      isLoading: createIsLoading,
      isSuccess: createIsSuccess,
      isError: createIsError,
      error: createError,
    },
  ] = useAddedCouponMutation();

  const onSubmit = (data, params) => {
    if (params === "create") {
      setCreateUpdate(false);
    } else {
      setCreateUpdate(true);
    }
    const formData = new FormData();

    formData.append("title", data.title);
    formData.append("code", data.code);
    formData.append("discount_type", data.discount_type);
    formData.append("discount", data.discount);
    formData.append("start_date", selectedStartDate);
    formData.append("end_date", selectedEndDate);
    formData.append("minimum_shopping", data.minimum_shopping);
    formData.append("status", data.status);
    if (fileName !== null) {
      formData.append("banner", {
        name: fileName,
        type: "image/*",
        uri: image,
      });
    }
    addedCoupon(formData);
  };

  useEffect(() => {
    if (createIsSuccess) {
      showMessage({
        message: createData.message,
        type: "success",
      });

      if (createUpdate) {
        router.push(`/admin/coupon/products/add/${createData.data.id}`);
      } else {
        router.push("/admin/coupon");
      }
    }
    if (createIsError) {
      showMessage({
        message: createError.data.message,
        type: "danger",
      });
    }
  }, [createIsSuccess, createError, createIsError, createData]);
  return (
    <>
      {createIsLoading && <Loading />}
      <Topbar title="Create Coupon" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Coupon Title <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="title"
              />
              {errors.title && (
                <Text style={styles.validationError}>
                  {capitalize(errors.title?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Coupon Code <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="code"
              />
              {errors.code && (
                <Text style={styles.validationError}>
                  {capitalize(errors.code?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Discount Type
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    items={[
                      { label: "Fixed", value: "fixed" },
                      { label: "Percentage", value: "percentage" },
                    ]}
                    value={value}
                    onChange={onChange}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="discount_type"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Discount
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="discount"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Start Date
              </Text>
              <FormDate setSelectedDate={setSelectedStartDate} />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                End Date
              </Text>
              <FormDate setSelectedDate={setSelectedEndDate} />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Minimum shopping
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="minimum_shopping"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Banner{" "}
                <Text>
                  (Supported type: png, jpg, jpeg | Max size: 300kb |
                  Width:500px, Height:500px)
                </Text>
              </Text>
              <Pressable onPress={pickImage}>
                <Text preset="h3_r" style={styles.chooseFile}>
                  Choose File
                </Text>
              </Pressable>
              {image && (
                <Image
                  source={{ uri: image }}
                  style={{
                    width: "100%",
                    height: 150,
                    borderRadius: 5,
                    marginTop: 10,
                    objectFit: "cover",
                  }}
                />
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Status<Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormRadio
                    onChange={onChange}
                    value={value}
                    items={[
                      {
                        label: "Active",
                        value: "active",
                      },
                      {
                        label: "Inactive",
                        value: "inactive",
                      },
                    ]}
                  />
                )}
                name="status"
              />
              {errors.status && (
                <Text style={styles.validationError}>
                  {capitalize(errors.status?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formActionBtn}>
              <View style={{ flexDirection: "row", gap: 10 }}>
                <Pressable
                  onPress={() =>
                    handleSubmit((data) => onSubmit(data, "create"))()
                  }
                  style={styles.formBtn}
                >
                  <Text preset="h3" style={styles.btnText}>
                    Submit
                  </Text>
                </Pressable>
                <Pressable
                  onPress={() =>
                    handleSubmit((data) => onSubmit(data, "createSetProduct"))()
                  }
                  style={styles.formBtn}
                >
                  <Text preset="h3" style={styles.btnText}>
                    Submit & Set product
                  </Text>
                </Pressable>
              </View>
              <Link href="/admin/coupon">
                <View style={[styles.formBtn, styles.cancelBtn]}>
                  <Text preset="h3" style={styles.btnText}>
                    Cancel
                  </Text>
                </View>
              </Link>
            </View>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default CouponCreate;

const styles = StyleSheet.create({
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
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 10,
  },
  btnText: {
    color: colors.white,
    fontSize: 12,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
  chooseFile: {
    backgroundColor: colors.themeColor,
    color: colors.white,
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 5,
    textAlign: "center",
  },
});
