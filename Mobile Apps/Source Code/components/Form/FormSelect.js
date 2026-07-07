import { StyleSheet } from "react-native";
import DropDownPicker from "react-native-dropdown-picker";
import { colors } from "../../themes/colors";
import { useEffect, useState } from "react";

const FormSelect = ({
  items,
  placeholder,
  onChange,
  index,
  searchable,
  selectedValue,
  position,
  zIndex,
  zIndexInverse,
  bg,
  height,
}) => {
  const [open, setOpen] = useState(false);
  const [value, setValue] = useState(selectedValue);

  useEffect(() => {
    setValue(selectedValue);
  }, [selectedValue]);
  return (
    <DropDownPicker
      open={open}
      value={value}
      items={items}
      setOpen={setOpen}
      setValue={setValue}
      listMode="SCROLLVIEW"
      placeholder={placeholder}
      onSelectItem={(item, aaa) => {
        onChange(item.value, index);
      }}
      searchable={searchable}
      dropDownDirection={position || "TOP"}
      selectable={true}
      zIndexInverse={zIndexInverse}
      zIndex={zIndex}
      placeholderStyle={{
        color: bg ? colors.black : "#727F8B",
        fontWeight: "regular",
      }}
      style={[
        styles.dropdown,
        {
          backgroundColor: bg || colors.white,
          minHeight: height,
          borderWidth: bg ? 0 : 1,
        },
      ]}
    />
  );
};

export default FormSelect;

const styles = StyleSheet.create({
  dropdown: {
    marginBottom: 15,
    backgroundColor: colors.white,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    borderRadius: 5,
    zIndex: 1000,
  },
});
