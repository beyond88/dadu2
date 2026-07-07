import { useState } from "react";
import { StyleSheet, TouchableOpacity, View } from "react-native";
import Text from "../text/Text";
import { colors } from "../../themes/colors";

const FormCheckbox = ({ toggleCheckbox, checked, label }) => {
  return (
    <TouchableOpacity style={styles.checkboxContainer} onPress={toggleCheckbox}>
      <View style={[styles.checkbox, checked && styles.checkboxCheck]}>
        {checked && <Text style={styles.checkmark}>&#10003;</Text>}
      </View>
      <Text preset="h5" style={styles.label}>
        {label}
      </Text>
    </TouchableOpacity>
  );
};

export default FormCheckbox;
const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  checkboxContainer: {
    flexDirection: "row",
    alignItems: "center",
  },
  checkbox: {
    width: 16,
    height: 16,
    borderWidth: 2,
    borderColor: colors.themeColor,
    borderRadius: 4,
    justifyContent: "center",
    alignItems: "center",
  },
  checkboxCheck: {
    backgroundColor: colors.themeColor,
  },
  checkmark: {
    color: colors.white,
    fontSize: 10,
  },
  label: {
    marginLeft: 8,
    color: colors.black,
  },
});
