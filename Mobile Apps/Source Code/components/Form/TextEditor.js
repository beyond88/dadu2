import React, { useState } from "react";
import { StyleSheet, View } from "react-native";
import { MaterialIcons } from "@expo/vector-icons";
import {
  RichTextEditor,
  RichTextViewer,
  ActionMap,
  ActionKey,
} from "@siposdani87/expo-rich-text-editor";
import { colors } from "../../themes/colors";

const TextEditor = ({ desc, setDesc }) => {
  const getColor = (selected) => {
    return selected ? "red" : "black";
  };

  const getActionMap = () => {
    const size = 24;

    return {
      [ActionKey.undo]: ({ selected }) => (
        <MaterialIcons name="undo" size={size} color={getColor(selected)} />
      ),
      [ActionKey.redo]: ({ selected }) => (
        <MaterialIcons name="redo" size={size} color={getColor(selected)} />
      ),
      [ActionKey.bold]: ({ selected }) => (
        <MaterialIcons
          name="format-bold"
          size={size}
          color={getColor(selected)}
        />
      ),
      [ActionKey.italic]: ({ selected }) => (
        <MaterialIcons
          name="format-italic"
          size={size}
          color={getColor(selected)}
        />
      ),
      [ActionKey.underline]: ({ selected }) => (
        <MaterialIcons
          name="format-underlined"
          size={size}
          color={getColor(selected)}
        />
      ),
      [ActionKey.unorderedList]: ({ selected }) => (
        <MaterialIcons
          name="format-list-bulleted"
          size={size}
          color={getColor(selected)}
        />
      ),
      [ActionKey.orderedList]: ({ selected }) => (
        <MaterialIcons
          name="format-list-numbered"
          size={size}
          color={getColor(selected)}
        />
      ),
      [ActionKey.clear]: ({ selected }) => (
        <MaterialIcons
          name="format-clear"
          size={size}
          color={getColor(selected)}
        />
      ),
      [ActionKey.code]: ({ selected }) => (
        <MaterialIcons name="code" size={size} color={getColor(selected)} />
      ),
    };
  };

  const onValueChange = (v) => {
    setDesc(v);
  };
  return (
    <View>
      <RichTextEditor
        minHeight={150}
        value={desc || ""}
        selectionColor="green"
        actionMap={getActionMap()}
        onValueChange={onValueChange}
        linkStyle={styles.link}
        textStyle={styles.text}
        containerStyle={styles.editor}
        toolbarStyle={styles.toolbar}
      />
    </View>
  );
};

export default TextEditor;

const styles = StyleSheet.create({
  text: {
    // fontFamily: 'Inter_500Medium',
    fontSize: 18,
  },
  link: {
    color: "green",
  },
  viewer: {
    borderColor: "green",
    borderWidth: 1,
    padding: 5,
  },
  editor: {
    backgroundColor: colors.grayBg,
    padding: 5,
  },
  toolbar: {
    // backgroundColor: "#838E9E",
    borderWidth: 1,
    borderColor: "#ddd",
    padding: 6,
    borderTopRightRadius: 5,
    borderTopLeftRadius: 5,
  },
});
