import React, { useEffect, useState } from "react";
import { View, TouchableOpacity, Text, StyleSheet } from "react-native";
import { colors } from "../../themes/colors";

const FormRadio = ({ items, onChange, selectedValue, row }) => {
  const [selected, setSelected] = useState(null);

  const handleSelect = (value) => {
    setSelected(value);
    onChange(value);
  };

  useEffect(() => {
    setSelected(selectedValue);
  }, [selectedValue]);
  const additionalContainerStyle = row
    ? { flexDirection: "row", gap: 10 }
    : null;
  const renderRadioOption = (value, label) => (
    <TouchableOpacity
      style={styles.radioOption}
      onPress={() => handleSelect(value)}
      key={value}
    >
      <View style={styles.radio}>
        {selected == value && <View style={styles.radioSelected} />}
      </View>
      <Text style={styles.label}>{label}</Text>
    </TouchableOpacity>
  );

  return (
    <View style={[styles.radioGroup, additionalContainerStyle]}>
      {items.map((item, index) => renderRadioOption(item.value, item.label))}
    </View>
  );
};

export default FormRadio;

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  radioGroup: {
    flexDirection: "column",
  },
  radioOption: {
    flexDirection: "row",
    alignItems: "center",
    marginVertical: 8,
  },
  radio: {
    width: 16,
    height: 16,
    borderWidth: 2,
    borderColor: colors.themeColor,
    borderRadius: 10,
    justifyContent: "center",
    alignItems: "center",
  },
  radioSelected: {
    width: 10,
    height: 10,
    backgroundColor: colors.themeColor,
    borderRadius: 5,
  },
  label: {
    marginLeft: 8,
  },
  flexRow: {
    flexDirection: "row",
  },
});
