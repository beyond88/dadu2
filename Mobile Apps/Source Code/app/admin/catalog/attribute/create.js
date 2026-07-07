import React, { useEffect, useState } from "react";
import {
  Button,
  Modal,
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
import { Link, router } from "expo-router";
import { capitalize, generateUniqueId } from "../../../../utils/helper";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";
import { useCreateAttributeMutation } from "../../../../redux/features/catalog/catalogApi";
import { Entypo } from "@expo/vector-icons";
import { AntDesign } from "@expo/vector-icons";
import ColorPicker, {
  Panel1,
  Swatches,
  Preview,
  OpacitySlider,
  HueSlider,
} from "reanimated-color-picker";
import * as ImagePicker from "expo-image-picker";
import { Image } from "expo-image";
import FormRadio from "../../../../components/Form/FormRadio";

//form validation schema
const schema = yup
  .object({
    name: yup.string().required(),
    status: yup.string().required(),
  })
  .required();

const AttributeCreate = () => {
  const [showModal, setShowModal] = useState(false);
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);
  const [attributeValue, setAttributeValue] = useState([]);
  const [colorIndex, setColorIndex] = useState(null);
  const [addedAttributeItems, setAddedAttributeItems] = useState([
    {
      id: generateUniqueId(),
    },
  ]);

  //pick image
  const pickImage = async (index) => {
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
      //set attribute value & image key in attributeValue state
      const newAttributeValue = [...attributeValue];
      newAttributeValue[index] = {
        ...newAttributeValue[index],
        image: result.assets[0].uri,
      };
      setAttributeValue(newAttributeValue);
    }
  };

  //create category mutation
  const [
    createAttribute,
    { data: createAttributeData, isLoading, isError, error, isSuccess },
  ] = useCreateAttributeMutation();
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
    formData.append("status", data.status);

    const attributeItem = [];
    attributeValue.map((item) => {
      attributeItem.push({
        name: item.name,
        color: item.color || "#333",
      });
    });
    formData.append("item_data", JSON.stringify(attributeItem));
    createAttribute(formData);
  };

  //handle add attribute list

  const handleAddAttributeList = () => {
    setAddedAttributeItems([
      ...addedAttributeItems,
      { id: generateUniqueId() },
    ]);
  };

  const handleDeleteAttributeList = (id, index) => {
    const newAddedAttributeItems = addedAttributeItems.filter(
      (item) => item.id !== id
    );
    const newAttributeValue = attributeValue.filter((item, i) => i !== index);
    setAddedAttributeItems(newAddedAttributeItems);
    setAttributeValue(newAttributeValue);
  };

  const handleAttributeChange = (text, index) => {
    //set attribute value & name key in attributeValue state
    const newAttributeValue = [...attributeValue];
    newAttributeValue[index] = {
      ...newAttributeValue[index],
      name: text,
    };
    setAttributeValue(newAttributeValue);
  };

  const onSelectColor = (hex) => {
    //set attribute value & color key in attributeValue state
    const newAttributeValue = [...attributeValue];
    newAttributeValue[colorIndex] = {
      ...newAttributeValue[colorIndex],
      color: hex,
    };
    setAttributeValue(newAttributeValue);
  };

  //handle show modal

  const handleShowModal = (index) => {
    setShowModal(true);
    setColorIndex(index);
  };

  //show error & success message

  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: createAttributeData.message,
        type: "success",
      });
      router.push("/admin/catalog/attribute");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [createAttributeData, isSuccess, isError, error]);

  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Create Attribute" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Attribute Name<Text style={{ color: "#ff0000" }}> *</Text>
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
              <View
                style={{
                  flexDirection: "row",
                  justifyContent: "space-between",
                  alignItems: "center",
                  marginBottom: 10,
                }}
              >
                <Text
                  style={[styles.label, { marginBottom: "0" }]}
                  preset="h2_sb"
                >
                  Attributes Items
                </Text>
                <Pressable
                  style={styles.addBtn}
                  onPress={handleAddAttributeList}
                >
                  <Entypo name="plus" size={16} color="white" />
                </Pressable>
              </View>
              {addedAttributeItems?.map((item, index) => (
                <View
                  style={{
                    borderColor: colors.border,
                    borderWidth: 1,
                    borderRadius: 5,
                    padding: 10,
                    marginBottom: 10,
                  }}
                  key={index}
                >
                  <View style={styles.formGroup}>
                    <Text style={styles.label} preset="h2_sb">
                      Item Name
                    </Text>
                    <TextInput
                      style={styles.textInput}
                      value={attributeValue[index]?.name || ""}
                      onChangeText={(text) =>
                        handleAttributeChange(text, index)
                      }
                    />
                  </View>
                  <View style={styles.formGroup}>
                    <Text style={styles.label} preset="h2_sb">
                      Color
                    </Text>
                    <Modal visible={showModal} animationType="slide">
                      <ColorPicker
                        style={{ width: "100%", padding: 20 }}
                        value={attributeValue[index]?.color || "#333"}
                        onComplete={({ hex }) => onSelectColor(hex)}
                      >
                        <Preview />
                        <Panel1 />
                        <HueSlider />
                        <OpacitySlider />
                        <Swatches />
                      </ColorPicker>

                      <Button title="Ok" onPress={() => setShowModal(false)} />
                    </Modal>
                    <Pressable
                      onPress={() => handleShowModal(index)}
                      style={[
                        styles.selectBtn,
                        {
                          backgroundColor:
                            attributeValue[index]?.color || "#333",
                        },
                      ]}
                    ></Pressable>
                  </View>

                  <Pressable
                    style={[styles.addBtn, styles.deleteBtn]}
                    onPress={() => handleDeleteAttributeList(item?.id, index)}
                  >
                    <AntDesign name="delete" size={16} color="white" />
                  </Pressable>
                </View>
              ))}
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
              <Link href="/admin/catalog/weight-unit">
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

export default AttributeCreate;

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
  selectBtn: {
    width: "100%",
    height: 40,
    justifyContent: "center",
    alignItems: "center",
    borderWidth: 1,
    borderColor: colors.border,
  },
  addBtn: {
    backgroundColor: colors.themeColor,
    width: 30,
    height: 30,
    borderRadius: 5,
    justifyContent: "center",
    alignItems: "center",
  },
  deleteBtn: {
    backgroundColor: colors.red,
    width: "100%",
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
