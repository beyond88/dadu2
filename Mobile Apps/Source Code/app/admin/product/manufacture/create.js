import React, { useEffect, useState } from "react";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { colors } from "../../../../themes/colors";
import Text from "../../../../components/text/Text";
import FormRadio from "../../../../components/Form/FormRadio";
import { Link, router } from "expo-router";
import { capitalize } from "../../../../utils/helper";
import * as ImagePicker from "expo-image-picker";
import { showMessage } from "react-native-flash-message";
import { Image } from "expo-image";
import Loading from "../../../../components/Loading/Loading";
import { useCreateManufactureMutation } from "../../../../redux/features/manufacture/manufactureApi";

//form validation schema
const schema = yup
  .object({
    name: yup.string().required(),
    status: yup.string().required(),
  })
  .required();

const BrandCreate = () => {
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);

  //create category mutation
  const [
    createManufacture,
    { data: createManufactureData, isLoading, isError, error, isSuccess },
  ] = useCreateManufactureMutation();
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
  const onSubmit = (data) => {
    const formData = new FormData();
    formData.append("name", data.name);
    formData.append("desc", data.desc || "");
    formData.append("status", data.status);
    if (fileName !== null) {
      formData.append("image", {
        uri: image,
        type: "image/jpeg",
        name: fileName,
      });
    }
    createManufacture(formData);
  };

  //pick image
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

  //show error & success message

  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: createManufactureData.message,
        type: "success",
      });
      router.push("/admin/product/manufacture");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [createManufactureData, isSuccess, isError, error]);

  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Create Brand" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Manufacture Name<Text style={{ color: "#ff0000" }}> *</Text>
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
                name="name"
              />
              {errors.name && (
                <Text style={styles.validationError}>
                  {capitalize(errors.name?.message)}
                </Text>
              )}
            </View>

            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Description
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    multiline={true}
                    onChangeText={onChange}
                  />
                )}
                name="desc"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Thumb{" "}
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
                  onPress={handleSubmit(onSubmit)}
                  style={styles.formBtn}
                >
                  <Text preset="h3" style={styles.btnText}>
                    Submit
                  </Text>
                </Pressable>
              </View>
              <Link href="/admin/product/manufacture">
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

export default BrandCreate;

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
    paddingHorizontal: 20,
  },
  btnText: {
    color: colors.white,
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
