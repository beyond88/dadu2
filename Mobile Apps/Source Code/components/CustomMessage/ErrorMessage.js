import { colors } from "../../themes/colors";
import Text from "../text/Text";

const ErrorMessage = ({ message }) => {
  return (
    <Text
      preset="h2"
      style={{ paddingVertical: 20, textAlign: "center", color: colors.red }}
    >
      {message}
    </Text>
  );
};

export default ErrorMessage;
