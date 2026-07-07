import React, { forwardRef, useEffect, useRef, useState } from "react";
import Barcode from "@kichiyaki/react-native-barcode-generator";
import { Dimensions } from "react-native";
import ViewShot from "react-native-view-shot";

const BarCode = forwardRef(({ barcodeData, setBarcodeImage }, prams2) => {
  const ref = useRef();
  useEffect(() => {
    // on mount
    ref.current.capture().then((uri) => {
      setBarcodeImage(uri);
    });
  }, [barcodeData]);
  return (
    <ViewShot
      ref={ref}
      options={{ fileName: "barcode", format: "jpg", quality: 0.9 }}
    >
      <Barcode
        format="CODE128"
        value={barcodeData}
        text={barcodeData}
        maxWidth={Dimensions.get("window").width / 2}
      />
    </ViewShot>
  );
});

export default BarCode;
